-- SoundMatch : données de départ (genres, 20 extraits, 1 série)
-- À exécuter UNE SEULE FOIS, après soundmatch_mpd.sql (dans phpMyAdmin : onglet SQL).
-- Les musiques sont des sons de test générés pour le projet (libres de droits).
-- On n'écrit jamais d'id à la main : on retrouve les id avec des sous-requêtes.

-- 1. Les 4 genres (on n'ajoute que ceux qui n'existent pas encore)
INSERT INTO genre_musical (libelle) SELECT 'Pop' FROM DUAL
    WHERE NOT EXISTS (SELECT 1 FROM genre_musical WHERE libelle = 'Pop');
INSERT INTO genre_musical (libelle) SELECT 'Rap' FROM DUAL
    WHERE NOT EXISTS (SELECT 1 FROM genre_musical WHERE libelle = 'Rap');
INSERT INTO genre_musical (libelle) SELECT 'Électro' FROM DUAL
    WHERE NOT EXISTS (SELECT 1 FROM genre_musical WHERE libelle = 'Électro');
INSERT INTO genre_musical (libelle) SELECT 'Rock' FROM DUAL
    WHERE NOT EXISTS (SELECT 1 FROM genre_musical WHERE libelle = 'Rock');

-- 2. Les 20 extraits (5 par genre)
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Soleil de minuit', 'Lumi', 30, 'audio/pop-1.mp3', '#F2B5A0', (SELECT id_genre FROM genre_musical WHERE libelle = 'Pop'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Ballade en ville', 'Nova Rose', 30, 'audio/pop-2.mp3', '#E8A0B4', (SELECT id_genre FROM genre_musical WHERE libelle = 'Pop'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Coeur en papier', 'Les Étoiles', 30, 'audio/pop-3.mp3', '#F7C59F', (SELECT id_genre FROM genre_musical WHERE libelle = 'Pop'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Été sans fin', 'Lumi', 30, 'audio/pop-4.mp3', '#FFD6A5', (SELECT id_genre FROM genre_musical WHERE libelle = 'Pop'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Danse au balcon', 'Clara Sun', 30, 'audio/pop-5.mp3', '#F4A6A6', (SELECT id_genre FROM genre_musical WHERE libelle = 'Pop'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Bitume', 'MC Lac', 30, 'audio/rap-1.mp3', '#3D3D3D', (SELECT id_genre FROM genre_musical WHERE libelle = 'Rap'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Quartier libre', 'Kenzo B', 30, 'audio/rap-2.mp3', '#5A4A42', (SELECT id_genre FROM genre_musical WHERE libelle = 'Rap'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Tard le soir', 'Shana', 30, 'audio/rap-3.mp3', '#2B2521', (SELECT id_genre FROM genre_musical WHERE libelle = 'Rap'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Rimes et rails', 'MC Lac', 30, 'audio/rap-4.mp3', '#6B6159', (SELECT id_genre FROM genre_musical WHERE libelle = 'Rap'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Plus haut', 'Kenzo B', 30, 'audio/rap-5.mp3', '#4A3B35', (SELECT id_genre FROM genre_musical WHERE libelle = 'Rap'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Néon', 'Pixel Wave', 30, 'audio/electro-1.mp3', '#7B61FF', (SELECT id_genre FROM genre_musical WHERE libelle = 'Électro'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Nuit blanche', 'Synthia', 30, 'audio/electro-2.mp3', '#4FC3F7', (SELECT id_genre FROM genre_musical WHERE libelle = 'Électro'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Pulsation', 'Pixel Wave', 30, 'audio/electro-3.mp3', '#00BFA6', (SELECT id_genre FROM genre_musical WHERE libelle = 'Électro'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Orbite', 'Vortex', 30, 'audio/electro-4.mp3', '#536DFE', (SELECT id_genre FROM genre_musical WHERE libelle = 'Électro'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Lumière bleue', 'Synthia', 30, 'audio/electro-5.mp3', '#29B6F6', (SELECT id_genre FROM genre_musical WHERE libelle = 'Électro'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Route 66', 'The Pebbles', 30, 'audio/rock-1.mp3', '#B85434', (SELECT id_genre FROM genre_musical WHERE libelle = 'Rock'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Orage', 'Les Falaises', 30, 'audio/rock-2.mp3', '#9C4429', (SELECT id_genre FROM genre_musical WHERE libelle = 'Rock'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Guitare rouge', 'The Pebbles', 30, 'audio/rock-3.mp3', '#A63A22', (SELECT id_genre FROM genre_musical WHERE libelle = 'Rock'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Liberté', 'Volt', 30, 'audio/rock-4.mp3', '#8D3B2A', (SELECT id_genre FROM genre_musical WHERE libelle = 'Rock'));
INSERT INTO extrait (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
    VALUES ('Dernier concert', 'Les Falaises', 30, 'audio/rock-5.mp3', '#C0623F', (SELECT id_genre FROM genre_musical WHERE libelle = 'Rock'));

-- 3. La série 1 et ses 20 extraits, dans un ordre mélangé (rang 1 à 20)
INSERT INTO serie (nom) VALUES ('Série 1');

INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/pop-1.mp3'), 1);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/rap-1.mp3'), 2);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/electro-1.mp3'), 3);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/rock-1.mp3'), 4);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/pop-2.mp3'), 5);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/rap-2.mp3'), 6);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/electro-2.mp3'), 7);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/rock-2.mp3'), 8);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/pop-3.mp3'), 9);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/rap-3.mp3'), 10);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/electro-3.mp3'), 11);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/rock-3.mp3'), 12);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/pop-4.mp3'), 13);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/rap-4.mp3'), 14);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/electro-4.mp3'), 15);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/rock-4.mp3'), 16);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/pop-5.mp3'), 17);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/rap-5.mp3'), 18);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/electro-5.mp3'), 19);
INSERT INTO serie_extrait (id_serie, id_extrait, rang)
    VALUES ((SELECT id_serie FROM serie WHERE nom = 'Série 1'), (SELECT id_extrait FROM extrait WHERE url_audio = 'audio/rock-5.mp3'), 20);
