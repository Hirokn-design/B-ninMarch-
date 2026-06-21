<?php
// 1. DÉMARRAGE DE LA SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. INCLUSION DE LA CONNEXION À LA BDD
require_once '../AdminLTE-master/config/db.php';

// 3. TRAITEMENT DE LA MISE À JOUR DE LA QUANTITÉ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_qty') {
    $id_prod = intval($_POST['id']);
    $nouvelle_qty = intval($_POST['quantite']);

    if (isset($_SESSION['panier'][$id_prod])) {
        // Sécurité : Vérification du stock réel en BDD
        $stmt_stock = $pdo->prepare("SELECT stock FROM produits WHERE id = :id");
        $stmt_stock->execute(['id' => $id_prod]);
        $prod_data = $stmt_stock->fetch();
        $stock_max = $prod_data ? intval($prod_data['stock']) : 1;

        // Règles de validation des limites
        if ($nouvelle_qty < 1) {
            $nouvelle_qty = 1;
        } elseif ($nouvelle_qty > $stock_max) {
            $nouvelle_qty = $stock_max;
        }

        $_SESSION['panier'][$id_prod]['quantite'] = $nouvelle_qty;
    }
    header('Location: panier.php');
    exit();
}

// 4. GESTION DE LA SUPPRESSION D'UN ARTICLE SINGLE
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && isset($_GET['id'])) {
    $id_a_supprimer = intval($_GET['id']);
    if (isset($_SESSION['panier'][$id_a_supprimer])) {
        unset($_SESSION['panier'][$id_a_supprimer]);
    }
    header('Location: panier.php');
    exit();
}

// 5. GESTION DU BOUTON "VIDER LE PANIER"
if (isset($_GET['action']) && $_GET['action'] == 'vider') {
    unset($_SESSION['panier']);
    header('Location: panier.php');
    exit();
}

// 6. CALCULS FINANCIERS GLOBAUX
$total_general = 0;
$nombre_articles_total = 0;
if (!empty($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $item) {
        $total_general += $item['prix'] * $item['quantite'];
        $nombre_articles_total += $item['quantite'];
    }
}

// 7. SUGGESTIONS DE PRODUITS (CROSS-SELLING)
// On va chercher 4 produits aléatoires disponibles pour remplir le bas du panier
try {
    $query_suggestions = "SELECT * FROM produits WHERE statut = 'Disponible' ORDER BY RAND() LIMIT 4";
    $stmt_sug = $pdo->query($query_suggestions);
    $suggestions = $stmt_sug->fetchAll();
} catch (PDOException $e) {
    $suggestions = [];
}

