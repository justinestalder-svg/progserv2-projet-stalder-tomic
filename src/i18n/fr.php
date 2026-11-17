<?php
// Tous les textes du site en français
return [
    // Commun à toutes les pages
    'francais' => 'Français',
    'anglais' => 'English',

    // Page d'accueil
    'accueil_description' => 'Projet réalisé dans le cadre du cours ProgServ2 à la HEIG-VD.',
    'accueil_genres' => 'Genres musicaux',
    'accueil_aucun_genre' => 'Aucun genre dans la base de données.',

    // Page d'inscription
    'inscription_titre' => 'Créer ton compte',
    'inscription_pseudo' => "Nom d'utilisateur / pseudo",
    'inscription_email' => 'Adresse mail',
    'inscription_mot_de_passe' => 'Mot de passe',
    'inscription_regle_longueur' => '10 caractères minimum',
    'inscription_regle_majuscule' => 'Une majuscule',
    'inscription_regle_chiffre' => 'Un chiffre',
    'inscription_regle_special' => 'Un caractère spécial',
    'inscription_cgu_debut' => "J'accepte les",
    'inscription_cgu_lien' => 'termes et conditions',
    'inscription_bouton' => 'Créer mon compte',
    'inscription_deja_compte' => 'Déjà un compte ?',
    'inscription_lien_connexion' => 'Se connecter',

    // Erreurs de l'inscription
    'erreur_pseudo' => '3 à 20 caractères : lettres, chiffres, point ou tiret bas.',
    'erreur_email' => "L'adresse e-mail n'est pas valide.",
    'erreur_mot_de_passe' => 'Le mot de passe ne respecte pas toutes les règles.',
    'erreur_cgu' => 'Tu dois accepter les termes et conditions.',
    'erreur_pseudo_utilise' => 'Ce pseudo est déjà utilisé.',
    'erreur_email_utilise' => 'Cette adresse e-mail est déjà utilisée.',

    // Page de connexion
    'connexion_titre' => 'Se connecter',
    'connexion_identifiant' => 'Adresse mail ou pseudo',
    'connexion_mot_de_passe' => 'Mot de passe',
    'connexion_bouton' => 'Se connecter',
    'connexion_pas_de_compte' => 'Pas encore de compte ?',
    'connexion_lien_inscription' => 'Créer un compte',

    // Erreurs de la connexion
    'erreur_champs_vides' => 'Remplis ton adresse mail (ou ton pseudo) et ton mot de passe.',
    'erreur_bloque' => 'Trop de tentatives. Réessaie après',
    'erreur_connexion' => 'Adresse mail, pseudo ou mot de passe incorrect.',

    // Page profil
    'profil_titre' => 'Mon profil',
    'profil_bonjour' => 'Bonjour',
    'profil_deconnexion' => 'Se déconnecter',

    // Bandeau cookies
    'cookies_message' => 'SoundMatch utilise uniquement des cookies nécessaires : pour se souvenir de ta langue et pour garder ta connexion.',
    'cookies_ok' => 'OK',

    // Menu des pages connectées
    'menu_concept' => 'Le concept',
    'menu_comment' => 'Comment ça marche ?',
    'menu_matchs' => 'Mes matchs',
    'menu_favoris' => 'Mes favoris',
    'menu_reglages' => 'Réglages',
    'menu_deconnexion' => 'Se déconnecter',

    // Page d'accueil
    'accueil_accroche' => 'Ta prochaine date commence par une chanson.',
    'accueil_texte' => "Sur SoundMatch, on ne choisit pas les gens sur leur photo : on découvre des personnes qui ont les mêmes goûts musicaux que soi, pour une rencontre amicale ou amoureuse.",
    'accueil_commencer' => 'Commencer à matcher →',
    'accueil_creer_compte' => 'Créer un compte',
    'accueil_se_connecter' => 'Se connecter',
    'accueil_meilleurs_matchs' => 'Tes meilleurs matchs',
    'accueil_aucun_match' => "Tu n'as pas encore de match. Fais une session d'écoute pour découvrir les personnes qui ont les mêmes goûts que toi.",
    'accueil_voir_matchs' => 'Voir tous mes matchs →',
    'accueil_ans' => 'ans',
    'concept_ecoute_titre' => 'Écoute',
    'concept_ecoute_texte' => '20 extraits de 30 secondes, de 4 genres différents.',
    'concept_choisis_titre' => 'Choisis',
    'concept_choisis_texte' => "« J'aime » ou « J'ignore », pas besoin d'écouter jusqu'au bout.",
    'concept_match_titre' => 'Match',
    'concept_match_texte' => 'Découvre les personnes qui ont les mêmes goûts que toi.',
    'comment_texte' => "Pour chaque genre, on compare ce que vous aimez et ce que vous n'aimez pas. La compatibilité est la moyenne des 4 genres : plus elle est haute, plus vos goûts se ressemblent.",
    'comment_exemple' => 'Exemple : Toi et Léa, 86 % de compatibilité.',

    // Création du profil (premier lancement)
    'creer_profil_titre' => 'Crée ton profil',
    'creer_profil_intro' => "Bienvenue sur SoundMatch ! Réponds à ces 4 questions pour créer ton profil. Toutes les questions sont obligatoires.",
    'creer_profil_genre' => 'Tu es…',
    'genre_femme' => 'Femme',
    'genre_homme' => 'Homme',
    'genre_autre' => 'Autre',
    'creer_profil_date' => 'Date de naissance',
    'creer_profil_photo' => 'Photo de profil (JPG ou PNG, 5 Mo maximum)',
    'creer_profil_instagram' => 'Ton Instagram',
    'creer_profil_instagram_aide' => "C'est toi qui choisis le partage : ton Instagram n'est jamais public, tu décides à quel match tu le montres.",
    'creer_profil_bouton' => 'Créer mon profil',
    'erreur_genre' => 'Choisis une réponse pour continuer.',
    'erreur_date_naissance' => 'Entre une date de naissance valide.',
    'erreur_age' => 'Désolé, SoundMatch est réservé aux 18 ans et plus.',
    'erreur_photo' => 'Ajoute une photo pour continuer.',
    'erreur_photo_taille' => 'La photo est trop lourde (5 Mo maximum).',
    'erreur_photo_format' => 'La photo doit être une image JPG ou PNG.',
    'erreur_instagram' => "2 à 30 caractères : lettres, chiffres, point ou tiret bas.",

    // Réglages
    'reglages_titre' => 'Réglages',
    'reglages_retour' => "← Retour à l'accueil",
    'reglages_succes' => 'Modifications enregistrées ✓',
    'reglages_mon_profil' => 'Mon profil',
    'reglages_instagram' => 'Instagram',
    'reglages_compte' => 'Compte et sécurité',
    'reglages_supprimer' => 'Supprimer mon compte',
    'reglages_photo' => 'Ma photo de profil',
    'reglages_changer_photo' => 'Changer la photo',
    'reglages_photo_aide' => "La photo est obligatoire : tu peux la changer, mais pas la retirer.",
    'reglages_enregistrer' => 'Enregistrer',
    'reglages_partage_avec' => 'Partagé avec',
    'reglages_aucun_partage' => "Tu n'as partagé ton insta avec personne.",
    'reglages_ne_plus_partager' => 'Ne plus partager',
    'reglages_email_bientot' => "Le changement d'adresse mail arrivera bientôt (il faut d'abord envoyer un mail de confirmation).",
    'reglages_modifier_mdp' => 'Modifier le mot de passe',
    'reglages_appareils' => 'Gérer mes appareils',
    'reglages_supprimer_texte' => 'Attention : la suppression de ton compte est définitive.',
];
