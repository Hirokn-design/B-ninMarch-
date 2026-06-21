<?php
session_start();
require_once '../AdminLTE-master/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit();
}

try {
    $pdo->beginTransaction();

    // 1. Calcul du montant total
    $total_general = 0;
    foreach ($_SESSION['panier'] as $item) {
        $total_general += ($item['prix'] * $item['quantite']);
    }

    // 2. Insertion dans la table 'commandes'
    // Utilisation des colonnes de votre schéma : id_client, adresse_livraison, montant_total
    $sql_cmd = "INSERT INTO commandes (id_client, adresse_livraison, montant_total, statut, date_commande) 
                VALUES (?, ?, ?, 'en_attente', NOW())";

    $stmt_cmd = $pdo->prepare($sql_cmd);
    $stmt_cmd->execute([
        $_SESSION['user_id'],
        $_POST['adresse_livraison'],
        $total_general
    ]);

    $id_commande = $pdo->lastInsertId();

    // 3. Insertion dans 'lignes_commandes' et mise à jour stock
    foreach ($_SESSION['panier'] as $id_prod => $item) {
        // Insertion ligne commande
        $stmt_lignes = $pdo->prepare("INSERT INTO lignes_commande (id_commande, id_produit, quantite, prix_unitaire) 
                                      VALUES (?, ?, ?, ?)");
        $stmt_lignes->execute([$id_commande, $id_prod, $item['quantite'], $item['prix']]);

        // Mise à jour du stock produit
        $stmt_stock = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");
        $stmt_stock->execute([$item['quantite'], $id_prod]);
    }

    $pdo->commit();
    unset($_SESSION['panier']);
    header('Location: confirmation.php?id=' . $id_commande);
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    die("Erreur lors de la validation : " . $e->getMessage());
}
