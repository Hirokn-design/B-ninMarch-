<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    if (!isset($_SESSION['comparer'])) {
        $_SESSION['comparer'] = [];
    }

    // Si le produit est déjà dans le comparateur, on le retire
    if (in_array($id, $_SESSION['comparer'])) {
        $key = array_search($id, $_SESSION['comparer']);
        unset($_SESSION['comparer'][$key]);
        $_SESSION['comparer'] = array_values($_SESSION['comparer']);
        $action = 'removed';
        $message = 'Produit retiré du comparateur !';
    } else {
        // Limite stricte à 3 produits maximum
        if (count($_SESSION['comparer']) >= 3) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 'error',
                'message' => 'Vous pouvez comparer 3 produits maximum en même temps !'
            ]);
            exit();
        }

        $_SESSION['comparer'][] = $id;
        $action = 'added';
        $message = 'Produit ajouté au comparateur !';
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'success',
        'action' => $action,
        'message' => $message,
        'count' => count($_SESSION['comparer'])
    ]);
    exit();
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['status' => 'error', 'message' => 'ID produit non conforme.']);
exit();
