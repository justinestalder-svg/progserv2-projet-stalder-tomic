-- =====================================================================
-- SoundMatch – Modèle Physique de Données (MPD)
-- SGBD : MariaDB (InnoDB, utf8mb4)
-- Projet libre ProgServ2 – HEIG-VD – Justine Stalder & Kristina Tomic
-- =====================================================================

-- À exécuter dans la base my_database (phpMyAdmin > my_database > SQL)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS idee_date;
DROP TABLE IF EXISTS score_genre;
DROP TABLE IF EXISTS participer;
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
    pseudo                VARCHAR(50)       NOT NULL,
    email                 VARCHAR(255)      NOT NULL,
    mot_de_passe_hash     VARCHAR(255)      NOT NULL,
    langue                CHAR(2)           NOT NULL DEFAULT 'fr',
    email_confirme        BOOLEAN           NOT NULL DEFAULT FALSE,
    date_inscription      DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_acceptation_cgu  DATETIME          NOT NULL,
    nb_echecs_connexion   TINYINT UNSIGNED  NOT NULL DEFAULT 0,
    bloque_jusqua         DATETIME          NULL,
    PRIMARY KEY (id_compte),
    UNIQUE KEY uq_compte_pseudo (pseudo),
    UNIQUE KEY uq_compte_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- JETON_VERIFICATION  (Recevoir : Jeton 1,1 — Compte 0,n)
-- ---------------------------------------------------------------------
CREATE TABLE jeton_verification (
    id_jeton          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    id_compte         INT UNSIGNED  NOT NULL,
    type              VARCHAR(30)   NOT NULL,      -- ex. 'confirmation_email', 'reinitialisation_mdp'
    valeur_hash       CHAR(64)      NOT NULL,      -- hash SHA-256 du jeton
    date_creation     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_expiration   DATETIME      NOT NULL,
    date_utilisation  DATETIME      NULL,
    PRIMARY KEY (id_jeton),
    UNIQUE KEY uq_jeton_valeur (valeur_hash),
    CONSTRAINT fk_jeton_compte FOREIGN KEY (id_compte)
        REFERENCES compte (id_compte) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- APPAREIL  (Se_connecter : Appareil 1,1 — Compte 0,n)
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
-- ---------------------------------------------------------------------
CREATE TABLE profil (
    id_profil       INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    id_compte       INT UNSIGNED  NOT NULL,
    genre           VARCHAR(20)   NULL,
    date_naissance  DATE          NOT NULL,
    photo_profil    VARCHAR(255)  NULL,          -- chemin du fichier
    nom_instagram   VARCHAR(30)   NULL,
    ville           VARCHAR(100)  NULL,
    PRIMARY KEY (id_profil),
    UNIQUE KEY uq_profil_compte (id_compte),
    CONSTRAINT fk_profil_compte FOREIGN KEY (id_compte)
        REFERENCES compte (id_compte) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- PARTAGE_INSTA  (Partager_insta : Profil 0,n partageur — Profil 0,n destinataire)
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
    duree_secondes    SMALLINT UNSIGNED  NOT NULL,
    url_audio         VARCHAR(255)       NOT NULL,
    couleur_pochette  CHAR(7)            NULL,     -- ex. '#FF66AA'
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
-- ---------------------------------------------------------------------
CREATE TABLE session_ecoute (
    id_session    INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    id_profil     INT UNSIGNED      NOT NULL,
    date_debut    DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut        VARCHAR(20)       NOT NULL DEFAULT 'en_cours',  -- ex. 'en_cours', 'terminee'
    rang_courant  TINYINT UNSIGNED  NOT NULL DEFAULT 0,
    nb_extraits   TINYINT UNSIGNED  NOT NULL,
    PRIMARY KEY (id_session),
    CONSTRAINT fk_session_profil FOREIGN KEY (id_profil)
        REFERENCES profil (id_profil) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- REACTION  (Reagir : Extrait 0,n — Session 0,n)
-- ---------------------------------------------------------------------
CREATE TABLE reaction (
    id_session     INT UNSIGNED      NOT NULL,
    id_extrait     INT UNSIGNED      NOT NULL,
    rang           TINYINT UNSIGNED  NOT NULL,
    choix          VARCHAR(20)       NOT NULL,     -- ex. 'like', 'dislike'
    date_reaction  DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_session, id_extrait),
    UNIQUE KEY uq_reaction_rang (id_session, rang),
    CONSTRAINT fk_reaction_session FOREIGN KEY (id_session)
        REFERENCES session_ecoute (id_session) ON DELETE CASCADE,
    CONSTRAINT fk_reaction_extrait FOREIGN KEY (id_extrait)
        REFERENCES extrait (id_extrait) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- MATCHS  (Generer : Match 1,1 — Session 0,n)
-- « MATCH » est un mot réservé de MariaDB, d'où le nom « matchs ».
-- ---------------------------------------------------------------------
CREATE TABLE matchs (
    id_match         INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    id_session       INT UNSIGNED      NOT NULL,
    score_global     TINYINT UNSIGNED  NOT NULL,     -- 0 à 100
    nb_sons_communs  TINYINT UNSIGNED  NOT NULL DEFAULT 0,
    date_match       DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    titre_duo        VARCHAR(100)      NULL,
    texte_analyse    TEXT              NULL,
    PRIMARY KEY (id_match),
    CONSTRAINT fk_match_session FOREIGN KEY (id_session)
        REFERENCES session_ecoute (id_session) ON DELETE CASCADE,
    CONSTRAINT ck_match_score CHECK (score_global BETWEEN 0 AND 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- PARTICIPER  (Participer : Match 2,2 — Profil 0,n)
-- Le « exactement 2 profils par match » est contrôlé côté PHP.
-- ---------------------------------------------------------------------
CREATE TABLE participer (
    id_match   INT UNSIGNED  NOT NULL,
    id_profil  INT UNSIGNED  NOT NULL,
    PRIMARY KEY (id_match, id_profil),
    CONSTRAINT fk_participer_match FOREIGN KEY (id_match)
        REFERENCES matchs (id_match) ON DELETE CASCADE,
    CONSTRAINT fk_participer_profil FOREIGN KEY (id_profil)
        REFERENCES profil (id_profil) ON DELETE CASCADE
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
