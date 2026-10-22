# SoundMatch

Application web de rencontre basée sur les goûts musicaux.
Projet libre réalisé dans le cadre du cours ProgServ2 à la HEIG-VD.

**Équipe :** Justine Stalder & Kristina Tomic

**Site en ligne :** https://soundmatch.laponie-suedoise.ch

## Documents du projet

- Cahier des charges : [cahier_de_charges.md](cahier_de_charges.md)
- Maquettes : [docs/maquettes](docs/maquettes)
- Modèle physique de données (MPD) : [sql/soundmatch_mpd.sql](sql/soundmatch_mpd.sql)

## Lancer le projet en local

Prérequis : Docker Desktop et VS Code avec l'extension Dev Containers.

1. Cloner le dépôt et l'ouvrir dans VS Code, puis choisir « Reopen in Container ».
2. Démarrer les services :
```bash
   docker compose up -d
```
3. Copier le fichier de configuration :
```bash
   cp src/config/database.example.ini src/config/database.ini
```
4. Ouvrir phpMyAdmin (http://localhost:9090, utilisateur `username`, mot de passe `password`), sélectionner `my_database` et exécuter le contenu de `sql/soundmatch_mpd.sql` dans l'onglet SQL.
5. Ouvrir http://localhost:8080/src/public/

## Déploiement sur Infomaniak

1. **Site** : dans le Manager Infomaniak, ajouter un site « projet vierge » Apache/PHP sur le sous-domaine `soundmatch.laponie-suedoise.ch`.
2. **Base de données** : créer une base MariaDB et un utilisateur avec les droits lecture, écriture et administration.
3. **Tables** : dans phpMyAdmin d'Infomaniak, exécuter `sql/soundmatch_mpd.sql` dans la nouvelle base.
4. **Fichiers** : se connecter en SFTP (Cyberduck, port 22) et envoyer les dossiers `src/classes` et `src/public` dans le dossier du site.
5. **Configuration** : sur le serveur, créer `config/database.ini` (même format que `database.example.ini`) avec l'hôte, le nom de la base, l'utilisateur et le mot de passe Infomaniak.
6. **Dossier racine** : dans *Avancé → Emplacement du site*, choisir le dossier `public`, pour que `classes/` et `config/` ne soient pas accessibles depuis le web.

Structure sur le serveur :

```
soundmatch.laponie-suedoise.ch/
├── classes/Database.php
├── config/database.ini   (uniquement sur le serveur, jamais sur GitHub)
└── public/index.php      (racine du site)
```

## Sécurité

- Les identifiants de la base ne sont jamais versionnés : `src/config/database.ini` est dans `.gitignore`.
- Seul le dossier `public` est accessible depuis le navigateur.
- Les données affichées sont protégées avec `htmlspecialchars()` (XSS).

## Utilisation de l'IA

Nous avons utilisé Claude (Anthropic) comme assistant pour :
- [à compléter : par ex. relire et améliorer le MPD, nous guider dans le déploiement sur Infomaniak, expliquer la connexion PDO]

Le code a été relu, testé et compris par l'équipe.
