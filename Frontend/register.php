<?php
session_start();
require_once '../AdminLTE-master/config/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Vérification si l'email existe déjà
    $stmt_check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt_check->execute([$email]);

    if ($stmt_check->rowCount() > 0) {
        $message = "<div class='alert alert-danger'>Cet email est déjà utilisé.</div>";
    } else {
        // Hachage du mot de passe (Crucial pour la sécurité)
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insertion dans la base de données
        $stmt_insert = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, 'client')");

        if ($stmt_insert->execute([$nom, $prenom, $email, $password_hash])) {
            $message = "<div class='alert alert-success'>Compte créé avec succès ! <a href='login.php'>Connectez-vous ici</a>.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Erreur lors de l'inscription. Veuillez réessayer.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Inscription - BéninMarché</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <div class="card shadow-sm border-0 p-4" style="width: 100%; max-width: 450px;">
            <h3 class="text-center fw-bold mb-4">Créer un compte</h3>

            <?= $message; ?>

            <form action="register.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-custom-orange w-100 py-2 fw-bold">S'inscrire</button>
            </form>

            <div class="text-center mt-3 small">
                Déjà inscrit ? <a href="login.php" class="text-decoration-none">Se connecter</a>
            </div>
        </div>
    </div>

</body>

</html>