<?php
session_start();
require_once '../AdminLTE-master/config/db.php';

// Sécurité : Accès réservé aux connectés
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Récupération des commandes avec l'adresse_livraison
$sql = "SELECT c.id as commande_id, c.date_commande, c.statut, c.montant_total, c.adresse_livraison, 
               l.quantite, l.prix_unitaire, p.titre as produit_nom
        FROM commandes c
        JOIN lignes_commande l ON c.id = l.id_commande
        JOIN produits p ON l.id_produit = p.id
        WHERE c.id_client = ?
        ORDER BY c.date_commande DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Regroupement par commande
$commandes = [];
foreach ($results as $row) {
    $commandes[$row['commande_id']]['info'] = [
        'date' => $row['date_commande'],
        'statut' => $row['statut'],
        'total' => $row['montant_total'],
        'adresse' => $row['adresse_livraison'] // Ajout de l'adresse
    ];
    $commandes[$row['commande_id']]['articles'][] = [
        'nom' => $row['produit_nom'],
        'quantite' => $row['quantite'],
        'prix' => $row['prix_unitaire']
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mes Commandes - BéninMarché</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php include_once 'navbar.php'; ?>


    <div class="container py-5">
        <h2 class="fw-bold mb-4">Mes commandes</h2>

        <?php if (empty($commandes)): ?>
            <div class="alert alert-info">Vous n'avez pas encore passé de commande.</div>
        <?php else: ?>
            <?php foreach ($commandes as $id => $cmd): ?>
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span><strong>Commande n°<?= $id ?></strong> - <?= date('d/m/Y à H:i', strtotime($cmd['info']['date'])) ?></span>
                        <span class="badge bg-warning text-dark"><?= ucfirst($cmd['info']['statut']) ?></span>
                    </div>

                    <div class="card-body">
                        <div class="mb-3 p-2 bg-light border-start border-4 border-warning">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.75rem;">Adresse de livraison</small>
                            <span class="text-dark"><?= htmlspecialchars($cmd['info']['adresse']) ?></span>
                        </div>

                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Qté</th>
                                    <th>Prix unitaire</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cmd['articles'] as $article): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($article['nom']) ?></td>
                                        <td><?= $article['quantite'] ?></td>
                                        <td><?= number_format($article['prix'], 0, ',', ' ') ?> FCFA</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="text-end fw-bold fs-5 border-top pt-2">
                            Total : <?= number_format($cmd['info']['total'], 0, ',', ' ') ?> FCFA
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>