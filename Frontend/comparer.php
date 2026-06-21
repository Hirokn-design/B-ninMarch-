<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../AdminLTE-master/config/db.php'; // Ta connexion PDO

$produits_compares = [];

if (!empty($_SESSION['comparer'])) {
    $ids_compares = array_map('intval', $_SESSION['comparer']);
    $in_clause = implode(',', $ids_compares);

    $stmt = $pdo->query("SELECT * FROM produits WHERE id IN ($in_clause)");
    $produits_compares = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparateur de Produits - BéninMarché</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .text-custom-orange {
            color: #f38b00;
        }

        .btn-custom-orange {
            background-color: #f38b00;
            border-color: #f38b00;
        }

        .btn-custom-orange:hover {
            background-color: #d77a00;
            border-color: #d77a00;
        }

        .object-fit-cover {
            object-fit: cover;
        }

        .table-compare th {
            background-color: #f8f9fa;
            min-width: 150px;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light">

    <?php include_once 'navbar.php'; ?>

    <div class="container my-5" style="min-height: 75vh;">
        <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
            <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="fas fa-arrow-left"></i></a>
            <h2 class="fw-bold text-dark m-0">Comparateur de Produits <i class="fas fa-balance-scale text-custom-orange ms-2"></i></h2>
        </div>

        <?php if (empty($produits_compares)): ?>
            <div class="text-center py-5">
                <i class="fas fa-balance-scale text-muted mb-4" style="font-size: 5rem;"></i>
                <h4 class="fw-semibold text-secondary">Aucun produit à comparer</h4>
                <p class="text-muted mb-4">Sélectionnez jusqu'à 3 articles sur le catalogue pour analyser leurs caractéristiques.</p>
                <a href="index.php" class="btn btn-custom-orange text-white rounded-pill px-4">Retour à l'accueil</a>
            </div>
        <?php else: ?>
            <div class="table-responsive bg-white rounded-3 shadow-sm border">
                <table class="table table-bordered table-compare align-middle text-center m-0">
                    <thead>
                        <tr>
                            <th class="text-start ps-3 align-middle" style="width: 200px;">Caractéristiques</th>
                            <?php foreach ($produits_compares as $prod): ?>
                                <th id="col-head-<?= $prod['id']; ?>">
                                    <div class="position-relative p-2">
                                        <a href="ajouter_comparer.php?id=<?= $prod['id']; ?>" class="btn-retirer-compare position-absolute top-0 end-0 btn btn-sm btn-light border text-danger rounded-circle" data-id="<?= $prod['id']; ?>" title="Enlever">
                                            <i class="fas fa-times"></i>
                                        </a>
                                        <img src="img/<?= !empty($prod['image']) ? $prod['image'] : 'default.png'; ?>" class="rounded object-fit-cover mb-2" style="width: 80px; height: 80px;" alt="">
                                        <h6 class="fw-bold text-dark text-truncate mb-0" style="max-width: 180px;"><?= htmlspecialchars($prod['titre']); ?></h6>
                                    </div>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="text-start ps-3">Prix</th>
                            <?php foreach ($produits_compares as $prod): ?>
                                <td class="font-monospace fw-bold text-custom-orange fs-5 col-data-<?= $prod['id']; ?>">
                                    <?= number_format($prod['prix'], 0, ',', ' '); ?> FCFA
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th class="text-start ps-3">Disponibilité</th>
                            <?php foreach ($produits_compares as $prod): ?>
                                <td class="col-data-<?= $prod['id']; ?>">
                                    <?php if ($prod['stock'] > 0): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">En Stock (<?= $prod['stock']; ?>)</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">Rupture</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th class="text-start ps-3">Description</th>
                            <?php foreach ($produits_compares as $prod): ?>
                                <td class="text-muted small text-start p-3 col-data-<?= $prod['id']; ?>" style="max-width: 250px; vertical-align: top;">
                                    <?= htmlspecialchars($prod['description'] ?? 'Aucune description disponible pour ce produit.'); ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th class="text-start ps-3">Action</th>
                            <?php foreach ($produits_compares as $prod): ?>
                                <td class="p-3 col-data-<?= $prod['id']; ?>">
                                    <a href="ajouter_panier.php?id=<?= $prod['id']; ?>&nom=<?= urlencode($prod['titre']); ?>&prix=<?= $prod['prix']; ?>&image=<?= urlencode($prod['image'] ?? 'default.png'); ?>"
                                        class="btn btn-sm btn-custom-orange text-white rounded-pill px-3 btn-ajouter-panier">
                                        <i class="fas fa-shopping-cart me-1"></i> Acheter
                                    </a>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.btn-retirer-compare').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const idProd = this.getAttribute('data-id');

                    fetch(this.getAttribute('href'), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // Supprimer dynamiquement la colonne visuelle sans recharger la page
                                document.getElementById('col-head-' + idProd).remove();
                                document.querySelectorAll('.col-data-' + idProd).forEach(el => el.remove());

                                // S'il n'y a plus de produits comparés, on rafraîchit pour afficher l'écran vide propre
                                if (data.count === 0) {
                                    location.reload();
                                }
                            }
                        });
                });
            });
        });
    </script>
</body>

</html>