// Paramètre marketing : Seuil pour livraison gratuite au Bénin (ex: 25 000 FCFA)
$seuil_livraison_gratuite = 25000;
$reste_pour_gratuité = $seuil_livraison_gratuite - $total_general;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - BéninMarché</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php include_once 'navbar.php'; ?>

    <div class="container py-5">

        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <a href="index.php" class="btn btn-outline-custom-orange btn-sm rounded-pill mb-2">
                    <i class="fas fa-arrow-left me-1"></i> Continuer mes achats
                </a>
                <h2 class="fw-bold text-dark mb-0">Mon Panier <span class="fs-5 text-muted fw-normal">(<?= $nombre_articles_total; ?> article<?= $nombre_articles_total > 1 ? 's' : ''; ?>)</span></h2>
            </div>
            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                <?php if (!empty($_SESSION['panier'])): ?>
                    <a href="panier.php?action=vider" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Voulez-vous vraiment vider l\'intégralité de votre panier BéninMarché ?')">
                        <i class="fas fa-trash-alt me-1"></i> Vider le panier
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($_SESSION['panier'])): ?>
            <div class="row my-5">
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-white rounded shadow-sm d-inline-block border w-100" style="max-width: 500px;">
                        <i class="fas fa-shopping-basket text-muted display-1 mb-4"></i>
                        <h4 class="text-dark fw-bold mb-3">Votre panier est de taille vide</h4>
                        <p class="text-muted mb-4">Parcourez les merveilles de notre terroir et ajoutez des articles.</p>
                        <a href="index.php" class="btn btn-custom-orange text-white rounded-pill px-4 py-2 fw-semibold shadow-sm">
                            Découvrir les produits
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">

                <div class="col-lg-8">
                    <div class="card border shadow-sm rounded bg-white overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-align-middle mb-0">
                                <thead class="table-light text-uppercase fs-7 border-bottom">
                                    <tr>
                                        <th scope="col" class="ps-3 py-3">Produit</th>
                                        <th scope="col" class="py-3">Prix</th>
                                        <th scope="col" class="py-3 text-center" style="width: 140px;">Quantité</th>
                                        <th scope="col" class="py-3">Total</th>
                                        <th scope="col" class="pe-3 py-3 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($_SESSION['panier'] as $id => $article):
                                        // 1. Récupération dynamique du stock max actuel pour ce produit
                                        $stmt_check = $pdo->prepare("SELECT stock FROM produits WHERE id = :id");
                                        $stmt_check->execute(['id' => $id]);
                                        $real_stock = $stmt_check->fetch();
                                        $stock_disponible = $real_stock ? intval($real_stock['stock']) : 1;

                                        // 2. Calcul du sous-total
                                        $sous_total = $article['prix'] * $article['quantite'];

                                        // 3. Déclaration de tes variables courtes
                                        $prix = $article['prix'];
                                        $nom_url = urlencode($article['nom']);
                                        $image = !empty($article['image']) ? $article['image'] : 'default.png';
                                    ?>
                                        <tr class="align-middle">
                                            <td class="ps-3 py-3">
                                                <div class="d-flex align-items-center">
                                                    <img src="img/<?= $image; ?>" class="rounded bg-light border me-3 object-fit-cover" style="width: 65px; height: 65px;" alt="<?= htmlspecialchars($article['nom']); ?>">
                                                    <div>
                                                        <h6 class="mb-0 text-dark fw-semibold text-truncate" style="max-width: 180px;"><?= htmlspecialchars($article['nom']); ?></h6>
                                                        <small class="text-muted d-block precision-stock">Stock restant : <?= $stock_disponible; ?></small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="py-3 fw-medium text-secondary">
                                                <?= number_format($prix, 0, ',', ' '); ?> <small>FCFA</small>
                                            </td>

                                            <td class="py-3 text-center">
                                                <form action="panier.php" method="POST" class="d-flex align-items-center justify-content-center">
                                                    <input type="hidden" name="action" value="update_qty">
                                                    <input type="hidden" name="id" value="<?= $id; ?>">

                                                    <div class="input-group input-group-sm rounded-pill border overflow-hidden bg-light" style="max-width: 110px;">
                                                        <button type="button" class="btn btn-link text-dark px-2 py-0 border-0 text-decoration-none" onclick="decrementQty(this)">
                                                            <i class="fas fa-minus fs-7"></i>
                                                        </button>
                                                        <input type="number" name="quantite"
                                                            class="form-control text-center bg-transparent border-0 fw-bold px-0 p-1 input-qty"
                                                            value="<?= $article['quantite']; ?>"
                                                            min="1"
                                                            max="<?= $stock_disponible; ?>"
                                                            onchange="validateAndSubmit(this, <?= $stock_disponible; ?>)">
                                                        <button type="button" class="btn btn-link text-dark px-2 py-0 border-0 text-decoration-none" onclick="incrementQty(this, <?= $stock_disponible; ?>)">
                                                            <i class="fas fa-plus fs-7"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>

                                            <td class="py-3 fw-bold text-custom-orange">
                                                <?= number_format($sous_total, 0, ',', ' '); ?> <small>FCFA</small>
                                            </td>

                                            <td class="pe-3 py-3 text-end">
                                                <a href="panier.php?action=supprimer&id=<?= $id; ?>" class="btn btn-sm btn-link text-custom-red p-1 text-decoration-none" title="Retirer l'article">
                                                    <i class="fas fa-trash-alt fs-5"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border shadow-sm rounded bg-white p-4">
                        <h5 class="fw-bold text-dark border-bottom pb-3 mb-3">Résumé de la commande</h5>

                        <?php if ($reste_pour_gratuité > 0): ?>
                            <div class="mb-4 p-2 bg-light rounded border">
                                <small class="text-dark d-block mb-1">Plus que <strong class="text-custom-orange"><?= number_format($reste_pour_gratuité, 0, ',', ' '); ?> FCFA</strong> pour la livraison gratuite !</small>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar progress-bar-striped" role="progressbar" style="width: <?= ($total_general / $seuil_livraison_gratuite) * 100; ?>%; background-color: var(--bm-orange);" aria-valuenow="<?= $total_general; ?>" aria-valuemin="0" aria-valuemax="<?= $seuil_livraison_gratuite; ?>"></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mb-4 p-2 bg-light rounded border border-success text-center">
                                <span class="text-success small fw-bold"><i class="fas fa-truck-moving me-1"></i> Livraison gratuite offerte à Cotonou !</span>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Sous-total</span>
                            <span class="fw-medium text-dark"><?= number_format($total_general, 0, ',', ' '); ?> FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                            <span class="text-muted">Frais de livraison</span>
                            <span class="text-secondary small"><?= $reste_pour_gratuité > 0 ? 'Calculés à l\'étape finale' : 'Offerts'; ?></span>
                        </div>

                        <div class="d-flex justify-content-between align-items-baseline mb-4">
                            <span class="fw-bold text-dark fs-5">Montant Final</span>
                            <span class="text-custom-orange fs-4 fw-black font-monospace"><?= number_format($total_general, 0, ',', ' '); ?> <span class="fs-6">FCFA</span></span>
                        </div>

                        <a href="commander.php" class="btn btn-custom-orange text-white w-100 rounded-pill py-2.5 fw-semibold shadow-sm d-flex justify-content-center align-items-center gap-2">
                            Passer à la caisse <i class="fas fa-chevron-right small"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($suggestions)): ?>
            <div class="row mt-5 pt-4">
                <div class="col-12">
                    <h4 class="fw-bold text-dark mb-4 border-bottom pb-2">Fréquemment achetés ensemble</h4>
                </div>
                <?php foreach ($suggestions as $sug):
                    $img_sug = !empty($sug['image']) ? $sug['image'] : 'default.png';

                    // Sécurisation des variables pour éviter les index inexistants et les plantages PHP
                    $nom_produit = $sug['nom'] ?? $sug['titre'] ?? 'Produit sans nom';
                    $id_produit = intval($sug['id'] ?? 0);
                    $prix_produit = intval($sug['prix'] ?? 0);
                ?>
                    <div class="col-6 col-md-4 col-lg-3 mb-3">
                        <div class="card h-100 border shadow-sm rounded-3 overflow-hidden bg-white">
                            <img src="img/<?= $img_sug; ?>" class="card-img-top object-fit-cover" style="height: 140px;" alt="<?= htmlspecialchars($nom_produit); ?>">
                            <div class="card-body p-2 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="card-title text-dark fw-semibold text-truncate mb-1">
                                        <?= htmlspecialchars($nom_produit); ?>
                                    </h6>
                                    <p class="text-custom-orange font-monospace fw-bold small mb-2">
                                        <?= number_format($prix_produit, 0, ',', ' '); ?> FCFA
                                    </p>
                                </div>
                                <a href="ajouter_panier.php?id=<?= $id_produit; ?>&nom=<?= urlencode($nom_produit); ?>&prix=<?= $prix_produit; ?>" class="btn btn-sm btn-custom-orange text-white rounded-pill w-100 py-1 fs-7">
                                    <i class="fas fa-shopping-cart me-1"></i> Ajouter
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

    <style>
        /* CSS Palette Personnalisée BéninMarché */
        :root {
            --bm-orange: #f38b00;
            /* Orange logo */
            --bm-red: #f11a00;
            /* Rouge badge */
        }

        .text-custom-orange {
            color: var(--bm-orange) !important;
        }

        .text-custom-red {
            color: var(--bm-red) !important;
        }

        .btn-custom-orange {
            background-color: var(--bm-orange) !important;
            border-color: var(--bm-orange) !important;
        }

        .btn-custom-orange:hover {
            background-color: #d67a00 !important;
            border-color: #d67a00 !important;
        }

        .btn-outline-custom-orange {
            color: var(--bm-orange) !important;
            border-color: var(--bm-orange) !important;
        }

        .btn-outline-custom-orange:hover {
            background-color: var(--bm-orange) !important;
            color: #fff !important;
        }

        .fs-7 {
            font-size: 0.85rem;
        }

        .fw-black {
            font-weight: 800;
        }

        .object-fit-cover {
            object-fit: cover;
        }

        .precision-stock {
            font-size: 0.75rem;
        }

        .table> :not(caption)>*>* {
            border-bottom-width: 1px;
            border-color: #f1f1f1;
        }

        .input-qty::-webkit-outer-spin-button,
        .input-qty::-webkit-inner-spin-button {
            -webkit-appearance: none;
            appearance: none;
            /* 🎯 Ajout de la propriété standard demandée */
            margin: 0;
        }

        .input-qty {
            -moz-appearance: textfield;
            appearance: textfield;
            /* 🎯 Ajout ici aussi par sécurité pour Firefox */
        }
    </style>

    <script>
        // SCRIPTS DE FLUIDITÉ POUR LES QUANTITÉS (JS)
        function incrementQty(button, maxStock) {
            const input = button.closest('.input-group').querySelector('.input-qty');
            let currentVal = parseInt(input.value) || 1;
            if (currentVal < maxStock) {
                input.value = currentVal + 1;
                input.form.submit();
            } else {
                alert("Désolé, le stock maximal disponible pour ce produit au Bénin est atteint (" + maxStock + ").");
            }
        }

        function decrementQty(button) {
            const input = button.closest('.input-group').querySelector('.input-qty');
            let currentVal = parseInt(input.value) || 1;
            if (currentVal > 1) {
                input.value = currentVal - 1;
                input.form.submit();
            }
        }

        function validateAndSubmit(input, maxStock) {
            let value = parseInt(input.value);
            if (isNaN(value) || value < 1) {
                input.value = 1;
            } else if (value > maxStock) {
                alert("Quantité automatiquement ajustée au stock disponible : " + maxStock);
                input.value = maxStock;
            }
            input.form.submit();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>