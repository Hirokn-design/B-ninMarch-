<?php
// 1. INITIALISATION DE LA SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. RÉCUPÉRATION ET SÉCURISATION DES PARAMÈTRES DE L'URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$nom = isset($_GET['nom']) ? htmlspecialchars(trim($_GET['nom'])) : 'Produit';
$prix = isset($_GET['prix']) ? floatval($_GET['prix']) : 0.0;
$image = !empty($_GET['image']) ? htmlspecialchars(trim($_GET['image'])) : 'default.png';

// On s'assure que l'ID du produit est valide avant de manipuler le panier
if ($id > 0) {

    // 3. CRÉATION DU PANIER S'IL N'EXISTE PAS ENCORE
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    // 4. AJOUT OU INCRÉMENTATION DE LA QUANTITÉ
    if (isset($_SESSION['panier'][$id])) {
        // Si le produit est déjà présent, on augmente simplement sa quantité de 1
        $_SESSION['panier'][$id]['quantite']++;
    } else {
        // Sinon, on injecte la structure complète du nouvel article
        $_SESSION['panier'][$id] = [
            'nom'      => $nom,
            'prix'     => $prix,
            'quantite' => 1,
            'image'    => $image
        ];
    }

    // 5. COMPTAGE DU NOMBRE D'ARTICLES ET CALCUL DU PRIX TOTAL
    $total_articles = 0;
    $montant_total = 0;

    foreach ($_SESSION['panier'] as $item) {
        $total_articles += $item['quantite'];
        $montant_total += ($item['prix'] * $item['quantite']);
    }

    // 6. GESTION DE LA RÉPONSE EN FONCTION DE LA MÉTHODE D'APPEL
    // Si la requête vient de JavaScript (Fetch / AJAX)
    if ((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_GET['ajax'])) {
        header('Content-Type: application/json; charset=utf-8');

        // On renvoie exactement les clés attendues par le JavaScript
        echo json_encode([
            'status'        => 'success',
            'message'       => 'L\'article a bien été ajouté au panier BéninMarché.',
            'count'         => $total_articles,
            'total_formate' => number_format($montant_total, 0, ',', ' ')
        ]);
        exit();
    }

    // Redirection de secours classique (accès direct ou JS désactivé)
    header('Location: ' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php'));
    exit();
}

// GESTION DU CAS D'ERREUR (ID invalide ou manquant)
if ((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_GET['ajax'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status'  => 'error',
        'message' => 'Impossible d\'ajouter cet article. ID produit non conforme.'
    ]);
    exit();
}

// Redirection de secours vers l'accueil en cas d'erreur brute
header('Location: index.php');
exit();
