<?php
// 1. INITIALISATION DE LA SESSION ET CONNEXION BDD
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure ton fichier de connexion à la base de données (Ajuste le nom si nécessaire, ex: connexion.php)
require_once '../AdminLTE-master/config/db.php';

// 2. RÉCUPÉRATION DES PRODUITS DE LA LISTE DE FAVORIS
$produits_favoris = [];

if (!empty($_SESSION['favoris'])) {
    // Sécurisation des IDs sous forme de chaîne de caractères (ex: "1, 4, 12")
    $ids_favoris = array_map('intval', $_SESSION['favoris']);
    $in_clause = implode(',', $ids_favoris);

    // Requête pour récupérer uniquement les produits aimés
    $stmt = $pdo->query("SELECT * FROM produits WHERE id IN ($in_clause)");
    $produits_favoris = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris - BéninMarché</title>
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
    </style>
</head>

<body class="bg-light">

    <?php include_once 'navbar.php'; ?>

    <div class="container my-5" style="min-height: 70vh;">
        <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
            <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="fas fa-arrow-left"></i></a>
            <h2 class="fw-bold text-dark m-0">Ma Liste d'Envies <i class="fas fa-heart text-danger ms-2"></i></h2>
        </div>

        <?php if (empty($produits_favoris)): ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="far fa-heart text-muted" style="font-size: 5rem;"></i>
                </div>
                <h4 class="fw-semibold text-secondary">Votre liste de favoris est vide</h4>
                <p class="text-muted mb-4">Parcourez notre catalogue pour ajouter des coups de cœur !</p>
                <a href="index.php" class="btn btn-custom-orange text-white rounded-pill px-4 py-2 shadow-sm">
                    Découvrir les produits du Bénin
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4" id="grille-favoris">
                <?php foreach ($produits_favoris as $prod):
                    $image = !empty($prod['image']) ? $prod['image'] : 'default.png';
                    $nom_url = urlencode($prod['titre']);
                    $prix = $prod['prix'];
                ?>
                    <div class="col-6 col-md-4 col-lg-3 target-card" id="produit-card-<?= $prod['id']; ?>">
                        <div class="card h-100 border shadow-sm rounded-3 overflow-hidden bg-white position-relative">

                            <a href="ajouter_favoris.php?id=<?= $prod['id']; ?>"
                                class="btn-retirer-favoris position-absolute top-0 end-0 m-2 btn btn-sm btn-light border shadow-sm rounded-circle text-danger"
                                title="Retirer des favoris" data-id="<?= $prod['id']; ?>">
                                <i class="fas fa-times"></i>
                            </a>

                            <img src="img/<?= $image; ?>" class="card-img-top object-fit-cover" style="height: 180px;" alt="<?= htmlspecialchars($prod['titre']); ?>">

                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="card-title text-dark fw-bold text-truncate mb-1"><?= htmlspecialchars($prod['titre']); ?></h6>
                                    <p class="text-muted small text-truncate mb-2"><?= htmlspecialchars($prod['description'] ?? 'Produit local authentique'); ?></p>
                                    <p class="text-custom-orange font-monospace fw-bold mb-3"><?= number_format($prix, 0, ',', ' '); ?> FCFA</p>
                                </div>

                                <a href="ajouter_panier.php?id=<?= $prod['id']; ?>&nom=<?= $nom_url; ?>&prix=<?= $prix; ?>&image=<?= urlencode($image); ?>"
                                    class="btn btn-sm btn-custom-orange text-white rounded-pill w-100 py-2 btn-ajouter-panier">
                                    <i class="fas fa-shopping-cart me-2"></i>Ajouter au panier
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
        <div id="bm-toast" class="toast align-items-center text-white border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" style="background-color: #f38b00; transition: opacity 0.3s ease, transform 0.3s ease; display: none; opacity: 0; transform: translateY(-20px);">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle fs-5"></i>
                    <span id="toast-message">Action effectuée avec succès !</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="masquerToast()"></button>
            </div>
        </div>
    </div>

    <script>
        function afficherToast(message) {
            const toast = document.getElementById('bm-toast');
            const toastMessage = document.getElementById('toast-message');
            toastMessage.textContent = message;
            toast.style.display = 'block';
            toast.offsetHeight;
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
            setTimeout(masquerToast, 3000);
        }

        function masquerToast() {
            const toast = document.getElementById('bm-toast');
            if (!toast) return;
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                if (toast.style.opacity === '0') toast.style.display = 'none';
            }, 300);
        }

        document.addEventListener("DOMContentLoaded", function() {
            // 1. SUPPRESSION ASYNCHRONE D'UN FAVORIS DEPUIS LA GRILLE
            const boutonsRetirer = document.querySelectorAll('.btn-retirer-favoris');
            boutonsRetirer.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const idProd = this.getAttribute('data-id');
                    const urlCible = this.getAttribute('href');
                    const carteProduit = document.getElementById('produit-card-' + idProd);

                    fetch(urlCible, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                afficherToast(data.message);
                                // Animation de disparition en douceur de la carte
                                carteProduit.style.transition = "opacity 0.3s ease, transform 0.3s ease";
                                carteProduit.style.opacity = "0";
                                carteProduit.style.transform = "scale(0.8)";

                                setTimeout(() => {
                                    carteProduit.remove();
                                    // Si plus aucun favori, on recharge pour afficher l'écran vide propre
                                    if (document.querySelectorAll('.target-card').length === 0) {
                                        location.reload();
                                    }
                                }, 300);
                            }
                        });
                });
            });

            // 2. AJOUT AU PANIER SANS QUITTER LA PAGE FAVORIS
            const boutonsPanier = document.querySelectorAll('.btn-ajouter-panier');
            boutonsPanier.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetch(this.getAttribute('href'), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                afficherToast("Article ajouté au panier !");
                            }
                        });
                });
            });
        });
    </script>
</body>

</html>