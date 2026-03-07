<?php
require_once('src/admin-check.php');

// Récupérer tous les articles
$stmt = $db->query('
    SELECT * 
    FROM articles 
    ORDER BY id ASC
');
$articles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Articles</title>
    <link rel="shortcut icon" href="images/manette-de-jeu.png" type="images.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"/>
    <script src="https://kit.fontawesome.com/67b2bbd532.js" crossorigin="anonymous"></script> <!-- Lien perso Font Awesome -->
    <link rel="stylesheet" href="design/css/admin.css" />
</head>
<body>
    <nav class="navbar navbar-dark navbar-admin">
        <div class="container-fluid">
            <span class="navbar-brand">
                <i class="fas fa-gamepad"></i> Administration - In My Gamer Girl Era
            </span>
            <div>
                <a href="index.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-home"></i> Retour au site
                </a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </nav>

<div class="content-wrapper">    <!-- wrapper: par convention, la class content contient d'autres éléments -->

<!--Entête -->    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestion des articles</h1>
                <a href="admin-create-article.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nouvel article
                </a>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i>
                <?php 
                if($_GET['success'] == 'created') echo 'Article créé avec succès !';
                elseif($_GET['success'] == 'updated') echo 'Article modifié avec succès !';
                elseif($_GET['success'] == 'deleted') echo 'Article supprimé avec succès !';
                ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 5%;">ID</th>
                                    <th style="width: 40%;">Titre</th>
                                    <th style="width: 15%;">Catégorie</th>
                                    <th style="width: 20%;">Date de création</th>
                                    <th class="text-end" style="width: 20%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($articles) > 0): ?>
                                    <?php foreach($articles as $article): ?>
                                        <tr>
                                            <td><span class="badge bg-secondary">#<?php echo $article['id']; ?></span></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($article['title']); ?></strong>
                                            </td>
                                            <td>
                                                <?php if(!empty($article['category'])): ?>
                                                    <span class="badge bg-primary">
                                                        <?php echo htmlspecialchars($article['category']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">Non catégorisé</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="far fa-calendar"></i>
                                                    <?php echo date('d/m/Y', strtotime($article['created_at'])); ?>
                                                    <br>
                                                    <i class="far fa-clock"></i>
                                                    <?php echo date('H:i', strtotime($article['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td class="text-end">
                                                <a href="article.php?id=<?php echo $article['id']; ?>" 
                                                class="btn btn-sm btn-info" target="_blank" title="Voir l'article">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="admin-edit-article.php?id=<?php echo $article['id']; ?>" 
                                                class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="admin-delete-article.php?id=<?php echo $article['id']; ?>" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cet article ?\n\nCette action est irréversible.');"
                                                title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                            <p class="mb-0">Aucun article pour le moment.</p>
                                            <small>Cliquez sur "Nouvel article" pour commencer.</small>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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