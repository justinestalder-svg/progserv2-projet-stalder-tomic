-- =====================================================================
-- SoundMatch – Modèle Physique de Données (MPD) – v2
-- SGBD : MariaDB (InnoDB, utf8mb4)
-- Projet libre ProgServ2 – HEIG-VD – Justine Stalder & Kristina Tomic
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS idee_date;
DROP TABLE IF EXISTS score_genre;
DROP TABLE IF EXISTS participer;      -- supprimée en v2 (remplacée par matchs.id_profil_1/2)
DROP TABLE IF EXISTS matchs;
DROP TABLE IF EXISTS reaction;
DROP TABLE IF EXISTS session_ecoute;
DROP TABLE IF EXISTS favori;
DROP TABLE IF EXISTS extrait;
DROP TABLE IF EXISTS genre_musical;
DROP TABLE IF EXISTS partage_insta;
DROP TABLE IF EXISTS profil;
DROP TABLE IF EXISTS appareil;
DROP TABLE IF EXISTS jeton_verification;
DROP TABLE IF EXISTS compte;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- COMPTE
-- ---------------------------------------------------------------------
CREATE TABLE compte (
    id_compte             INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    pseudo                VARCHAR(20)       NOT NULL,                 -- 3 à 20 caractères (page 1)
    email                 VARCHAR(255)      NOT NULL,
    mot_de_passe_hash     VARCHAR(255)      NOT NULL,                 -- password_hash()
    langue                ENUM('fr','en')   NOT NULL DEFAULT 'fr',
    email_confirme        BOOLEAN           NOT NULL DEFAULT FALSE,
    date_inscription      DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_acceptation_cgu  DATETIME          NOT NULL,
    nb_echecs_connexion   TINYINT UNSIGNED  NOT NULL DEFAULT 0,       -- blocage après 5 échecs
    bloque_jusqua         DATETIME          NULL,                     -- + 15 minutes
    PRIMARY KEY (id_compte),
    UNIQUE KEY uq_compte_pseudo (pseudo),
    UNIQUE KEY uq_compte_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- JETON_VERIFICATION  (Recevoir : Jeton 1,1 — Compte 0,n)
-- confirmation_email : 24 h | reinitialisation_mdp : 1 h
-- changement_email : la nouvelle adresse est gardée dans email_cible
-- jusqu'à la confirmation (l'ancienne reste active entre-temps, page 7b)
-- ---------------------------------------------------------------------
CREATE TABLE jeton_verification (
    id_jeton          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    id_compte         INT UNSIGNED  NOT NULL,
    type              ENUM('confirmation_email','reinitialisation_mdp','changement_email') NOT NULL,
    valeur_hash       CHAR(64)      NOT NULL,      -- hash SHA-256 du jeton envoyé par mail
    email_cible       VARCHAR(255)  NULL,          -- seulement pour changement_email
    date_creation     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- sert aussi au délai « Renvoyer le mail » (2 min)
    date_expiration   DATETIME      NOT NULL,
    date_utilisation  DATETIME      NULL,
    PRIMARY KEY (id_jeton),
    UNIQUE KEY uq_jeton_valeur (valeur_hash),
    CONSTRAINT fk_jeton_compte FOREIGN KEY (id_compte)
        REFERENCES compte (id_compte) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- APPAREIL  (Se_connecter : Appareil 1,1 — Compte 0,n)
-- Une ligne = un appareil connecté (30 jours). Déconnecter = supprimer la ligne.
-- ---------------------------------------------------------------------
CREATE TABLE appareil (
    id_appareil         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    id_compte           INT UNSIGNED  NOT NULL,
    nom_appareil        VARCHAR(100)  NULL,
    navigateur          VARCHAR(100)  NULL,
    ville_connexion     VARCHAR(100)  NULL,
    derniere_activite   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_connexion      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_expiration     DATETIME      NOT NULL,
    jeton_session_hash  CHAR(64)      NOT NULL,
    PRIMARY KEY (id_appareil),
    UNIQUE KEY uq_appareil_jeton (jeton_session_hash),
    CONSTRAINT fk_appareil_compte FOREIGN KEY (id_compte)
        REFERENCES compte (id_compte) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- PROFIL  (Posseder : Profil 1,1 — Compte 0,1  → FK unique)
-- Créé à la fin des questions obligatoires (6b à 6e) : genre, date de
-- naissance, photo et Instagram sont donc tous obligatoires.
-- ---------------------------------------------------------------------
CREATE TABLE profil (
    id_profil       INT UNSIGNED                    NOT NULL AUTO_INCREMENT,
    id_compte       INT UNSIGNED                    NOT NULL,
    genre           ENUM('femme','homme','autre')   NOT NULL,
    date_naissance  DATE                            NOT NULL,   -- 18 ans minimum (contrôlé en PHP)
    photo_profil    VARCHAR(255)                    NOT NULL,   -- chemin du fichier JPG/PNG
    nom_instagram   VARCHAR(30)                     NOT NULL,   -- sans le « @ »
    ville           VARCHAR(100)                    NULL,
    PRIMARY KEY (id_profil),
    UNIQUE KEY uq_profil_compte (id_compte),
    CONSTRAINT fk_profil_compte FOREIGN KEY (id_compte)
        REFERENCES compte (id_compte) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- PARTAGE_INSTA  (Partager_insta : Profil 0,n partageur — Profil 0,n destinataire)
-- « Ne plus partager » = supprimer la ligne.
-- « Échange complet » = les deux lignes (A→B et B→A) existent.
-- ---------------------------------------------------------------------
CREATE TABLE partage_insta (
    id_profil_partageur     INT UNSIGNED  NOT NULL,
    id_profil_destinataire  INT UNSIGNED  NOT NULL,
    date_partage            DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_profil_partageur, id_profil_destinataire),
    CONSTRAINT fk_partage_partageur FOREIGN KEY (id_profil_partageur)
        REFERENCES profil (id_profil) ON DELETE CASCADE,
    CONSTRAINT fk_partage_destinataire FOREIGN KEY (id_profil_destinataire)
        REFERENCES profil (id_profil) ON DELETE CASCADE,
    CONSTRAINT ck_partage_different CHECK (id_profil_partageur <> id_profil_destinataire)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- GENRE_MUSICAL
-- ---------------------------------------------------------------------
CREATE TABLE genre_musical (
    id_genre  INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    libelle   VARCHAR(50)   NOT NULL,
    PRIMARY KEY (id_genre),
    UNIQUE KEY uq_genre_libelle (libelle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- EXTRAIT  (Appartenir_a : Extrait 1,1 — Genre 0,n)
-- ---------------------------------------------------------------------
CREATE TABLE extrait (
    id_extrait        INT UNSIGNED       NOT NULL AUTO_INCREMENT,
    id_genre          INT UNSIGNED       NOT NULL,
    titre             VARCHAR(150)       NOT NULL,
    artiste           VARCHAR(150)       NOT NULL,
    duree_secondes    SMALLINT UNSIGNED  NOT NULL DEFAULT 30,
    url_audio         VARCHAR(255)       NOT NULL,
    couleur_pochette  CHAR(7)            NULL,     -- ex. '#B85434'
    PRIMARY KEY (id_extrait),
    CONSTRAINT fk_extrait_genre FOREIGN KEY (id_genre)
        REFERENCES genre_musical (id_genre) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- FAVORI  (Mettre_en_favori : Profil 0,n — Extrait 0,n)
-- ---------------------------------------------------------------------
CREATE TABLE favori (
    id_profil   INT UNSIGNED  NOT NULL,
    id_extrait  INT UNSIGNED  NOT NULL,
    date_ajout  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_profil, id_extrait),
    CONSTRAINT fk_favori_profil FOREIGN KEY (id_profil)
        REFERENCES profil (id_profil) ON DELETE CASCADE,
    CONSTRAINT fk_favori_extrait FOREIGN KEY (id_extrait)
        REFERENCES extrait (id_extrait) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- SESSION_ECOUTE  (Lancer : Session 1,1 — Profil 0,n)
-- statut : en_cours | enregistree (« continuer après », page 8b) | terminee
-- « Quitter définitivement » = supprimer la session (les réactions suivent).
-- ---------------------------------------------------------------------
CREATE TABLE session_ecoute (
    id_session    INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    id_profil     INT UNSIGNED      NOT NULL,
    date_debut    DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut        ENUM('en_cours','enregistree','terminee') NOT NULL DEFAULT 'en_cours',
    rang_courant  TINYINT UNSIGNED  NOT NULL DEFAULT 1,     -- extrait en cours (1 à 20)
    nb_extraits   TINYINT UNSIGNED  NOT NULL DEFAULT 20,
    PRIMARY KEY (id_session),
    CONSTRAINT fk_session_profil FOREIGN KEY (id_profil)
        REFERENCES profil (id_profil) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- REACTION  (Reagir : Extrait 0,n — Session 0,n)
-- Au lancement d'une session, on insère les 20 extraits tirés (4 genres × 5)
-- avec choix = NULL. On remplit choix au clic J'aime / J'ignore.
-- → permet de reprendre une session enregistrée avec les mêmes extraits.
-- « Revenir au son précédent » = remettre choix à NULL.
-- Pour le calcul des matchs, on prend la réaction la plus récente
-- de chaque extrait (une réécoute remplace l'ancienne réponse).
-- ---------------------------------------------------------------------
CREATE TABLE reaction (
    id_session     INT UNSIGNED             NOT NULL,
    id_extrait     INT UNSIGNED             NOT NULL,
    rang           TINYINT UNSIGNED         NOT NULL,   -- position 1 à 20 dans la session
    choix          ENUM('aime','ignore')    NULL,       -- NULL = pas encore répondu
    date_reaction  DATETIME                 NULL,
    PRIMARY KEY (id_session, id_extrait),
    UNIQUE KEY uq_reaction_rang (id_session, rang),
    CONSTRAINT fk_reaction_session FOREIGN KEY (id_session)
        REFERENCES session_ecoute (id_session) ON DELETE CASCADE,
    CONSTRAINT fk_reaction_extrait FOREIGN KEY (id_extrait)
        REFERENCES extrait (id_extrait) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- MATCHS  (Match entre 2 profils — recalculé après chaque session)
-- Une seule ligne par paire de profils : id_profil_1 < id_profil_2.
-- « MATCH » est un mot réservé de MariaDB, d'où le nom « matchs ».
-- ---------------------------------------------------------------------
CREATE TABLE matchs (
    id_match         INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    id_profil_1      INT UNSIGNED      NOT NULL,
    id_profil_2      INT UNSIGNED      NOT NULL,
    score_global     TINYINT UNSIGNED  NOT NULL,     -- compatibilité 0 à 100
    nb_sons_communs  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    date_match       DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    titre_duo        VARCHAR(100)      NULL,
    texte_analyse    TEXT              NULL,
    PRIMARY KEY (id_match),
    UNIQUE KEY uq_match_paire (id_profil_1, id_profil_2),
    KEY idx_match_profil_2 (id_profil_2),
    CONSTRAINT fk_match_profil_1 FOREIGN KEY (id_profil_1)
        REFERENCES profil (id_profil) ON DELETE CASCADE,
    CONSTRAINT fk_match_profil_2 FOREIGN KEY (id_profil_2)
        REFERENCES profil (id_profil) ON DELETE CASCADE,
    CONSTRAINT ck_match_ordre CHECK (id_profil_1 < id_profil_2),
    CONSTRAINT ck_match_score CHECK (score_global BETWEEN 0 AND 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- SCORE_GENRE  (Scorer_genre : Match 0,n — Genre 0,n)
-- ---------------------------------------------------------------------
CREATE TABLE score_genre (
    id_match     INT UNSIGNED      NOT NULL,
    id_genre     INT UNSIGNED      NOT NULL,
    score_genre  TINYINT UNSIGNED  NOT NULL,         -- 0 à 100
    PRIMARY KEY (id_match, id_genre),
    CONSTRAINT fk_score_match FOREIGN KEY (id_match)
        REFERENCES matchs (id_match) ON DELETE CASCADE,
    CONSTRAINT fk_score_genre FOREIGN KEY (id_genre)
        REFERENCES genre_musical (id_genre) ON DELETE CASCADE,
    CONSTRAINT ck_score_genre CHECK (score_genre BETWEEN 0 AND 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- IDEE_DATE  (Inspirer : Idee_date 1,1 — Genre 0,n)
-- ---------------------------------------------------------------------
CREATE TABLE idee_date (
    id_idee      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    id_genre     INT UNSIGNED  NOT NULL,
    titre        VARCHAR(100)  NOT NULL,
    description  TEXT          NULL,
    PRIMARY KEY (id_idee),
    CONSTRAINT fk_idee_genre FOREIGN KEY (id_genre)
        REFERENCES genre_musical (id_genre) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- DONNÉES DE DÉPART : les 4 genres (choix encore ouvert, section 9)
-- ---------------------------------------------------------------------
INSERT INTO genre_musical (libelle) VALUES
    ('Pop'), ('Rap'), ('Électro'), ('Rock');
