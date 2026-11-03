<?php

namespace Matchs;

// La classe s'appelle MatchDuo car « match » est un mot réservé en PHP
class MatchDuo {
    // Propriétés privées (encapsulation), une par colonne de la table `matchs`
    private ?int $id;
    private int $scoreGlobal;
    private int $nbSonsCommuns;
    private \DateTime $dateMatch;
    private ?string $titreDuo;
    private ?string $texteAnalyse;
    private int $idProfil1;
    private int $idProfil2;

    // Les scores par genre (table `score_genre`) : [id_genre => score]
    private array $scoresGenre = [];

    public function __construct(
        ?int $id,
        int $scoreGlobal,
        int $nbSonsCommuns,
        \DateTime $dateMatch,
        ?string $titreDuo,
        ?string $texteAnalyse,
        int $idProfilA,
        int $idProfilB
    ) {
        // Un match relie deux profils différents
        if ($idProfilA === $idProfilB) {
            throw new \InvalidArgumentException("Un profil ne peut pas matcher avec lui-même.");
        }

        $this->id = $id;
        $this->setScoreGlobal($scoreGlobal);
        $this->setNbSonsCommuns($nbSonsCommuns);
        $this->dateMatch = $dateMatch;
        $this->setTitreDuo($titreDuo);
        $this->texteAnalyse = $texteAnalyse;

        // On met toujours le plus petit id en premier : ainsi la paire (5, 8)
        // et la paire (8, 5) sont enregistrées de la même façon, et on évite les doublons
        $this->idProfil1 = min($idProfilA, $idProfilB);
        $this->idProfil2 = max($idProfilA, $idProfilB);
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    public function getScoreGlobal(): int {
        return $this->scoreGlobal;
    }
    public function getNbSonsCommuns(): int {
        return $this->nbSonsCommuns;
    }
    public function getDateMatch(): \DateTime {
        return $this->dateMatch;
    }
    public function getTitreDuo(): ?string {
        return $this->titreDuo;
    }
    public function getTexteAnalyse(): ?string {
        return $this->texteAnalyse;
    }
    public function getIdProfil1(): int {
        return $this->idProfil1;
    }
    public function getIdProfil2(): int {
        return $this->idProfil2;
    }
    public function getScoresGenre(): array {
        return $this->scoresGenre;
    }

    // Depuis le profil de la personne connectée, renvoie l'id de l'autre personne du duo
    public function getIdAutreProfil(int $idProfil): int {
        return $idProfil === $this->idProfil1 ? $this->idProfil2 : $this->idProfil1;
    }

    // Le genre où le duo a le meilleur score (pour proposer une idée de date)
    public function getIdMeilleurGenre(): ?int {
        if (empty($this->scoresGenre)) {
            return null;
        }

        return array_search(max($this->scoresGenre), $this->scoresGenre);
    }

    // Setters, avec vérification des données
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setScoreGlobal(int $scoreGlobal): void {
        if ($scoreGlobal < 0 || $scoreGlobal > 100) {
            throw new \InvalidArgumentException("Le score global doit être entre 0 et 100.");
        }

        $this->scoreGlobal = $scoreGlobal;
    }

    public function setNbSonsCommuns(int $nbSonsCommuns): void {
        if ($nbSonsCommuns < 0) {
            throw new \InvalidArgumentException("Le nombre de sons en commun ne peut pas être négatif.");
        }

        $this->nbSonsCommuns = $nbSonsCommuns;
    }

    public function setTitreDuo(?string $titreDuo): void {
        if ($titreDuo !== null && mb_strlen($titreDuo) > 100) {
            throw new \InvalidArgumentException("Le titre du duo ne doit pas dépasser 100 caractères.");
        }

        $this->titreDuo = $titreDuo;
    }

    public function setTexteAnalyse(?string $texteAnalyse): void {
        $this->texteAnalyse = $texteAnalyse;
    }

    // Ajoute ou modifie le score d'un genre musical pour ce duo
    public function setScoreGenre(int $idGenre, int $score): void {
        if ($score < 0 || $score > 100) {
            throw new \InvalidArgumentException("Le score d'un genre doit être entre 0 et 100.");
        }

        $this->scoresGenre[$idGenre] = $score;
    }
}
