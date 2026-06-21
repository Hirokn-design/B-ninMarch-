<?php
session_start();
require_once '../AdminLTE-master/config/db.php';

// Sécurité : Redirection si non connecté
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'commander.php';
    header('Location: login.php');
    exit();
}

// Sécurité : Panier vide
if (empty($_SESSION['panier'])) {
    header('Location: index.php');
    exit();
}

// Récupération infos utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$total_general = 0;
foreach ($_SESSION['panier'] as $item) {
    $total_general += ($item['prix'] * $item['quantite']);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Commander - BéninMarché</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php include_once 'navbar.php'; ?>


    <div class="container py-5">
        <h2 class="fw-bold mb-4">Finaliser la commande</h2>

        <form action="traitement_commande.php" method="POST">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card p-4 shadow-sm">
                        <label class="form-label">Adresse de livraison</label>
                        <textarea name="adresse_livraison" class="form-control" required rows="3"></textarea>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-warning w-100 fw-bold">Valider la commande</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card p-4 shadow-sm">
                        <h5>Résumé : <?= number_format($total_general, 0, ',', ' '); ?> FCFA</h5>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>

</html>