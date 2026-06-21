<?php
// Désactiver l'affichage des erreurs pour garantir un JSON valide
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require_once '../AdminLTE-master/config/db.php';

$terme = isset($_GET['q']) ? trim($_GET['q']) : '';
$cat_id = isset($_GET['cat']) ? $_GET['cat'] : 'All';

$resultats = []; // Tableau vide par défaut

if (strlen($terme) >= 2) {
    // Utilisation de id_categorie comme tu l'as précisé
    $sql = "SELECT id, titre FROM produits WHERE titre LIKE :terme";
    if ($cat_id !== 'All') {
        $sql .= " AND id_categorie = :cat_id";
    }
    $sql .= " LIMIT 5";

    $stmt = $pdo->prepare($sql);
    $params = ['terme' => "%$terme%"];
    if ($cat_id !== 'All') {
        $params['cat_id'] = $cat_id;
    }

    $stmt->execute($params);
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Si aucun résultat n'est trouvé, on remplit le tableau avec un message
if (empty($resultats)) {
    $resultats = [['id' => 0, 'titre' => 'Aucun produit trouvé à ce nom']];
}

echo json_encode($resultats);
exit();
