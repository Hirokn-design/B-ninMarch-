<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Calcul rapide des totaux pour les badges
$nb_panier = isset($_SESSION['panier']) ? array_sum(array_column($_SESSION['panier'], 'quantite')) : 0;
$nb_favoris = isset($_SESSION['favoris']) ? count($_SESSION['favoris']) : 0;
?>

<style>
    /* --- Styles sur-mesure pour la Navbar --- */
    .navbar-bm .nav-link {
        font-weight: 500;
        color: #495057;
        transition: color 0.3s ease-in-out;
    }

    .navbar-bm .nav-link:hover,
    .navbar-bm .nav-link.active {
        color: #f38b00 !important;
        /* Orange BéninMarché */
    }

    .btn-outline-account {
        color: #495057;
        border: 1px solid #ced4da;
        background: transparent;
        transition: all 0.3s ease;
    }

    .btn-outline-account:hover {
        color: #f38b00;
        border-color: #f38b00;
        background-color: #fffaf0;
        /* Fond très légèrement orangé */
    }

    .custom-dropdown {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        padding: 0.5rem 0;
    }

    .custom-dropdown .dropdown-item {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        transition: background-color 0.2s;
    }

    .custom-dropdown .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #f38b00;
    }

    /* Effet de rebond discret au survol du logo */
    .brand-logo-hover {
        transition: transform 0.3s ease;
    }

    .navbar-brand:hover .brand-logo-hover {
        transform: scale(1.05);
    }
</style>

<div class="container-fluid px-5 py-2 shadow-sm bg-white sticky-top" style="z-index: 1000;">
    <nav class="navbar navbar-expand-lg navbar-light p-0 navbar-bm">

        <a href="index.php" class="navbar-brand d-flex align-items-center text-decoration-none">
            <img src="img/logo.png" alt="Logo BéninMarché" class="me-2 brand-logo-hover rounded-circle border border-2 border-custom-orange" style="width: 45px; height: 45px; object-fit: cover;">
            <h3 class="text-custom-orange fw-bolder m-0" style="letter-spacing: -0.5px;">BéninMarché</h3>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto align-items-center gap-2 gap-lg-4">

                <div class="d-flex flex-column flex-lg-row gap-2 gap-lg-3 me-lg-3">
                    <a href="index.php" class="nav-item nav-link active px-0">Accueil</a>
                    <a href="boutique.php" class="nav-item nav-link px-0">Boutique</a>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <a href="favoris.php" class="nav-item nav-link position-relative px-1" title="Mes Favoris">
                        <i class="fas fa-heart fs-5 text-danger"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-2 border-white" style="font-size: 0.6rem; transform: translate(-30%, -30%) !important;">
                            <?= $nb_favoris; ?>
                        </span>
                    </a>

                    <a href="panier.php" class="nav-item nav-link position-relative px-1" title="Mon Panier">
                        <i class="fas fa-shopping-cart fs-5 text-dark"></i>
                        <span id="nav-cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-custom-orange border border-2 border-white" style="font-size: 0.6rem; transform: translate(-30%, -30%) !important;">
                            <?= $nb_panier; ?>
                        </span>
                    </a>
                </div>

                <div class="dropdown ms-lg-2 mt-3 mt-lg-0">
                    <button class="btn btn-outline-account btn-sm rounded-pill px-3 py-2 fw-semibold dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-user-circle fs-5 me-2"></i> Mon compte
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end custom-dropdown mt-2">
                        <li><a class="dropdown-item" href="panier.php"><i class="fas fa-shopping-basket me-2 text-muted"></i> Mon panier</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-box-open me-2 text-muted"></i> Mes commandes</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2 text-muted"></i> Paramètres</a></li>
                        <li>
                            <hr class="dropdown-divider opacity-25">
                        </li>
                        <li><a class="dropdown-item text-danger fw-bold" href="#"><i class="fas fa-sign-out-alt me-2"></i> Déconnexion</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </nav>
</div>