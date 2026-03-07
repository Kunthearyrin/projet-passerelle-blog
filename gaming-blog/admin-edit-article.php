<?php
require_once('src/admin-check.php');

// Récupérer l'ID de l'article
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

// Traitement du formulaire de modification
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $title = htmlspecialchars(trim($_POST['title']));
    $content = htmlspecialchars(trim($_POST['content']));
    $category = htmlspecialchars(trim($_POST['category']));
    $image = $article['image']; // Garder l'ancienne image par défaut

    // Validation
    if(empty($title) || empty($content)) {
        $error = "Le titre et le contenu sont obligatoires.";
    } else {
        
        // Gestion de l'upload d'une nouvelle image
        if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024;

            if(in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= $max_size) {
                $upload_dir = 'uploads/articles/';
                
                if(!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $filepath = $upload_dir . $filename;

                if(move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                    // Supprimer l'ancienne image si elle existe
                    if(!empty($article['image']) && file_exists($article['image'])) {
                        unlink($article['image']);
                    }
                    $image = $filepath;
                } else {
                    $error = "Erreur lors de l'upload de l'image.";
                }
            } else {
                $error = "Format d'image invalide ou fichier trop volumineux (max 5 MB).";
            }
        }

        // Mettre à jour l'article si pas d'erreur
        if(!isset($error)) {
            try {
                $stmt = $db->prepare('
                    UPDATE articles 
                    SET title = ?, content = ?, image = ?, category = ?, updated_at = NOW() 
                    WHERE id = ?
                ');
                $stmt->execute([$title, $content, $image, $category, $article_id]);

                header('Location: admin.php?success=updated');
                exit();

            } catch(PDOException $e) {
                $error = "Erreur lors de la modification.";
                error_log($e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'article</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"/>
    <script src="https://kit.fontawesome.com/67b2bbd532.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="design/css/admin.css" />
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">
                <i class="fas fa-edit"></i> Modifier l'article
            </span>
            <a href="admin.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title mb-4">
                            <i class="fas fa-pen"></i> Modifier : <?php echo htmlspecialchars($article['title']); ?>
                        </h2>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <!-- Titre -->
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    Titre de l'article <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($article['title']); ?>" required>
                            </div>

                            <!-- Catégorie -->
                            <div class="mb-3">
                                <label for="category" class="form-label">Catégorie</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">Sans catégorie</option>
                                    <option value="Action" <?php echo ($article['category'] === 'Action') ? 'selected' : ''; ?>>Action</option>
                                    <option value="RPG" <?php echo ($article['category'] === 'RPG') ? 'selected' : ''; ?>>RPG</option>
                                    <option value="Aventure" <?php echo ($article['category'] === 'Aventure') ? 'selected' : ''; ?>>Aventure</option>
                                    <option value="Sport" <?php echo ($article['category'] === 'Sport') ? 'selected' : ''; ?>>Sport</option>
                                    <option value="Stratégie" <?php echo ($article['category'] === 'Stratégie') ? 'selected' : ''; ?>>Stratégie</option>
                                    <option value="Simulation" <?php echo ($article['category'] === 'Simulation') ? 'selected' : ''; ?>>Simulation</option>
                                    <option value="FPS" <?php echo ($article['category'] === 'FPS') ? 'selected' : ''; ?>>FPS</option>
                                    <option value="Horreur" <?php echo ($article['category'] === 'Horreur') ? 'selected' : ''; ?>>Horreur</option>
                                </select>
                            </div>

                            <!-- Image actuelle -->
                            <?php if(!empty($article['image'])): ?>
                                <div class="mb-3">
                                    <label class="form-label">Image actuelle :</label><br>
                                    <img src="<?php echo htmlspecialchars($article['image']); ?>" 
                                         alt="Image actuelle" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            <?php endif; ?>

                            <!-- Nouvelle image -->
                            <div class="mb-3">
                                <label for="image" class="form-label">Nouvelle image (optionnel)</label>
                                <input type="file" class="form-control" id="image" name="image" 
                                       accept="image/jpeg,image/png,image/gif,image/webp">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Laissez vide pour garder l'image actuelle
                                </small>
                            </div>

                            <!-- Contenu -->
                            <div class="mb-3">
                                <label for="content" class="form-label">
                                    Contenu de l'article <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="content" name="content" 
                                          rows="15" required><?php echo htmlspecialchars($article['content']); ?></textarea>
                            </div>

                            <!-- Boutons -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="admin.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
</body>
</html>