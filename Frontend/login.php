<?php
session_start();
require_once '../AdminLTE-master/config/db.php';

$error = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Recherche de l'utilisateur par email
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Vérification du mot de passe
    // NOTE : Assurez-vous que vos mots de passe en BDD sont hachés avec password_hash()
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        // Connexion réussie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_role'] = $user['role'];

        // Redirection : vers la page de commande si elle était en attente, sinon index.php
        $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'index.php';
        unset($_SESSION['redirect_after_login']);

        header("Location: $redirect");
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion - BéninMarché</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
        <div class="card shadow-sm border-0 p-4" style="width: 100%; max-width: 400px;">
            <h3 class="text-center fw-bold mb-4">Connexion</h3>

            <?php if ($error): ?>
                <div class="alert alert-danger text-center small"><?= $error; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-custom-orange w-100 py-2 fw-bold">Se connecter</button>
            </form>

            <div class="text-center mt-3 small">
                Pas encore de compte ? <a href="register.php" class="text-decoration-none">S'inscrire</a>
            </div>
        </div>
    </div>

</body>

</html>