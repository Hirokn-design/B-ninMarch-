<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    if (!isset($_SESSION['favoris'])) {
        $_SESSION['favoris'] = [];
    }

    // Si le produit est déjà en favori, on le retire (Toggle)
    if (in_array($id, $_SESSION['favoris'])) {
        $key = array_search($id, $_SESSION['favoris']);
        unset($_SESSION['favoris'][$key]);
        $_SESSION['favoris'] = array_values($_SESSION['favoris']); // Réindexer le tableau
        $action = 'removed';
        $message = 'Produit retiré de vos favoris !';
    } else {
        // Sinon, on l'ajoute
        $_SESSION['favoris'][] = $id;
        $action = 'added';
        $message = 'Produit ajouté à vos favoris !';
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'success',
        'action' => $action,
        'message' => $message,
        'count' => count($_SESSION['favoris'])
    ]);
    exit();
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['status' => 'error', 'message' => 'ID produit non conforme.']);
exit();
