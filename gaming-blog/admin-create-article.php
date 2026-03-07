<?php
require_once('src/admin-check.php');

// Traitement du formulaire
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $title = htmlspecialchars(trim($_POST['title']));
    $content = htmlspecialchars(trim($_POST['content']));
    $category = htmlspecialchars(trim($_POST['category']));
    $image = '';

    // Validation
    if(empty($title) || empty($content)) {
        $error = "Le titre et le contenu sont obligatoires.";
    } else {
        
        // Gestion d'upload de l'image
        if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5 MB

            if(in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= $max_size) {
                $upload_dir = 'uploads/articles/';
                
                // Créer le dossier s'il n'existe pas
                if(!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $filepath = $upload_dir . $filename;

                if(move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                    $image = $filepath;
                } else {
                    $error = "Erreur lors de l'upload de l'image.";
                }
            } else {
                $error = "Format d'image invalide ou fichier trop volumineux (max 5 MB).";
            }
        }

        // Insérer l'article si pas d'erreur
        if(!isset($error)) {
            try {
                $stmt = $db->prepare('
                    INSERT INTO articles (title, content, image, category, created_at) 
                    VALUES (?, ?, ?, ?, NOW())
                ');
                $stmt->execute([$title, $content, $image, $category]);

                header('Location: admin.php?success=created');
                exit();

            } catch(PDOException $e) {
                $error = "Erreur lors de la création de l'article.";
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
    <link rel="shortcut icon" href="images/manette-de-jeu.png" type="images.png"/>
    <title>Créer un article</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"/>
    <script src="https://kit.fontawesome.com/67b2bbd532.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="design/css/admin.css" />
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-pink">
        <div class="container-fluid">
            <span class="navbar-brand">
                <i class="fas fa-plus-circle"></i> Créer un article
            </span>
            <a href="admin.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container mt-4 mb-5"> 
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title mb-4">
                            <i class="fas fa-pen"></i> Nouvel article
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
                                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" 
                                       required placeholder="Ex: Elden Ring">
                            </div>

                            <!-- Catégorie -->
                            <div class="mb-3">
                                <label for="category" class="form-label">Catégorie</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">Sans catégorie</option>
                                    <option value="Action" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Action') ? 'selected' : ''; ?>>Action</option>
                                    <option value="RPG" <?php echo (isset($_POST['category']) && $_POST['category'] === 'RPG') ? 'selected' : ''; ?>>RPG</option>
                                    <option value="Aventure" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Aventure') ? 'selected' : ''; ?>>Aventure</option>
                                    <option value="Sport" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Sport') ? 'selected' : ''; ?>>Sport</option>
                                    <option value="Stratégie" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Stratégie') ? 'selected' : ''; ?>>Stratégie</option>
                                    <option value="Simulation" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Simulation') ? 'selected' : ''; ?>>Simulation</option>
                                    <option value="FPS" <?php echo (isset($_POST['category']) && $_POST['category'] === 'FPS') ? 'selected' : ''; ?>>FPS</option>
                                    <option value="Horreur" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Horreur') ? 'selected' : ''; ?>>Horreur</option>
                                </select>
                            </div>

                            <!-- Image -->
                            <div class="mb-3">
                                <label for="image" class="form-label">Image de couverture</label>
                                <input type="file" class="form-control" id="image" name="image" 
                                       accept="image/jpeg,image/png,image/gif,image/webp">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Formats acceptés : JPG, PNG, GIF, WebP (Max 5 MB)
                                </small>
                            </div>

                            <!-- Contenu -->
                            <div class="mb-3">
                                <label for="content" class="form-label">
                                    Contenu de l'article <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="content" name="content" 
                                          rows="15" required placeholder="Rédigez votre article ici..."><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                            </div>

                            <!-- Boutons -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="admin.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Publier l'article
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<footer class="footer">
    <div class="container text-center">
        <p class="mb-0">© In My Gamer Girl Era</p>
    </div>
</footer>
    
</body>
</html>