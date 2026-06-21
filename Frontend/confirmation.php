<?php
session_start();
// Sécurité : On vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Récupération de l'ID de commande si passé dans l'URL
$order_id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : 'N/A';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Commande validée - BéninMarché</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .btn-custom-orange {
            background-color: #f38b00;
            border-color: #f38b00;
            color: white;
        }

        .btn-custom-orange:hover {
            background-color: #d67a00;
            color: white;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-sm border-0 p-5 text-center" style="max-width: 500px;">
            <div class="mb-4">
                <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
            </div>

            <h2 class="fw-bold mb-3">Merci pour votre commande !</h2>
            <p class="text-muted">Votre commande <strong>n°<?= $order_id ?></strong> a bien été enregistrée sur BéninMarché.</p>
            <p class="mb-4">Nous préparons vos articles avec soin. Vous pouvez suivre l'évolution de votre commande directement depuis votre espace personnel.</p>

            <div class="d-grid gap-2">
                <a href="commande.php" class="btn btn-custom-orange py-2 fw-bold">Voir mes commandes</a>
                <a href="index.php" class="btn btn-outline-secondary py-2">Continuer mes achats</a>
            </div>
        </div>
    </div>

</body>

</html>