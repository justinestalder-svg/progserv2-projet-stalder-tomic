-- SoundMatch - MPD
-- Justine Stalder & Kristina Tomic

CREATE TABLE compte (
    id_compte INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    langue VARCHAR(2) NOT NULL DEFAULT 'fr',
    email_confirme BOOLEAN NOT NULL DEFAULT FALSE,
    date_inscription DATETIME NOT NULL,
    date_acceptation_cgu DATETIME NOT NULL,
    nb_echecs_connexion INT NOT NULL DEFAULT 0,
    bloque_jusqua DATETIME
);

CREATE TABLE jeton_verification (
    id_jeton INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(30) NOT NULL,
    valeur_hash VARCHAR(64) NOT NULL,
    email_cible VARCHAR(255),
    date_creation DATETIME NOT NULL,
    date_expiration DATETIME NOT NULL,
    date_utilisation DATETIME,
    id_compte INT NOT NULL,
    FOREIGN KEY (id_compte) REFERENCES compte(id_compte)
);

CREATE TABLE appareil (
    id_appareil INT AUTO_INCREMENT PRIMARY KEY,
    nom_appareil VARCHAR(100),
    navigateur VARCHAR(100),
    ville_connexion VARCHAR(100),
    derniere_activite DATETIME NOT NULL,
    date_connexion DATETIME NOT NULL,
    date_expiration DATETIME NOT NULL,
    jeton_session_hash VARCHAR(64) NOT NULL,
    id_compte INT NOT NULL,
    FOREIGN KEY (id_compte) REFERENCES compte(id_compte)
);

CREATE TABLE profil (
    id_profil INT AUTO_INCREMENT PRIMARY KEY,
    genre VARCHAR(10) NOT NULL,
    date_naissance DATE NOT NULL,
    photo_profil VARCHAR(255) NOT NULL,
    nom_instagram VARCHAR(30) NOT NULL,
    ville VARCHAR(100),
    id_compte INT NOT NULL UNIQUE,
    FOREIGN KEY (id_compte) REFERENCES compte(id_compte)
);

CREATE TABLE partage_insta (
    id_profil_partageur INT NOT NULL,
    id_profil_destinataire INT NOT NULL,
    date_partage DATETIME NOT NULL,
    PRIMARY KEY (id_profil_partageur, id_profil_destinataire),
    FOREIGN KEY (id_profil_partageur) REFERENCES profil(id_profil),
    FOREIGN KEY (id_profil_destinataire) REFERENCES profil(id_profil)
);

CREATE TABLE genre_musical (
    id_genre INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

CREATE TABLE extrait (
    id_extrait INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    artiste VARCHAR(150) NOT NULL,
    duree_secondes INT NOT NULL,
    url_audio VARCHAR(255) NOT NULL,
    couleur_pochette VARCHAR(7),
    id_genre INT NOT NULL,
    FOREIGN KEY (id_genre) REFERENCES genre_musical(id_genre)
);

CREATE TABLE serie (
    id_serie INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL
);

CREATE TABLE serie_extrait (
    id_serie INT NOT NULL,
    id_extrait INT NOT NULL,
    rang INT NOT NULL,
    PRIMARY KEY (id_serie, id_extrait),
    FOREIGN KEY (id_serie) REFERENCES serie(id_serie),
    FOREIGN KEY (id_extrait) REFERENCES extrait(id_extrait)
);

CREATE TABLE favori (
    id_profil INT NOT NULL,
    id_extrait INT NOT NULL,
    date_ajout DATETIME NOT NULL,
    PRIMARY KEY (id_profil, id_extrait),
    FOREIGN KEY (id_profil) REFERENCES profil(id_profil),
    FOREIGN KEY (id_extrait) REFERENCES extrait(id_extrait)
);

CREATE TABLE session_ecoute (
    id_session INT AUTO_INCREMENT PRIMARY KEY,
    date_debut DATETIME NOT NULL,
    statut VARCHAR(20) NOT NULL,
    rang_courant INT NOT NULL,
    id_profil INT NOT NULL,
    id_serie INT NOT NULL,
    FOREIGN KEY (id_profil) REFERENCES profil(id_profil),
    FOREIGN KEY (id_serie) REFERENCES serie(id_serie)
);

CREATE TABLE reaction (
    id_session INT NOT NULL,
    id_extrait INT NOT NULL,
    choix VARCHAR(10) NOT NULL,
    date_reaction DATETIME NOT NULL,
    PRIMARY KEY (id_session, id_extrait),
    FOREIGN KEY (id_session) REFERENCES session_ecoute(id_session),
    FOREIGN KEY (id_extrait) REFERENCES extrait(id_extrait)
);

CREATE TABLE matchs (
    id_match INT AUTO_INCREMENT PRIMARY KEY,
    score_global INT NOT NULL,
    nb_sons_communs INT NOT NULL,
    date_match DATETIME NOT NULL,
    titre_duo VARCHAR(100),
    texte_analyse TEXT,
    id_profil_1 INT NOT NULL,
    id_profil_2 INT NOT NULL,
    FOREIGN KEY (id_profil_1) REFERENCES profil(id_profil),
    FOREIGN KEY (id_profil_2) REFERENCES profil(id_profil)
);

CREATE TABLE score_genre (
    id_match INT NOT NULL,
    id_genre INT NOT NULL,
    score_genre INT NOT NULL,
    PRIMARY KEY (id_match, id_genre),
    FOREIGN KEY (id_match) REFERENCES matchs(id_match),
    FOREIGN KEY (id_genre) REFERENCES genre_musical(id_genre)
);

CREATE TABLE idee_date (
    id_idee INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    description TEXT,
    id_genre INT NOT NULL,
    FOREIGN KEY (id_genre) REFERENCES genre_musical(id_genre)
);
