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

## Calcul de la compatibilité
Lorsqu'un utilisateur consulte le profil d'un ami, l'application compare les titres qu'ils ont swipés tous les deux :

> **Compatibilité = titres sur lesquels ils sont d'accord (les deux aiment ou les deux n'aiment pas) ÷ titres swipés par les deux × 100**

Exemple d'affichage :
> **Kris × Justine : 82 % de compatibilité**
> Artistes en commun : SZA · Drake · Brent Faiyaz
> Titres aimés en commun : 4

## Fonctionnalités optionnelles
- Choix d'un genre avant de swiper.
- Classement public des titres les plus likés.
- Statistiques personnelles (genres et artistes préférés).
- Annuler le dernier swipe.
- Animation de swipe façon Tinder.
- Découvrir les profils d'autres utilisateurs (pas seulement ses amis).
- Connexion à Spotify pour importer ses artistes les plus écoutés (sous réserve des restrictions de l'API Spotify).

---

## Utilisation de l'intelligence artificielle
Claude (Anthropic) a été utilisé pour comparer nos idées de projet, fusionner nos deux versions et structurer ce cahier des charges. Le contenu a été relu et validé par l'équipe.

## Fonctionnalités réellement implémentées
_À compléter en fin de projet._

## Conclusion
_À compléter en fin de projet._
