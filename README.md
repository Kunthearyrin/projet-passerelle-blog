# projet-passerelle-blog
Mon deuxième projet passerelle d'un blog/portfolio (BELIEVEMY)

Présentation Projet Passerelle n°2

❖ Description 
Le but de ce projet est la création d'un blog sur l’univers du jeu vidéo dans lequel je poste mes reviews/mes avis sur divers jeux. Un système d'authentification permet aux utilisateurs de devenir membre et ainsi de laisser des commentaires sous chaque article. Le compte admin peut créer, modifier, supprimer les articles. 

◇ Technologie utilisée 
    • Front end : HTML, CSS, SASS/Bootstrap
    • Back end : PHP, MySQL

❖ Les fonctionnalités principales 
    • Système d'authentification : connexion (login.php), inscription (signup.php)
    • Session et connexion automatique avec l'utilisation de cookies
    • La mise en place de rôle dès l'inscription : utilisateur classique ou admin (table users dans phpmyadmin)
    • Navbar conditionnelle en fonction de si l'utilisateur est connecté ou non
    • Page d'accueil index.php : partie portfolio avec présentation du blog, partie articles avec la présentation des articles publiés récemment et leur note attribuée (note visuelle avec étoile Font Awesome)
    • Page article.php : Présentation de l'article + formulaire d'ajout de commentaires si connecté 
    • Panel d'administration : admin.php pour la gestion des articles (création, modification ou suppression d'article), utilisation de la bibliothèque Font Awesome 
    • Création d'article : admin-create-article.php (formulaire avec upload d'images) 
    • Suppression d'article : admin-delete-article.php

◇ Les évolutions futures 
    • Permettre aux membres de pouvoir noter les articles/les jeux avec un système d'étoile : 5 étant 100% satisfait. 
    • Sécuriser davantage les mots de passe.
