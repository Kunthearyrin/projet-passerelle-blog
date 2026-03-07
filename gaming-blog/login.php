<?php

    session_start();

    require_once('src/option.php');

//Vérifier que le formulaire d'inscription a bien été envoyé
if(!empty($_POST['username']) && !empty($_POST['password'])) {


    //Protéger les variables 
    $username			= htmlspecialchars($_POST['username']);
	$password		= htmlspecialchars($_POST['password']);

    

    //Chiffrement du mot de passe   
    $password = "aq1".sha1($password."672")."94";


    //Connexion
    $req = $db->prepare('SELECT * FROM users WHERE username = ?');
	$req->execute([$username]);

	$user = $req->fetch(PDO::FETCH_ASSOC);

if ($user && $password == $user['password']) {

    $_SESSION['connect']  = 1;
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['role']     = $user['role'];

    // Cookie 
    if (isset($_POST['auto'])) {
        setcookie('auth', $user['secret'], [
    'expires'  => time() + 365 * 24 * 3600,
    'path'     => '/',
    'secure'   => false, 
    'httponly' => true,
    'samesite' => 'Lax'
]);
    }

    header('Location: index.php?success=1');
    exit();

} else {
    header('Location: login.php?error=1&message=Impossible de vous authentifier correctement.');
    exit();
}
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/manette-de-jeu.png" type="images.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"/>
    <link rel="stylesheet" href="design/css/login.css" />
    <title>Connexion</title>
</head>
<body id="login_body">
    <div class="login-container">
        <div class="login-box">
    <section>

                <?php if(isset($_SESSION['connect'])) { ?>

					<h1>Bonjour !</h1>
					<?php
					if(isset($_GET['success'])){
						echo'<div class="alert success">Vous êtes maintenant connecté.</div>';
					} ?>
					<p>Bienvenue sur mon blog</p>
					<small><a href="logout.php">Déconnexion</a></small>

				<?php } else { ?>
					<h1 class="login-title">Se connecter</h1>

					<?php if(isset($_GET['error'])) {

						if(isset($_GET['message'])) {
							echo'<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
						}

					} ?>
                    

                    <form method="post" action="login.php">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Pseudonyme :</label>
	                        <input type="text" name="username" class="form-control" id="username" placeholder="Votre pseudonyme" required />
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe :</label>
	                        <input type="password" name="password" class="form-control" id="password" placeholder="Mot de passe" required />
                        </div>
 
                        <div class="mb-3 form-check">
	                        <label class="form-check-label" for="checkbox">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="auto" checked />Se souvenir de moi</label>
                        </div>
	                    
                        <button type="submit" class="btn btn-primary w-100 login-btn">S'identifier</button>
                       
                        <div class="text-center mt-3">
                            <a href="#" class="forgot-password">Mot de passe oublié ?</a>
                        </div>

                        <div class="text-center mt-3">
                            <span>Première visite sur mon blog ? <a href="signup.php" class="signup-link">Inscris toi !</a></span>
	                </form>
				
                    <?php } ?>
        </div>
    </section>
</body>
</html>