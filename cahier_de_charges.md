# Cahier des charges – SoundMatch 🎧

> Projet libre réalisé dans le cadre du cours **Programmation serveur 2 (ProgServ2)** à la **HEIG-VD** (groupe ProgServ2-B).

## Membres de l'équipe
- Justine Stalder
- Kristina Tomic

---

## Présentation du projet
SoundMatch est une application web qui permet de découvrir sa **compatibilité musicale** avec d'autres personnes. Le concept s'inspire des applications de rencontre comme Tinder : l'utilisateur écoute des extraits de musique et indique s'il aime ou non chaque titre. L'application compare ensuite ses goûts avec ceux de ses amis et calcule un pourcentage de compatibilité.

L'objectif est de proposer une expérience **sociale et ludique**, basée sur la musique.

## Problématique
Les goûts musicaux peuvent être un moyen de créer des liens entre les personnes. Cependant, il n'est pas toujours facile de savoir rapidement quels artistes, genres ou morceaux nous avons en commun avec quelqu'un.

L'application cherche donc à répondre à la question :
> « À quel point mes goûts musicaux sont-ils compatibles avec ceux d'une autre personne ? »

## Objectifs

### Objectif principal
Permettre à deux utilisateurs de comparer leurs goûts musicaux et d'obtenir un **score de compatibilité musicale**.

### Objectifs secondaires
L'application doit permettre de :
- créer un profil ;
- construire ses goûts musicaux en aimant ou non des titres ;
- ajouter des amis ;
- afficher les titres et artistes aimés en commun ;
- calculer un pourcentage de compatibilité ;
- rendre la découverte musicale plus ludique et sociale.

## Public cible
L'application s'adresse principalement aux personnes qui écoutent régulièrement de la musique et souhaitent comparer leurs goûts avec leurs amis. Le public visé est surtout composé de **jeunes adultes** habitués aux plateformes musicales et aux applications sociales.

---

## Source des données musicales : Deezer
Les titres proposés proviennent de l'**API gratuite de Deezer**. Elle permet de récupérer des titres, des artistes, des genres et des **extraits audio de 30 secondes**, sans que l'utilisateur ait besoin d'un compte Deezer.

## Fonctionnalités principales
- **Comptes** : inscription, connexion, déconnexion, modification du profil. E-mail de bienvenue à l'inscription.
- **Swipe** : écouter un extrait et choisir « J'aime » ou « J'aime pas ».
- **Mes titres likés** : liste des titres aimés.
- **Amis** : envoyer, accepter ou refuser une demande d'ami. E-mail lors d'une nouvelle demande.
- **Compatibilité** : pourcentage de compatibilité avec un ami, titres et artistes aimés en commun.
- **Administration** : l'administrateur gère les genres musicaux proposés et les comptes utilisateurs.
- **Multilingue** : application disponible en français et en anglais.

## Fonctionnalités optionnelles
- Choix d'un genre avant de swiper.
- Classement public des titres les plus likés.
- Statistiques personnelles (genres et artistes préférés).
- Annuler le dernier swipe.
- Animation de swipe façon Tinder.
- Découvrir les profils d'autres utilisateurs (pas seulement ses amis).
- Connexion à Spotify pour importer ses artistes les plus écoutés (sous réserve des restrictions de l'API Spotify).

## Calcul de la compatibilité
Lorsqu'un utilisateur consulte le profil d'un ami, l'application compare les titres qu'ils ont swipés tous les deux :

> **Compatibilité = titres sur lesquels ils sont d'accord (les deux aiment ou les deux n'aiment pas) ÷ titres swipés par les deux × 100**

Exemple d'affichage :
> **Kris × Justine : 82 % de compatibilité**
> Artistes en commun : SZA · Drake · Brent Faiyaz
> Titres aimés en commun : 4
## Pages de l'application

### Pages publiques (sans connexion)
| Page | Description |
|---|---|
| Accueil | Présente le concept de SoundMatch et donne accès à l'inscription / connexion |
| Inscription | Création d'un compte (nom d'utilisateur, e-mail, mot de passe) |
| Connexion | Connexion avec e-mail et mot de passe |

### Pages privées (après connexion)
| Page | Description |
|---|---|
| Swipe | Écouter un extrait de 30 s et choisir « J'aime » ou « J'aime pas » |
| Mes titres likés | Liste des titres aimés, avec possibilité de retirer un like |
| Amis | Rechercher un utilisateur, envoyer / accepter / refuser une demande d'ami |
| Compatibilité | Pourcentage de compatibilité avec un ami + titres et artistes en commun |
| Mon profil | Modifier ses informations, changer de langue, se déconnecter |
| Administration *(admin uniquement)* | Gérer les genres musicaux proposés et les comptes utilisateurs |

## Rôles et droits
| Action | Visiteur | Utilisateur | Administrateur |
|---|:---:|:---:|:---:|
| Voir la page d'accueil | ✅ | ✅ | ✅ |
| Créer un compte / se connecter | ✅ | — | — |
| Swiper, liker, voir ses titres | ❌ | ✅ | ✅ |
| Gérer ses amis et voir la compatibilité | ❌ | ✅ | ✅ |
| Gérer les genres et les comptes | ❌ | ❌ | ✅ |

## Données principales
| Entité | Informations stockées |
|---|---|
| Utilisateur | pseudo, e-mail, mot de passe haché, rôle (utilisateur / admin), langue, date d'inscription |
| Titre | identifiant Deezer, titre, artiste, genre, lien de l'extrait, pochette |
| Genre | nom, identifiant Deezer, actif ou non |
| Swipe | utilisateur, titre, aimé ou non, date |
| Amitié | utilisateur qui demande, utilisateur qui reçoit, statut (en attente / acceptée / refusée) |

Relations :
- un utilisateur swipe plusieurs titres, un titre peut être swipé par plusieurs utilisateurs → table **Swipe**
- un utilisateur peut avoir plusieurs amis → table **Amitié**
- un titre appartient à un genre

## E-mails envoyés
- **Bienvenue** : à l'inscription
- **Nouvelle demande d'ami** : quand quelqu'un nous envoie une demande

## Technologies et contraintes
- PHP (programmation orientée objet, autoload des classes), **sans framework**
- Base de données **MariaDB / MySQL**
- API publique **Deezer** (appelée côté serveur en PHP)
- Déploiement sur **Infomaniak**
- Application disponible en **français et en anglais**
- Sécurité : mots de passe hachés, requêtes préparées (contre l'injection SQL), échappement des données affichées (contre le XSS), validation côté client et côté serveur
- Informations de connexion à la base de données dans un **fichier de configuration**

---

## Utilisation de l'intelligence artificielle
Claude (Anthropic) a été utilisé pour comparer nos idées de projet, fusionner nos deux versions et structurer ce cahier des charges. Le contenu a été relu et validé par l'équipe.

## Fonctionnalités réellement implémentées
_À compléter en fin de projet._

## Conclusion
_À compléter en fin de projet._
