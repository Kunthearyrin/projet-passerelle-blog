<?php

    session_start();
    require_once('src/option.php');


// Récupérer l'ID de l'article depuis l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$article_id = (int)$_GET['id'];

// Récupérer l'article
$stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$article_id]);

$article = $stmt->fetch();

// Si l'article n'existe pas, redirection
if (!$article) {
    header('Location: index.php');
    exit(); 
}

// Post = ajout commentaire 
if (isset($_POST['add_comment']) && isset($_SESSION['user_id'])) {
    $comment_content = trim($_POST['comment_content']);
    
    if (!empty($comment_content)) {
        try {
            $stmt = $db->prepare('
                INSERT INTO comments (article_id, user_id, content) 
                VALUES (?, ?, ?)
            ');
            
            if ($stmt->execute([$article_id, $_SESSION['user_id'], $comment_content])) {
                // ✅ Redirection (ne rechargez PAS les commentaires ici)
                header("Location: article.php?id=$article_id&comment=success");
                exit();
            }
            
        } catch(PDOException $e) {
            $comment_error = "Erreur lors de l'ajout du commentaire.";
        }
    } else {
        $comment_error = "Le commentaire ne peut pas être vide.";
    }
}

// Message de succès (après redirection)
if (isset($_GET['comment']) && $_GET['comment'] === 'success') {
    $comment_success = "Commentaire ajouté avec succès !";
}

// Récupérer les commentaires pour cet article
try {
    $stmt = $db->prepare('
        SELECT c.*, u.username 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.article_id = ? 
        ORDER BY c.created_at DESC
    ');
    $stmt->execute([$article_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $comments = []; // En cas d'erreur, tableau vide pour éviter l'erreur count()
    error_log("Erreur récupération commentaires: " . $e->getMessage());
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/manette-de-jeu.png" type="images.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"/>
    <script src="https://kit.fontawesome.com/67b2bbd532.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="design/css/article.css" />
    <title>Articles</title>
</head>
<body>
<header class="header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">In My Gamer Girl Era</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">Accueil</a>
                        </li>
                
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">Déconnexion</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Connexion</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
        </div>
    </nav>
</header>

<!--Header de l'article-->
<section class="article-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="display-4 text-white mb-3"><?php echo htmlspecialchars($article['title']); ?></h1>
                </div>
            </div>
        </div>
</section>

 <!-- Contenu de l'article -->
<div class="container mb-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Image principale -->
            <img src="<?php echo htmlspecialchars($article['thumbnail']); ?>" 
                alt="<?php echo htmlspecialchars($article['title']); ?>" 
                    class="article-thumbnail-large">

            <!-- Note -->
            <div class="article-rating">
                <?php 
                $rating = (int)$article['rating'];
                for($i = 1; $i <= 5; $i++): 
                    if($i <= $rating):
                ?>
                    <i class="fas fa-star"></i>
                <?php else: ?>
                    <i class="far fa-star"></i>
                <?php 
                    endif;
                    endfor; 
                ?>
                <span class="ms-2 text-muted"><?php echo $rating; ?>/5</span>
            </div>

            <!-- Contenu -->
                <div class="article-content">
                    <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                </div>
            </div>
        </div>
    </div>

<!-- Section commentaires -->
    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="comment-section">
                    <h3 class="mb-4">
                        <i class="fas fa-comments"></i> 
                        Commentaires (<?php echo count($comments); ?>)
                    </h3>

                    <!-- Formulaire d'ajout de commentaire -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($comment_success)): ?>
                            <div class="alert alert-success"><?php echo $comment_success; ?></div>
                        <?php endif; ?>
                        <?php if (isset($comment_error)): ?>
                            <div class="alert alert-danger"><?php echo $comment_error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="" class="mb-4">
                            <div class="mb-3">
                                <label for="comment_content" class="form-label">Ajouter un commentaire :</label>
                                <textarea class="form-control" id="comment_content" name="comment_content" 
                                          rows="4" placeholder="Partagez votre avis..." required></textarea>
                            </div>
                            <button type="submit" name="add_comment" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Publier
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            Vous devez être <a href="login.php">connecté</a> pour laisser un commentaire.
                        </div>
                    <?php endif; ?>

                    <!-- Liste des commentaires précédents-->
                    <?php if (count($comments) > 0): ?>
                        <?php foreach($comments as $comment): ?>
                            <div class="comment-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="comment-author">
                                        <i class="fas fa-user-circle"></i> 
                                        <?php echo htmlspecialchars($comment['username']); ?>
                                    </span>
                                    <span class="comment-date">
                                        <?php 
                                        $timestamp = strtotime($comment['created_at']);
                                        echo $timestamp ? date('d/m/Y à H:i', $timestamp) : '';
                                        ?>
                                    </span>
                                </div>
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">Aucun commentaire pour le moment. Soyez le premier à commenter !</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<footer class="footer">
    <div class="container text-center">© In My Gamer Girl Era</div>
</footer> 
    
</body>
</html>