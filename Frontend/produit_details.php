<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../AdminLTE-master/config/db.php'; // Connexion PDO

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit();
}

// 2. RÉCUPÉRATION DU PRODUIT + VENDEUR + EMAIL UTILISATEUR
// Jointure triple : produits -> vendeurs -> utilisateur
$stmt = $pdo->prepare("
    SELECT p.*, 
           v.boutique, v.region, v.telephone, v.description AS desc_vendeur,
           u.email 
    FROM produits p 
    LEFT JOIN vendeurs v ON p.id_vendeur = v.id 
    LEFT JOIN utilisateurs u ON v.id_utilisateur = u.id 
    WHERE p.id = :id
");
$stmt->execute(['id' => $id]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    header('Location: index.php');
    exit();
}

$is_favori = isset($_SESSION['favoris']) && in_array($id, $_SESSION['favoris']);
$is_compare = isset($_SESSION['comparer']) && in_array($id, $_SESSION['comparer']);
$image_principale = !empty($produit['image']) ? $produit['image'] : 'default.png';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($produit['titre']); ?> - BéninMarché</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .text-custom-orange {
            color: #f38b00;
        }

        .btn-custom-orange {
            background-color: #f38b00;
            border-color: #f38b00;
            color: white;
        }

        .img-detail-wrapper {
            border-radius: 1rem;
            border: 1px solid #e9ecef;
        }
    </style>
</head>

<body class="bg-light">

    <?php include_once 'navbar.php'; ?>
    
    <div class="container my-5" style="min-height: 75vh;">
        <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
            <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="fas fa-arrow-left"></i></a>
            <h2 class="fw-bold text-dark m-0">Détails du Produit <i class="fas fa-info-circle text-custom-orange ms-2"></i></h2>
        </div>
        <div class="container my-5">
            <div class="row g-5 bg-white p-4 rounded-4 shadow-sm border">
                <div class="col-md-5">
                    <div class="img-detail-wrapper bg-light d-flex justify-content-center align-items-center" style="height: 400px;">
                        <img src="img/<?= $image_principale; ?>" class="img-fluid object-fit-cover h-100 w-100" alt="<?= htmlspecialchars($produit['titre']); ?>">
                    </div>
                </div>

                <div class="col-md-7">
                    <h1 class="fw-bold text-dark"><?= htmlspecialchars($produit['titre']); ?></h1>
                    <div class="mb-3">
                        <span class="text-custom-orange fw-bolder fs-2 font-monospace"><?= number_format($produit['prix'], 0, ',', ' '); ?> FCFA</span>
                    </div>

                    <p class="text-muted"><?= nl2br(htmlspecialchars($produit['description'])); ?></p>

                    <div class="card bg-light border-0 my-4 p-3 rounded-4">
                        <h6 class="fw-bold mb-3"><i class="fas fa-store text-custom-orange me-2"></i> Informations Vendeur</h6>
                        <p class="mb-1"><strong>Boutique :</strong> <?= htmlspecialchars($produit['boutique'] ?? 'N/A'); ?></p>
                        <p class="mb-1"><strong>Région :</strong> <?= htmlspecialchars($produit['region'] ?? 'N/A'); ?></p>
                        <p class="mb-1">
                            <strong>Contact :</strong>
                            <a href="tel:<?= htmlspecialchars($produit['telephone'] ?? '#'); ?>"><?= htmlspecialchars($produit['telephone'] ?? 'N/A'); ?></a>
                        </p>
                        <p class="mb-0">
                            <strong>Email :</strong>
                            <a href="mailto:<?= htmlspecialchars($produit['email'] ?? '#'); ?>"><?= htmlspecialchars($produit['email'] ?? 'Non disponible'); ?></a>
                        </p>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="ajouter_panier.php?id=<?= $produit['id']; ?>&nom=<?= urlencode($produit['titre']); ?>&prix=<?= $produit['prix']; ?>&image=<?= urlencode($image_principale); ?>"
                            class="btn btn-lg btn-custom-orange rounded-pill px-5">Ajouter au panier</a>
                        <a href="ajouter_favoris.php?id=<?= $produit['id']; ?>" class="btn btn-lg <?= $is_favori ? 'btn-danger' : 'btn-light border'; ?> rounded-circle"><i class="fa-heart <?= $is_favori ? 'fas' : 'far'; ?>"></i></a>
                    </div>
                </div>
            </div>
        </div>
</body>

</html>