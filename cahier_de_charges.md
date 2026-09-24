# Cahier des charges - SoundMatch 🎧

> Projet libre réalisé dans le cadre du cours **Programmation serveur 2 ** à la **HEIG-VD**, année 2026-2027 (groupe ProgServ2-B).

## Membres de l'équipe

| Nom             | 
| --------------- | 
| Justine Stalder | 
| Kristina Tomic  | 
## Description du projet

SoundMatch est un « Tinder de la musique ». L'utilisateur écoute des extraits musicaux et indique pour chacun s'il l'aime ou non (swipe). À partir de ces choix, l'application calcule le pourcentage de compatibilité musicale entre deux amis et affiche les titres qu'ils aiment en commun.

Les titres, pochettes et extraits proviennent de l'[API publique de Deezer](https://developers.deezer.com/api), gratuite et sans clé d'accès.

## Fonctionnalités principales

### Comptes et profil

- **F1 – Inscription** : création d'un compte (pseudo, e-mail, mot de passe). Le mot de passe est stocké haché dans la base de données. Un e-mail de bienvenue est envoyé.
- **F2 – Connexion et déconnexion** : la session est maintenue sur toutes les pages privées.
- **F3 – Gestion du profil** : modification du pseudo, de l'e-mail, du mot de passe et de la langue ; suppression du compte.

### Swipe musical

- **F4 – Swipe** : l'application propose un titre (pochette, titre, artiste, extrait de 30 secondes). L'utilisateur choisit « J'aime » ou « J'aime pas », et son choix est enregistré.
- **F5 – Mes titres likés** : liste des titres aimés, avec possibilité de réécouter l'extrait ou de retirer un titre.

### Amis et compatibilité

- **F6 – Amis** : envoi d'une demande d'ami à partir d'un pseudo, acceptation ou refus d'une demande, suppression d'un ami. Un e-mail prévient l'utilisateur lorsqu'il reçoit une demande.
- **F7 – Compatibilité** : pour chaque ami, l'application calcule le pourcentage de compatibilité (part des titres swipés par les deux pour lesquels ils ont fait le même choix) et affiche la liste des titres aimés en commun.

### Administration

- **F8 – Gestion du catalogue** : l'administrateur ajoute ou retire des genres et des playlists Deezer qui servent de source aux titres proposés. Tous les utilisateurs swipent ainsi dans le même catalogue, ce qui permet de comparer leurs choix.
- **F9 – Gestion des utilisateurs** : l'administrateur consulte la liste des utilisateurs, peut désactiver ou supprimer un compte.

### Multilingue

- **F10 – Traduction** : l'ensemble de l'application est disponible en français et en anglais.

## Pages prévues

| Type      | Pages                                                                    |
| --------- | ------------------------------------------------------------------------ |
| Publiques | Accueil (présentation du concept), Inscription, Connexion                |
| Privées   | Swipe, Mes titres likés, Mon profil, Mes amis, Compatibilité avec un ami |
| Admin     | Gestion du catalogue, Gestion des utilisateurs                           |

## Rôles

- **Utilisateur** : swipe, gère ses titres likés, ses amis et son profil.
- **Administrateur** : dispose des droits d'un utilisateur et accède en plus aux pages d'administration.

## Données principales

- **Utilisateurs** (`users`) : pseudo, e-mail, mot de passe haché, rôle, langue.
- **Titres** (`tracks`) : identifiant Deezer, titre, artiste, pochette, genre.
- **Genres** (`genres`) : nom, identifiant de la playlist Deezer source.
- **Swipes** (`swipes`) : lien entre un utilisateur et un titre, avec son choix (j'aime / j'aime pas).
- **Amitiés** (`friendships`) : lien entre deux utilisateurs, avec un statut (en attente / acceptée).

Les schémas MCD, MLD et MPD seront fournis à la séance 4.

## Fonctionnalités optionnelles (si le temps le permet)

- **O1 – Classement public** : page publique affichant les titres les plus likés par la communauté.
- **O2 – Swipe par genre** : l'utilisateur choisit un genre avant de swiper.
- **O3 – Profil musical** : statistiques personnelles (genres et artistes préférés).
- **O4 – Notification de compatibilité** : e-mail lorsqu'un ami atteint un certain nombre de swipes en commun, avec le pourcentage obtenu.
- **O5 – Annuler le dernier swipe**.
- **O6 – Animation de swipe** : glisser la carte à gauche ou à droite, comme sur Tinder.

## Contraintes techniques

- PHP 8.5 sans framework, programmation orientée objet avec chargement automatique des classes.
- Base de données MariaDB, informations de connexion dans un fichier de configuration.
- Environnement de développement basé sur le template du cours (Docker Compose, phpMyAdmin, Mailpit).
- Déploiement sur Infomaniak avec une base de données dédiée.
- Protection contre les attaques courantes (injections SQL, XSS, CSRF), validation côté client et côté serveur.
- JavaScript limité à l'interface (lecture des extraits et effet de swipe).
- Appels à l'API Deezer effectués côté serveur en PHP.

## Utilisation de l'intelligence artificielle

Claude (Anthropic) a été utilisé pour nous aider à comparer plusieurs idées de projet, évaluer leur faisabilité dans le temps imparti et structurer ce cahier des charges. Les choix de fonctionnalités ont été relus et validés par l'équipe. Toute utilisation future sera documentée dans le README.

---

## Fonctionnalités réellement implémentées

_À compléter en fin de projet._

## Conclusion

_À compléter en fin de projet._
