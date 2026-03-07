<?php
    session_start();
    require_once('src/connection.php');

// SQL query pour récupérer les articles 
$stmt = $db->query("SELECT * FROM articles ORDER BY id DESC LIMIT 3");
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/manette-de-jeu.png" type="images.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"/>
    <script src="https://kit.fontawesome.com/67b2bbd532.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="design/css/styles.css" />
    <title>In My Gamer Girl Era | Video Games Reviews</title>
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
                        <li class="nav-item">
                            <a class="nav-link" href="#articles">Articles</a>
                        </li>
                        
                        <?php if(isset($_SESSION['connect']) && $_SESSION['connect'] == 1): ?>
                        <!-- Utilisateur connecté -->
                        
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <!-- Onglet Administration (visible seulement pour le compte admin) -->
                            <li class="nav-item">
                                <a class="nav-link text-danger fw-bold" href="admin.php">Administration</a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Afficher le pseudonyme de l'utilisateur -->
                        <li class="nav-item">
                            <span class="nav-link"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </li>
                        
                        <!-- Bouton Déconnexion -->
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Déconnexion</a>
                        </li>
                        
                    <?php else: ?>
                        <!-- Utilisateur non connecté -->
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Connexion/Inscription</a>
                        </li>
                    <?php endif; ?>
                       
                    </ul>
                </div>
            </div>
        </nav>
    </header>

<!--Partie Portfolio-->
<section class="portfolio" id="portfolio"> 
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-1 order-2">
                <div class="portfolio-text">
                    <h1 class="mb-4">Bienvenue dans mon univers gaming !</h1>
                        <span class="lead">
                            Passionnée de jeux vidéo depuis petite, je partage avec vous mes découvertes, 
                            mes coups de cœur et mes critiques honnêtes sur les derniers jeux du moment. 
                            Alors si vous aussi êtes fan des jeux vidéos (ou souhaiter en découvrir), 
                            vous êtes au bon endroit !
                        </span> <br>
                        <a href="#articles" class="btn btn-custom mt-3">Découvrir mes articles</a>
                </div>
            </div>
            <div class="col-lg-6 order-lg-2 order-1 text-center mb-4 mb-lg-0">
                <img src="images/controller.png" alt="Manette gaming rose" class="controller-img img-fluid">
            </div>
        </div>
    </div>
</section>

<!--Partie Articles-->
    <section class="articles" id="articles">
        <div class="container">
            <h2 class="section-title text-center mb-5">Mes derniers articles</h2>
            <div class="row g-4">
                <?php foreach($articles as $index => $article): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="article-card">
                        <div class="article-thumbnail">
                            <img src="<?php echo htmlspecialchars($article['thumbnail']); ?>" 
                                 alt="<?php echo htmlspecialchars($article['title']); ?>" 
                                 class="img-fluid">
                        </div>
                        <div class="article-content">
                            <h3 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                            <p class="article-excerpt"><?php echo htmlspecialchars($article['excerpt']); ?></p>
                            <div class="article-footer">
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
                                </div>
                                <span class="article-date"><?php echo date('d/m/Y', strtotime($article['created_at'])); ?></span>
                            </div>
                            <a href="article.php?id=<?php echo $article['id']; ?>" class="btn btn-read-more mt-3">Lire l'article</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<footer class="footer">
    <div class="container text-center">© In My Gamer Girl Era</div>
</footer> 
    
</body>
</html>