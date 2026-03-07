<?php
require_once('src/admin-check.php');

// Vérifier l'ID
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: admin.php?error=1&message=ID d\'article manquant');
    exit();
}

$article_id = (int)$_GET['id'];

// Récupérer l'article
$stmt = $db->prepare('SELECT * FROM articles WHERE id = ?');
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if(!$article) {
    header('Location: admin.php?error=1&message=Article introuvable');
    exit();
}

// Supprimer l'image 
if(!empty($article['image']) && file_exists($article['image'])) {
    unlink($article['image']);
}

// Supprimer l'article
try {
    $stmt = $db->prepare('DELETE FROM articles WHERE id = ?');
    $stmt->execute([$article_id]);

    header('Location: admin.php?success=deleted');
    exit();

} catch(PDOException $e) {
    header('Location: admin.php?error=1&message=' . urlencode('Erreur lors de la suppression'));
    error_log($e->getMessage());
    exit();
}
?>