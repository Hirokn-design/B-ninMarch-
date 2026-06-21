<?php
// 0. DÉMARRAGE DE LA SESSION (Crucial pour lire $_SESSION['panier'])
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Inclusion de la connexion à la base de données
require_once '../AdminLTE-master/config/db.php';

// 🎯 INITIALISATION : On définit la variable par défaut pour éviter le warning
$id_cat = 0;
$produits = [];

$montant_total_panier = 0;
$nb_articles_panier = 0;

// Maintenant $_SESSION est accessible !
if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $item) {
        $montant_total_panier += ($item['prix'] * $item['quantite']);
        $nb_articles_panier += $item['quantite'];
    }
}

try {
    // 2. Récupération de TOUTES les catégories avec le nombre de produits
    // J'ai fusionné tes deux requêtes sur les catégories car la 2ème écrasait la 1ère
    $query_cat = "SELECT c.*, COUNT(p.id) as total_produits 
                  FROM categories c 
                  LEFT JOIN produits p ON c.id = p.id_categorie AND p.statut != 'En attente de validation'
                  GROUP BY c.id
                  ORDER BY c.nom ASC";
    $stmt_cat = $pdo->query($query_cat);
    $categories = $stmt_cat->fetchAll();

    // 3. Récupération de l'ID depuis l'URL si présent
    $id_cat = isset($_GET['id_cat']) ? intval($_GET['id_cat']) : 0;

    if ($id_cat > 0) {
        $query = "SELECT * FROM produits WHERE id_categorie = :id_cat ORDER BY id DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id_cat' => $id_cat]);
        $produits = $stmt->fetchAll();
    } else {
        $query = "SELECT * FROM produits ORDER BY id DESC";
        $stmt = $pdo->query($query);
        $produits = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    $erreur_bdd = "Erreur de chargement des données : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>BéninMarché: Plateforme de vente de produits locaux</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="shortcut icon" href="img/logo.ico" type="image/x-icon">

    <style>
        .status-indicator {
            display: inline-flex;
            align-items: center;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .dot {
            height: 10px;
            width: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .bg-online {
            background-color: #28a745;
        }

        /* Vert Bootstrap */
        .bg-offline {
            background-color: #dc3545;
        }

        /* Rouge Bootstrap */
    </style>
</head>

<body>

    <!-- Spinner Start -->
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Chargement...</span>
        </div>
    </div>
    <!-- Spinner End -->


    <!-- Topbar Start -->
    <div class="container-fluid px-5 d-none border-bottom d-lg-block">
        <div class="row gx-0 align-items-center">
            <div class="col-lg-4 text-center text-lg-start mb-lg-0">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <a href="#" class="text-muted me-2">Aide</a><small> / </small>
                    <a href="#" class="text-muted mx-2">Soutien</a><small> / </small>
                    <a href="contact.php" class="text-muted ms-2">Contact</a>

                </div>
            </div>
            <div class="col-lg-4 text-center d-flex align-items-center justify-content-center">
                <small class="text-dark">Appelez-nous :</small>
                <a href="#" class="text-muted">(+229) 01 99 01 92 59</a>
            </div>

            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle text-muted me-2" data-bs-toggle="dropdown"><small>
                                FCFA</small></a>
                        <div class="dropdown-menu rounded">
                            <a href="#" class="dropdown-item"> FCFA</a>
                            <a href="#" class="dropdown-item"> Euro</a>
                            <a href="#" class="dropdown-item"> Dollar</a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle text-muted mx-2" data-bs-toggle="dropdown"><small>
                                Français</small></a>
                        <div class="dropdown-menu rounded">
                            <a href="#" class="dropdown-item"> Français</a>
                            <a href="#" class="dropdown-item"> English</a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle text-muted ms-2" data-bs-toggle="dropdown"><small><i
                                    class="fa fa-home me-2"></i> Mon espace</small></a>
                        <div class="dropdown-menu rounded">
                            <a href="login.php" class="dropdown-item"> Connexion</a>
                            <a href="favoris.php" class="dropdown-item"> Mes favoris</a>
                            <a href="comparer.php" class="dropdown-item"> Mon comparateur</a>
                            <a href="panier.php" class="dropdown-item"> Mon panier</a>
                            <a href="commande.php" class="dropdown-item"> Mes commandes</a>
                            <a href="#" class="dropdown-item"> Paramètres</a>
                            <a href="#" class="dropdown-item"> Mon compte</a>
                            <a href="logout.php" class="dropdown-item"> Déconnexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid px-5 py-4 d-none d-lg-block">
        <div class="row gx-0 align-items-center text-center">
            <div class="col-md-4 col-lg-3 text-center text-lg-start">
                <div class="d-inline-flex align-items-center">
                    <a href="" class="navbar-brand p-0">
                        <h1 class="display-5 text-primary m-0">
                            <img src="img/logo.png" alt="Logo" class="me-2" style="width: 90px; height: 90px; object-fit: cover;">
                            <i class="text-secondary me-2"></i>BéninMarché
                        </h1> <br> <br><br><br>


                    </a>
                </div>
            </div>
            <div class="col-md-4 col-lg-6 text-center">
                <div class="position-relative">
                    <div class="d-flex border rounded-pill bg-white">
                        <input id="search-input" class="form-control border-0 rounded-pill w-100 py-3" type="text" placeholder="Rechercher un produit...">
                        <select id="search-cat" class="form-select text-dark border-0 border-start rounded-0 p-3" style="width: 200px;">
                            <option value="All">Toutes catégories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id']); ?>">
                                    <?= htmlspecialchars($cat['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-primary rounded-pill py-3 px-5"><i class="fas fa-search"></i></button>
                    </div>
                    <div id="search-results" class="position-absolute w-100 bg-white shadow rounded mt-2 border" style="z-index: 1050; display: none;"></div>
                </div>
            </div>
            <?php
            // On initialise le montant total à 0 par défaut
            $montant_total_panier = 0;

            // Si le panier existe en session et n'est pas vide, on calcule le total
            if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
                foreach ($_SESSION['panier'] as $article) {
                    // Prix du produit * Quantité ajoutée
                    $montant_total_panier += $article['prix'] * $article['quantite'];
                }
            }
            ?>

            <div class="col-md-4 col-lg-3 text-center text-lg-end">
                <div class="d-inline-flex align-items-center">
                    <a href="comparer.php" class="text-muted d-flex align-items-center justify-content-center me-3">
                        <span class="rounded-circle btn-md-square border"><i class="fas fa-random"></i></span>
                    </a>

                    <a href="favoris.php" class="text-muted d-flex align-items-center justify-content-center me-3">
                        <span class="rounded-circle btn-md-square border"><i class="fas fa-heart"></i></span>
                    </a>

                    <a href="panier.php" class="text-muted d-flex align-items-center justify-content-center text-decoration-none" title="Voir mon panier">
                        <span class="rounded-circle btn-md-square border bg-white position-relative shadow-sm">
                            <i class="fas fa-shopping-cart text-custom-orange"></i>
                            <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; <?= $nb_articles_panier == 0 ? 'display: none;' : ''; ?>">
                                <?= $nb_articles_panier; ?>
                            </span>
                        </span>
                        <span class="ms-3 d-flex flex-column text-start">
                            <small class="text-muted fw-semibold" style="font-size: 0.75rem;">Mon Panier</small>
                            <span id="cart-total" class="text-dark fw-bold font-monospace" style="font-size: 1.1rem;">
                                <?= number_format($montant_total_panier, 0, ',', ' '); ?> <small class="text-custom-orange fs-6">FCFA</small>
                            </span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar & Hero Start -->
    <div class="container-fluid nav-bar p-0">
        <div class="row gx-0 bg-primary px-5 align-items-center">
            <div class="col-lg-3 d-none d-lg-block">
                <nav class="navbar navbar-light position-relative" style="width: 250px;">
                    <button class="navbar-toggler border-0 fs-4 w-100 px-0 text-start" type="button"
                        data-bs-toggle="collapse" data-bs-target="#allCat">
                        <h4 class="m-0"><i class="fa fa-bars me-2"></i>Catégories</h4>
                    </button>
                    <div class="collapse navbar-collapse rounded-bottom" id="allCat">
                        <div class="navbar-nav ms-auto py-0 w-100">
                            <ul class="list-unstyled categories-bars w-100 mb-0">
                                <li>
                                    <div class="categories-bars-item <?= empty($_GET['id_cat']) ? 'active' : ''; ?>">
                                        <a href="index.php" class="fw-bold text-primary">Tous nos produits</a>
                                    </div>
                                </li>

                                <?php
                                // On récupère toutes les catégories et on compte le nombre de produits validés par catégorie
                                // (Assure-toi d'exécuter cette requête SQL en haut de ton script pour remplir $categories)
                                if (!empty($categories)):
                                    foreach ($categories as $cat):
                                        $activeClass = (isset($_GET['id_cat']) && $_GET['id_cat'] == $cat['id']) ? 'bg-light fw-bold' : '';
                                ?>
                                        <li>
                                            <div class="categories-bars-item d-flex justify-content-between align-items-center p-2 <?= $activeClass; ?>">
                                                <a href="index.php?id_cat=<?= $cat['id']; ?>" class="text-decoration-none text-dark">
                                                    <?= htmlspecialchars($cat['nom_categorie'] ?? $cat['nom']); ?>
                                                </a>
                                                <span class="badge bg-light text-muted border rounded-pill">(<?= $cat['total_produits'] ?? 0; ?>)</span>
                                            </div>
                                        </li>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="col-12 col-lg-9">
                <nav class="navbar navbar-expand-lg navbar-light bg-primary ">
                    <a href="" class="navbar-brand d-block d-lg-none">
                        <h1 class="display-5 text-secondary m-0"><i
                                class="fas fa-shopping-bag text-white me-2"></i>Electro</h1>
                        <img src="img/logo.png" alt="Logo">
                    </a>
                    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarCollapse">
                        <span class="fa fa-bars fa-1x"></span>
                    </button>
                    <div class="status-indicator">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <span class="dot bg-online"></span>
                            <span class="text-success">En ligne</span>
                        <?php else: ?>
                            <span class="dot bg-offline"></span>
                            <span class="text-danger">Déconnecté</span>
                        <?php endif; ?>
                    </div>
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <div class="navbar-nav ms-auto py-0">
                            <a href="index.php" class="nav-item nav-link active">Accueil</a>
                            <a href="panier.php" class="nav-item nav-link">Mon Panier</a>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                                <div class="dropdown-menu m-0">
                                    <a href="comparer.php" class="dropdown-item">Comparateur</a>
                                    <a href="favoris.php" class="dropdown-item">Mes favoris</a>
                                    <a href="commande.php" class="dropdown-item">Mes commandes</a>
                                </div>
                            </div>
                            <a href="contact.php" class="nav-item nav-link me-2">Contact</a>

                            <a href="tel:+2290199019259"
                                class="btn btn-outline-custom-orange rounded-pill py-2 px-4 shadow-sm d-inline-flex align-items-center fw-bold transition-all"
                                style="border-width: 2px; transition: all 0.3s ease;">
                                <i class="fa fa-mobile-alt me-2"></i> +229 01 99 01 92 59
                            </a>
                        </div>
                </nav>
            </div>
        </div>
    </div>
    <!-- Navbar & Hero End -->

    <!-- Carousel Start -->
    <div class="container-fluid carousel bg-light px-0">
        <div class="row g-0">
            <div class="col-12 col-lg-7 col-xl-9">
                <div class="header-carousel owl-carousel bg-light py-5">
                    <?php
                    if (!empty($produits)) {
                        $items = $produits;
                        shuffle($items);
                        $items = array_slice($items, 0, 5);

                        foreach ($items as $produit):
                            $nom = htmlspecialchars($produit['titre']);
                            $prix = number_format($produit['prix'], 0, ',', ' ');
                            $image = !empty($produit['image']) ? $produit['image'] : 'default.png';
                            $id = $produit['id'];
                    ?>
                            <div class="row g-0 header-carousel-item align-items-center">
                                <div class="col-xl-6 carousel-img wow fadeInLeft" data-wow-delay="0.1s">
                                    <img src="img/<?= $image; ?>" class="img-fluid w-100" alt="<?= $nom; ?>">
                                </div>
                                <div class="col-xl-6 carousel-content p-4">
                                    <h4 class="text-uppercase fw-bold mb-4 text-primary wow fadeInRight" data-wow-delay="0.1s" style="letter-spacing: 3px;">Top Sélection</h4>
                                    <h1 class="display-3 text-capitalize mb-4 wow fadeInRight" data-wow-delay="0.3s"><?= $nom; ?></h1>
                                    <p class="text-dark fs-4 fw-bold wow fadeInRight" data-wow-delay="0.5s"><?= $prix; ?> FCFA</p>
                                    <a class="btn btn-primary rounded-pill py-3 px-5 wow fadeInRight" data-wow-delay="0.7s" href="produit_details.php?id=<?= $id; ?>">Voir le produit</a>
                                </div>
                            </div>
                    <?php
                        endforeach;
                    } else {
                        echo '<p class="text-center">Aucun produit à afficher pour le moment.</p>';
                    }
                    ?>
                </div>
            </div>

            <div class="col-12 col-lg-5 col-xl-3 wow fadeInRight" data-wow-delay="0.1s">
                <div class="carousel-header-banner h-100 position-relative">
                    <img src="img/promo_banner.jpg" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Offre Spéciale">
                    <div class="col-12 col-lg-5 col-xl-3 wow fadeInRight" data-wow-delay="0.1s">
                        <div class="carousel-header-banner h-100 position-relative">

                            <div class="col-12 col-lg-5 col-xl-3 wow fadeInRight" data-wow-delay="0.1s">
                                <div style="position: absolute; bottom: 20px; left: 20px;">
                                    <h3 class="text-white">Qualité garantie</h3>
                                    <p class="text-white mb-0">Découvrez nos services au Bénin</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->

    <!-- Searvices Start -->
    <div class="container-fluid px-0">
        <div class="row g-0">
            <div class="col-6 col-md-4 col-lg-2 border-start border-end wow fadeInUp" data-wow-delay="0.1s">
                <div class="p-4">
                    <div class="d-inline-flex align-items-center">
                        <i class="fa fa-sync-alt fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Satisfait ou remboursé</h6>
                            <p class="mb-0">Un retour sur 30 jours est accepté</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.2s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fab fa-telegram-plane fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Livraison gratuite</h6>
                            <p class="mb-0">Minimum 25 000 FCFA d'achât</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.3s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-life-ring fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Support 24/7</h6>
                            <p class="mb-0">Nous supportons en ligne 24 heures sur 24</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.4s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-credit-card fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Recevez des cartes-cadeaux</h6>
                            <p class="mb-0">Jusqu'à 50 000 FCFA de crédit</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.5s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lock fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Payement sécurisé</h6>
                            <p class="mb-0">Mtn Momo, FedaPay, Carte de crédit</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.6s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-blog fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Service en ligne</h6>
                            <p class="mb-0">Pour toutes vos préoccupations</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Searvices End -->

    <!-- Products Offer Start -->
    <div class="container-fluid bg-light py-5">
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">

                <div class="container py-5">
                    <div class="row mb-4">
                        <div class="row mb-4">
                            <div class="col-12 wow fadeInLeft" data-wow-delay="0.1s">
                                <h2 class="fw-bold text-dark border-bottom pb-2 d-inline-block">
                                    <?php
                                    if ($id_cat > 0 && !empty($categories)) {
                                        // On cherche la catégorie actuelle dans le tableau pour extraire son nom
                                        $nom_page = "Catégorie";
                                        foreach ($categories as $cat) {
                                            if ($cat['id'] == $id_cat) {
                                                // Récupère 'nom' selon la structure de la base de données
                                                $nom_page = htmlspecialchars($cat['nom_categorie'] ?? $cat['nom']);
                                                break;
                                            }
                                        }
                                        echo $nom_page;
                                    } else {
                                        echo "Tous nos produits";
                                    }
                                    ?>
                                </h2>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <?php
                        if (!empty($produits)):
                            foreach ($produits as $index => $produit):
                                // Préparation des variables
                                $id = $produit['id'];
                                $nom = htmlspecialchars($produit['titre']);
                                $nom_url = urlencode($produit['titre']);
                                $prix = $produit['prix'];
                                $statut = $produit['statut'];
                                $image = !empty($produit['image']) ? $produit['image'] : 'default.png';

                                $delais = [0.1, 0.2, 0.3, 0.4];
                                $wow_delay = $delais[$index % 4] . 's';
                        ?>

                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <div class="card h-100 border shadow-sm rounded overflow-hidden product-item wow fadeInUp d-flex flex-column bg-white" data-wow-delay="<?= $wow_delay; ?>">

                                        <div class="position-relative overflow-hidden bg-light text-center product-item-inner-item" style="height: 220px;">
                                            <img src="img/<?= $image; ?>" class="img-fluid h-100 w-100 object-fit-cover" alt="<?= $nom; ?>">

                                            <div class="position-absolute top-0 start-0 m-2">
                                                <?php if ($statut == 'Sur commande'): ?>
                                                    <span class="badge bg-warning text-dark px-2 py-1 small shadow-sm">Sur commande</span>
                                                <?php elseif ($statut == 'En précommande'): ?>
                                                    <span class="badge bg-info text-white px-2 py-1 small shadow-sm">Précommande</span>
                                                <?php elseif ($statut == 'Rupture de stock'): ?>
                                                    <span class="badge bg-danger text-white px-2 py-1 small shadow-sm">Épuisé</span>
                                                <?php elseif ($statut == 'Presque épuisé'): ?>
                                                    <span class="badge px-2 py-1 small shadow-sm text-white" style="background-color: #fd7e14;">Presque épuisé 🔥</span>
                                                <?php elseif ($statut == 'En attente de validation'): ?>
                                                    <span class="badge bg-secondary text-white px-2 py-1 small shadow-sm">En attente</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success text-white px-2 py-1 small shadow-sm">Disponible</span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="product-details position-absolute top-50 start-50 translate-middle opacity-0 transition-all">
                                                <a href="produit_details.php?id=<?= $id; ?>" class="btn btn-light btn-sm shadow rounded-circle p-2">
                                                    <i class="fa fa-eye text-primary"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="card-body p-3 d-flex flex-column text-center justify-content-between flex-grow-1">
                                            <div>
                                                <span class="text-muted text-uppercase x-small d-block mb-1"><?= htmlspecialchars($produit['categorie'] ?? 'Produit'); ?></span>
                                                <a href="produit_details.php?id=<?= $id; ?>" class="h6 card-title text-decoration-none text-dark d-block text-truncate mb-2" title="<?= $nom; ?>">
                                                    <?= $nom; ?>
                                                </a>
                                            </div>
                                            <div class="mt-2">
                                                <span class="text-primary fs-5 fw-bold"><?= number_format($prix, 0, ',', ' '); ?> <small class="fs-6 fw-normal">FCFA</small></span>
                                            </div>
                                        </div>

                                        <div class="p-3 pt-0 bg-white border-top-0 mt-auto">
                                            <?php if ($statut == 'Rupture de stock'): ?>
                                                <button class="btn btn-outline-danger btn-sm rounded-pill w-100 mb-3 py-2" disabled>
                                                    <i class="fas fa-ban me-1"></i> Épuisé
                                                </button>

                                            <?php elseif ($statut == 'En attente de validation'): ?>
                                                <button class="btn btn-outline-secondary btn-sm rounded-pill w-100 mb-3 py-2" disabled>
                                                    <i class="fas fa-hourglass-half me-1"></i> En vérification
                                                </button>

                                            <?php else: ?>
                                                <a href="ajouter_panier.php?id=<?= $id; ?>&nom=<?= $nom_url; ?>&prix=<?= $prix; ?>&image=<?= urlencode($image); ?>"
                                                    class="btn btn-primary btn-sm rounded-pill w-100 mb-3 py-2 fw-semibold shadow-sm btn-add-cart">
                                                    <i class="fas fa-shopping-cart me-1"></i>
                                                    <?php
                                                    if ($statut == 'Sur commande' || $statut == 'En précommande') {
                                                        echo 'Réserver';
                                                    } elseif ($statut == 'Presque épuisé') {
                                                        echo 'Vite ! Acheter';
                                                    } else {
                                                        echo 'Ajouter';
                                                    }
                                                    ?>
                                                </a>
                                            <?php endif; ?>

                                            <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                                <div class="text-warning small">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <?php
                                                    $is_compare = isset($_SESSION['comparer']) && in_array($produit['id'], $_SESSION['comparer']);
                                                    ?>
                                                    <a href="ajouter_comparer.php?id=<?= $produit['id']; ?>"
                                                        class="btn btn-sm <?= $is_compare ? 'btn-custom-orange text-white' : 'btn-light border'; ?> p-1 rounded-circle btn-comparer ms-1"
                                                        title="Comparer ce produit">
                                                        <i class="fas fa-balance-scale small"></i>
                                                    </a>
                                                    <?php
                                                    // Sécurité : Vérifier si le produit est déjà dans les favoris de la session
                                                    $is_favori = isset($_SESSION['favoris']) && in_array($produit['id'], $_SESSION['favoris']);
                                                    ?>
                                                    <a href="ajouter_favoris.php?id=<?= $produit['id']; ?>"
                                                        class="text-danger btn btn-sm btn-light border p-1 rounded-circle btn-favoris"
                                                        title="<?= $is_favori ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>">
                                                        <i class="<?= $is_favori ? 'fas' : 'far'; ?> fa-heart small"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            <?php
                            endforeach;
                        else:
                            ?>
                            <div class="col-12 text-center py-5">
                                <div class="p-4 bg-light rounded shadow-sm d-inline-block">
                                    <p class="text-muted fs-5 mb-0">Aucun produit disponible pour le moment au Bénin.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Products Offer End -->

    <!-- Product Banner Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row g-4">

                <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                    <a href="boutique.php" class="text-decoration-none">
                        <div class="position-relative rounded overflow-hidden shadow-sm">
                            <img src="img/promo_1.jpg" class="img-fluid w-100" alt="Nos sélections" style="min-height: 300px; object-fit: cover;">

                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center p-4"
                                style="background: rgba(255, 255, 255, 0.8);">
                                <h3 class="display-6 fw-bold text-dark">Nos <br> <span class="text-custom-orange">Sélections</span></h3>
                                <p class="fs-5 text-muted mb-4">Le meilleur de nos articles au Bénin</p>
                                <span class="btn btn-outline-dark rounded-pill align-self-start py-2 px-4">Découvrir</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.2s">
                    <a href="commande.php" class="text-decoration-none">
                        <div class="text-center position-relative rounded overflow-hidden shadow-sm">
                            <img src="img/promo_2.jpg" class="img-fluid w-100" alt="Promotions" style="min-height: 300px; object-fit: cover;">

                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center p-4"
                                style="background: rgba(243, 139, 0, 0.7);">
                                <h2 class="display-3 text-white fw-bold">COMMANDES</h2>
                                <h4 class="text-white mb-4">Suivez l'état de vos commandes</h4>
                                <span class="btn btn-light rounded-pill py-2 px-4 fw-bold text-custom-orange">Voir mes commandes</span>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>
    <!-- Product Banner End -->

    <!-- Footer Start -->
    <div class="container-fluid footer py-5 bg-dark text-white">
        <div class="container py-4">
            <div class="row g-5">
                <div class="col-lg-4">
                    <h4 class="text-white mb-4">BéninMarché</h4>
                    <p class="text-muted mb-4">Votre marketplace de confiance au Bénin. Qualité, rapidité et service client irréprochable.</p>
                    <div class="position-relative">
                        <input class="form-control rounded-pill py-3 ps-4 pe-5" type="text" placeholder="Votre email...">
                        <button class="btn btn-custom-orange rounded-pill position-absolute top-0 end-0 py-2 mt-2 me-2">S'inscrire</button>
                    </div>
                </div>

                <div class="col-lg-4">
                    <h4 class="text-white mb-4">Navigation</h4>
                    <div class="row">
                        <div class="col-6">
                            <a href="#" class="footer-link">À propos</a> <br>
                            <a href="#" class="footer-link">Contact</a> <br>
                            <a href="#" class="footer-link">Politique de confidentialité</a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="footer-link">Mon compte</a> <br>
                            <a href="commande.php" class="footer-link">Suivi de commande</a> <br>
                            <a href="#" class="footer-link">FAQ</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <h4 class="text-white mb-4">Contact</h4>
                    <div class="contact-item mb-3"><i class="fas fa-map-marker-alt"></i> Cotonou, Bénin</div>
                    <div class="contact-item mb-3"><i class="fas fa-envelope"></i> contact@beninmarche.bj</div>
                    <div class="contact-item mb-3"><i class="fas fa-phone-alt"></i> +229 01 99 01 92 59</div>
                </div>
            </div>

            <hr class="my-5 border-secondary">

        </div>
    </div>
    <!-- Footer End -->


    <!-- Copyright Start -->
    <div class="container-fluid copyright py-4">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-6 text-center text-md-start mb-md-0">
                    <span class="text-white"><a href="#" class="border-bottom text-white"><i
                                class="fas fa-copyright text-light me-2"></i>BéninMarché</a>, Tous droits réservés.</span>
                </div>
                <div class="col-md-6 text-center text-md-end text-white">

                    <!--/*** This template is free as long as you keep the below author’s credit link/attribution link/backlink. ***/-->
                    <!--/*** If you'd like to use the template without the below author’s credit link/attribution link/backlink, ***/-->
                    <!--/*** you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". ***/-->
                    Designed By <a class="border-bottom text-white" href="https://htmlcodex.com">HTML Codex</a>.
                    Distributed By <a class="border-bottom text-white" href="https://themewagon.com">ThemeWagon</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>


    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
        <div id="bm-toast" class="toast align-items-center text-white border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" style="background-color: #f38b00; transition: opacity 0.3s ease, transform 0.3s ease; display: none; opacity: 0; transform: translateY(-20px);">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle fs-5"></i>
                    <span id="toast-message">Produit ajouté au panier !</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="masquerToast()"></button>
            </div>
        </div>
    </div>

    <script>
        // Fonction pour afficher le Toast de manière fluide
        function afficherToast(message) {
            const toast = document.getElementById('bm-toast');
            const toastMessage = document.getElementById('toast-message');

            toastMessage.textContent = message;
            toast.style.display = 'block';

            // Forcer le reflow pour l'animation CSS
            toast.offsetHeight;

            // Appliquer les styles d'apparition
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';

            // Disparition automatique après 3 secondes
            setTimeout(masquerToast, 3000);
        }

        function masquerToast() {
            const toast = document.getElementById('bm-toast');
            if (!toast) return;

            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';

            // Attendre la fin de la transition CSS pour masquer l'élément du DOM
            setTimeout(() => {
                if (toast.style.opacity === '0') {
                    toast.style.display = 'none';
                }
            }, 300);
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Intercepter le clic sur les boutons d'ajout
            const boutonsAjout = document.querySelectorAll('a[href^="ajouter_panier.php"]');

            boutonsAjout.forEach(bouton => {
                bouton.addEventListener('click', function(e) {
                    e.preventDefault(); // 🛑 Bloque la redirection brute

                    const urlCible = this.getAttribute('href');

                    // 🎯 AJOUT DE L'EN-TÊTE AJAX : On dit explicitement au PHP qu'on est en Fetch/AJAX
                    fetch(urlCible, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Erreur réseau');
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                // 🎉 Déclenche la notification stylisée orange
                                afficherToast("L'article a bien été ajouté à votre panier BéninMarché !");
                            } else {
                                alert(data.message || "Erreur lors de l'ajout.");
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert("Une erreur est survenue lors de la communication avec le serveur.");
                        });
                });
            });
        });

        // ==========================================
        // 🎯 GESTION DES FAVORIS VIA AJAX
        // ==========================================
        const boutonsFavoris = document.querySelectorAll('.btn-favoris');

        boutonsFavoris.forEach(bouton => {
            bouton.addEventListener('click', function(e) {
                e.preventDefault(); // 🛑 Bloque la redirection brute

                const urlCible = this.getAttribute('href');
                const iconeCoeur = this.querySelector('i');
                const lien = this;

                fetch(urlCible, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // On déclenche le Toast orange pour avertir l'utilisateur
                            afficherToast(data.message);

                            // On change le design du cœur dynamiquement
                            if (data.action === 'added') {
                                iconeCoeur.classList.remove('far');
                                iconeCoeur.classList.add('fas'); // Devient plein
                                lien.setAttribute('title', 'Retirer des favoris');
                            } else {
                                iconeCoeur.classList.remove('fas');
                                iconeCoeur.classList.add('far'); // Devient vide
                                lien.setAttribute('title', 'Ajouter aux favoris');
                            }
                        } else {
                            alert(data.message || "Erreur lors de la modification des favoris.");
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                    });
            });
        });

        // ==========================================
        // 🎯 GESTION DE LA COMPARAISON VIA AJAX
        // ==========================================
        const boutonsComparer = document.querySelectorAll('.btn-comparer');

        boutonsComparer.forEach(bouton => {
            bouton.addEventListener('click', function(e) {
                e.preventDefault(); // 🛑 Bloque le rechargement brutal de la page

                const urlCible = this.getAttribute('href');
                const icone = this.querySelector('i');
                const lien = this;

                fetch(urlCible, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // On affiche le message de confirmation dans le Toast orange
                            afficherToast(data.message);

                            // On change le style du bouton dynamiquement
                            if (data.action === 'added') {
                                // Le produit vient d'être ajouté au comparateur
                                lien.classList.remove('btn-light', 'border');
                                lien.classList.add('btn-custom-orange', 'text-white');
                            } else {
                                // Le produit vient d'être retiré
                                lien.classList.remove('btn-custom-orange', 'text-white');
                                lien.classList.add('btn-light', 'border');
                            }
                        } else if (data.status === 'error') {
                            // Alerte si l'utilisateur dépasse la limite stricte de 3 produits
                            afficherToast(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la comparaison:', error);
                    });
            });
        });

        const searchInput = document.getElementById('search-input');
        const resultsDiv = document.getElementById('search-results');
        const searchCat = document.getElementById('search-cat');

        searchInput.addEventListener('input', function() {
            const q = this.value;
            if (q.length < 2) {
                resultsDiv.style.display = 'none';
                return;
            }

            fetch(`recherche.php?q=${encodeURIComponent(q)}&cat=${searchCat.value}`)
                .then(res => res.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    if (data.length > 0) {
                        // ... dans ton fetch ...
                        data.forEach(prod => {
                            const item = document.createElement('a');
                            item.className = 'search-item text-decoration-none text-dark d-block border-bottom';
                            item.textContent = prod.titre;
                            item.href = `produit_details.php?id=${prod.id}`;
                            resultsDiv.appendChild(item);
                        });
                        resultsDiv.style.display = 'block';
                    } else {
                        resultsDiv.style.display = 'none';
                    }
                });
        });

        // Fermer les résultats si on clique ailleurs
        document.addEventListener('click', (e) => {
            if (!resultsDiv.contains(e.target)) resultsDiv.style.display = 'none';
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addCartButtons = document.querySelectorAll('.btn-add-cart');

            addCartButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); // Empêche le rechargement de la page
                    const url = this.getAttribute('href'); // Récupère le lien ajouter_panier.php...

                    // Appel AJAX
                    fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest' // Indique au serveur que c'est de l'AJAX
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // 1. Mettre à jour le badge rouge (nombre d'articles)
                                const badge = document.getElementById('cart-badge');
                                badge.textContent = data.count;
                                badge.style.display = 'inline-block'; // L'afficher s'il était caché

                                // 2. Mettre à jour le prix total
                                const totalEl = document.getElementById('cart-total');
                                totalEl.innerHTML = data.total_formate + ' <small class="text-custom-orange fs-6">FCFA</small>';

                                // Optionnel : Petite animation pour montrer que ça a marché
                                const cartIcon = document.querySelector('.fa-shopping-cart');
                                cartIcon.classList.add('fa-bounce');
                                setTimeout(() => cartIcon.classList.remove('fa-bounce'), 1000);
                            }
                        })
                        .catch(error => console.error('Erreur lors de l\'ajout au panier :', error));
                });
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>

    <script>
        new WOW().init();
    </script>
</body>

</html>