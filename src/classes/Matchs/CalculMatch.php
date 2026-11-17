<?php

namespace Matchs;

use Database;

// Calcule la compatibilité entre les profils (voir le cahier des charges, section 2)
// et enregistre les matchs avec MatchDuoManager.
class CalculMatch {
    private Database $database;
    private MatchDuoManager $matchManager;

    public function __construct() {
        $this->database = new Database();
        $this->matchManager = new MatchDuoManager();
    }

    // Les réponses d'un profil, sur toutes ses sessions terminées.
    // Renvoie [id de l'extrait => ['choix' => 'aime' ou 'ignore', 'id_genre' => ...]]
    // Si un extrait a été écouté plusieurs fois, on garde la réponse la plus récente.
    public function getReponses(int $idProfil): array {
        $sql = "SELECT reaction.id_extrait, reaction.choix, extrait.id_genre
                FROM reaction
                JOIN session_ecoute ON session_ecoute.id_session = reaction.id_session
                JOIN extrait ON extrait.id_extrait = reaction.id_extrait
                WHERE session_ecoute.id_profil = :id_profil
                AND session_ecoute.statut = 'terminee'
                ORDER BY reaction.date_reaction";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_profil', $idProfil, \PDO::PARAM_INT);
        $stmt->execute();

        $reponses = [];

        // Les lignes sont triées par date : une réponse plus récente remplace l'ancienne
        foreach ($stmt->fetchAll() as $ligne) {
            $reponses[(int) $ligne['id_extrait']] = [
                'choix' => $ligne['choix'],
                'id_genre' => (int) $ligne['id_genre'],
            ];
        }

        return $reponses;
    }

    // Les profils qui ont terminé au moins une session
    public function getIdsProfilsAvecSession(): array {
        $sql = "SELECT DISTINCT id_profil FROM session_ecoute WHERE statut = 'terminee'";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->execute();

        $ids = [];
        foreach ($stmt->fetchAll() as $ligne) {
            $ids[] = (int) $ligne['id_profil'];
        }

        return $ids;
    }

    // Le taux d'appréciation par genre : [id du genre => nombre de J'aime ÷ nombre d'extraits écoutés]
    private function calculerTaux(array $reponses): array {
        $ecoutes = [];
        $aimes = [];

        foreach ($reponses as $reponse) {
            $idGenre = $reponse['id_genre'];

            if (!isset($ecoutes[$idGenre])) {
                $ecoutes[$idGenre] = 0;
                $aimes[$idGenre] = 0;
            }

            $ecoutes[$idGenre]++;

            if ($reponse['choix'] === 'aime') {
                $aimes[$idGenre]++;
            }
        }

        $taux = [];
        foreach ($ecoutes as $idGenre => $nombre) {
            $taux[$idGenre] = $aimes[$idGenre] / $nombre;
        }

        return $taux;
    }

    // Recalcule tous les matchs d'un profil (à appeler à la fin d'une session)
    public function mettreAJourMatchs(int $idProfil): void {
        $mesReponses = $this->getReponses($idProfil);

        if (empty($mesReponses)) {
            return;
        }

        $mesTaux = $this->calculerTaux($mesReponses);

        foreach ($this->getIdsProfilsAvecSession() as $idAutreProfil) {
            // On ne matche pas avec soi-même
            if ($idAutreProfil === $idProfil) {
                continue;
            }

            $autresReponses = $this->getReponses($idAutreProfil);
            $autresTaux = $this->calculerTaux($autresReponses);

            // 1. Score de chaque genre : 100 − (écart entre les deux taux × 100)
            $scoresGenre = [];
            foreach ($mesTaux as $idGenre => $taux) {
                if (isset($autresTaux[$idGenre])) {
                    $scoresGenre[$idGenre] = (int) round(100 - abs($taux - $autresTaux[$idGenre]) * 100);
                }
            }

            if (empty($scoresGenre)) {
                continue;
            }

            // 2. Compatibilité = moyenne des scores des genres, arrondie
            $total = 0;
            foreach ($scoresGenre as $score) {
                $total += $score;
            }
            $scoreGlobal = (int) round($total / count($scoresGenre));

            // 3. Sons aimés en commun : extraits que les deux ont aimés
            $nbSonsCommuns = 0;
            foreach ($mesReponses as $idExtrait => $reponse) {
                if (
                    $reponse['choix'] === 'aime'
                    && isset($autresReponses[$idExtrait])
                    && $autresReponses[$idExtrait]['choix'] === 'aime'
                ) {
                    $nbSonsCommuns++;
                }
            }

            // 4. On crée le match, ou on met à jour celui qui existe déjà
            $matchExistant = $this->matchManager->getMatchByProfils($idProfil, $idAutreProfil);

            $idMatch = null;
            if ($matchExistant !== null) {
                $idMatch = $matchExistant->getId();
            }

            $match = new MatchDuo($idMatch, $scoreGlobal, $nbSonsCommuns, new \DateTime(), null, null, $idProfil, $idAutreProfil);

            foreach ($scoresGenre as $idGenre => $score) {
                $match->setScoreGenre($idGenre, $score);
            }

            if ($matchExistant === null) {
                $this->matchManager->addMatch($match);
            } else {
                $this->matchManager->updateMatch($match);
            }
        }
    }
}
