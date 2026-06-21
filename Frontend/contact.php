<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Inclusion de la connexion BDD (nécessaire pour la navbar dynamique)
require_once '../AdminLTE-master/config/db.php'; 
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Contact - BéninMarché</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="contact, béninmarché, e-commerce, produits locaux" name="keywords">
    <meta content="Contactez BéninMarché pour toute question ou assistance." name="description">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/style.css" rel="stylesheet">

    <style>
        .text-custom-orange { color: #f38b00 !important; }
        .bg-custom-orange { background-color: #f38b00 !important; }
        .btn-custom-orange { background-color: #f38b00; border-color: #f38b00; color: white; }
        .btn-custom-orange:hover { background-color: #d77a00; border-color: #d77a00; color: white; }
        .border-custom-orange { border-color: #f38b00 !important; }
        
        /* Personnalisation de l'entête de page */
        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('img/contact-bg.jpg') center center no-repeat;
            background-size: cover;
        }
    </style>
</head>

<body class="bg-light">

    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-custom-orange" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Chargement...</span>
        </div>
    </div>
    <?php include_once 'navbar.php'; ?>

    <div class="container-fluid page-header py-5 mb-5 bg-dark">
        <h1 class="text-center text-white display-5 fw-bold wow fadeInUp" data-wow-delay="0.1s">Contactez-nous</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="index.php" class="text-white text-decoration-none">Accueil</a></li>
            <li class="breadcrumb-item active text-custom-orange">Contact</li>
        </ol>
    </div>
    <div class="container-fluid contact pb-5">
        <div class="container py-2">
            <div class="p-5 bg-white shadow-sm rounded-4 border">
                <div class="row g-4">
                    
                    <div class="col-12">
                        <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 900px;">
                            <h4 class="text-custom-orange border-bottom border-custom-orange border-2 d-inline-block pb-2">Gardons le contact</h4>
                            <p class="mb-5 fs-5 text-muted">Notre équipe est à votre disposition pour vous accompagner, répondre à vos questions et vous aider à trouver les meilleurs produits locaux.</p>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <h5 class="text-custom-orange wow fadeInUp" data-wow-delay="0.1s">Discutons de vos besoins</h5>
                        <h1 class="display-6 mb-4 fw-bold wow fadeInUp" data-wow-delay="0.3s">Envoyez-nous un message</h1>
                        <p class="mb-4 text-muted wow fadeInUp" data-wow-delay="0.5s">Remplissez ce formulaire et notre service client vous répondra dans les plus brefs délais. Que vous soyez acheteur ou vendeur, nous sommes à votre écoute.</p>
                        
                        <form action="traitement_contact.php" method="POST">
                            <div class="row g-4 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control rounded-3" id="name" name="nom" placeholder="Votre Nom" required>
                                        <label for="name">Votre Nom</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control rounded-3" id="email" name="email" placeholder="Votre Email" required>
                                        <label for="email">Votre Email</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control rounded-3" id="phone" name="telephone" placeholder="Téléphone">
                                        <label for="phone">Numéro de Téléphone</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control rounded-3" id="subject" name="sujet" placeholder="Sujet" required>
                                        <label for="subject">Sujet</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control rounded-3" name="message" placeholder="Laissez un message ici" id="message" style="height: 160px" required></textarea>
                                        <label for="message">Votre Message</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-custom-orange rounded-pill w-100 py-3 fw-bold fs-5 shadow-sm">Envoyer le message <i class="fas fa-paper-plane ms-2"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="h-100 rounded-4 overflow-hidden shadow-sm border">
                            <iframe class="w-100" style="height: 100%; min-height: 400px;"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126839.06019385312!2d2.3486326079998826!3d6.368028784347781!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1023543ac8571545%3A0x63ebcc60067a9446!2sCotonou%2C%20B%C3%A9nin!5e0!3m2!1sfr!2sbj!4v1680000000000!5m2!1sfr!2sbj"
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>

                    <div class="col-lg-12 mt-5">
                        <div class="row g-4 align-items-center justify-content-center">
                            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="bg-light rounded-4 p-4 text-center border h-100">
                                    <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px;">
                                        <i class="fas fa-map-marker-alt fa-2x text-custom-orange"></i>
                                    </div>
                                    <h5 class="fw-bold">Adresse</h5>
                                    <p class="mb-0 text-muted">Cotonou, Littoral<br>Bénin</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.3s">
                                <div class="bg-light rounded-4 p-4 text-center border h-100">
                                    <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px;">
                                        <i class="fas fa-envelope fa-2x text-custom-orange"></i>
                                    </div>
                                    <h5 class="fw-bold">Email</h5>
                                    <p class="mb-0"><a href="mailto:contact@beninmarche.com" class="text-muted text-decoration-none">contact@beninmarche.com</a></p>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.5s">
                                <div class="bg-light rounded-4 p-4 text-center border h-100">
                                    <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px;">
                                        <i class="fa fa-phone-alt fa-2x text-custom-orange"></i>
                                    </div>
                                    <h5 class="fw-bold">Téléphone</h5>
                                    <p class="mb-0"><a href="tel:+22999019259" class="text-muted text-decoration-none">(+229) 99 01 92 59</a></p>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.7s">
                                <div class="bg-light rounded-4 p-4 text-center border h-100">
                                    <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px;">
                                        <i class="fab fa-chrome fa-2x text-custom-orange"></i>
                                    </div>
                                    <h5 class="fw-bold">Site Web</h5>
                                    <p class="mb-0"><a href="index.php" class="text-muted text-decoration-none">www.beninmarche.com</a></p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid footer bg-dark py-5 wow fadeIn" data-wow-delay="0.2s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-md-6 col-lg-4">
                    <h3 class="text-custom-orange fw-bold mb-4">BéninMarché</h3>
                    <p class="text-light mb-4">La meilleure plateforme pour découvrir, acheter et vendre des produits 100% locaux au Bénin. Qualité, authenticité et soutien à l'artisanat local.</p>
                    <div class="d-flex gap-2">
                        <a class="btn btn-outline-light btn-social rounded-circle" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social rounded-circle" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social rounded-circle" href=""><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <h5 class="text-white mb-4">Liens Utiles</h5>
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-light mb-2 text-decoration-none" href="index.php"><i class="fas fa-angle-right text-custom-orange me-2"></i>Accueil</a>
                        <a class="text-light mb-2 text-decoration-none" href="boutique.php"><i class="fas fa-angle-right text-custom-orange me-2"></i>Notre Boutique</a>
                        <a class="text-light mb-2 text-decoration-none" href="panier.php"><i class="fas fa-angle-right text-custom-orange me-2"></i>Mon Panier</a>
                        <a class="text-light mb-2 text-decoration-none" href="contact.php"><i class="fas fa-angle-right text-custom-orange me-2"></i>Nous Contacter</a>
                    </div>
                </div>
                <div class="col-md-12 col-lg-4">
                    <h5 class="text-white mb-4">Newsletter</h5>
                    <p class="text-light">Abonnez-vous pour recevoir les dernières nouveautés et promotions de nos artisans locaux.</p>
                    <div class="position-relative w-100 mt-3">
                        <input class="form-control border-0 rounded-pill w-100 ps-4 pe-5 py-3" type="text" placeholder="Votre email">
                        <button type="button" class="btn btn-custom-orange rounded-pill position-absolute top-0 end-0 py-2 px-4 mt-2 me-2">S'abonner</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid copyright bg-dark text-light border-top border-secondary py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <a class="text-custom-orange text-decoration-none" href="#">BéninMarché</a>, Tous droits réservés.
                </div>
            </div>
        </div>
    </div>
    <a href="#" class="btn btn-custom-orange btn-lg-square rounded-circle back-to-top text-white"><i class="fa fa-arrow-up"></i></a>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <script src="js/main.js"></script>
</body>

</html>