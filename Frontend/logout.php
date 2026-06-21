<?php
// 1. Démarrer la session pour pouvoir la manipuler
session_start();

// 2. Supprimer toutes les variables de session (ex: user_id, user_nom, etc.)
$_SESSION = array();

// 3. Détruire la session côté serveur
session_destroy();

// 4. Rediriger l'utilisateur vers l'accueil (ou vers login.php)
header('Location: index.php');
exit();
