<?php

    session_start();

    require_once('src/option.php');

//Vérifier que le formulaire d'inscription a bien été envoyé
if(!empty($_POST['email']) && !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['password_two'])) {


    //Protection des variables 
    $email			= htmlspecialchars($_POST['email']);
    $username		= htmlspecialchars($_POST['username']);
    $password		= ($_POST['password']);
    $passwordTwo	= ($_POST['password_two']);

    //Vérifier que les mots de passe soient différents
    if($password != $passwordTwo) {

			header('location: signup.php?error=1&message=Vos mots de passe ne sont pas identiques.');
			exit();

		}
    
    //Vérifier la syntaxe de l'adresse mail    
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {

			header('location: signup.php?error=1&message=Votre adresse email est invalide.');
			exit();

		}

    // Vérifier que le username n'existe pas déjà
    $req = $db->prepare('SELECT COUNT(*) as numberUsername FROM users WHERE username = ?');
    $req->execute([$username]);
    $usernameVerification = $req->fetch();

    if($usernameVerification['numberUsername'] != 0) {
        header('location: signup.php?error=1&message=Ce pseudonyme est déjà utilisé.');
        exit();
    }

    //Vérifier que l'adresse mail n'est pas un doublon 
    $req = $db->prepare('SELECT COUNT(*) as numberEmail FROM users WHERE email = ?');
	$req->execute([$email]);

		while($emailVerification = $req->fetch()) {

			if($emailVerification['numberEmail'] != 0) {

				header('location: signup.php?error=1&message=Votre adresse email est déjà utilisée par un autre utilisateur.');
				exit();

			}

		}

    //Chiffrement du mot de passe   
    $password = "aq1".sha1($password."672")."94";

	// Secret
	$secret = sha1($email).time();
	$secret = sha1($secret).time();

	// Ajouter un utilisateur
	$req = $db->prepare('INSERT INTO users(username, email, password, secret, role) VALUES(?, ?, ?, ?, ?)');
	$req->execute([$username, $email, $password, $secret, 'user']);

	header('location: signup.php?success=1');
	exit();

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/manette-de-jeu.png" type="images.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"/>
    <link rel="stylesheet" href="design/css/signup.css" />
    <title>Document</title>
</head>
<body id="signup_body">
    <div class="signup-container">
        <div class="signup-box">
            <section>
		
			<h1 class="signup-title">Inscription</h1>

			<?php if(isset($_GET['error']) && isset($_GET['message'])) {

				echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';

			} else if(isset($_GET['success'])) {

				echo '<div class="alert success">Vous êtes désormais inscrit. <a href="login.php">Connectez-vous</a>.</div>';

			} ?>

            <form method="post" action="signup.php">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Pseudonyme :</label>
	                        <input type="username" name="username" class="form-control" id="username" placeholder="Votre pseudonyme" required />
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email :</label>
	                        <input type="email" name="email" class="form-control" id="email" placeholder="Votre email" required />
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe :</label>
	                        <input type="password" name="password" class="form-control" id="password" placeholder="Mot de passe" required />
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Vérification du mot de passe :</label>
	                        <input type="password" name="password_two" class="form-control" id="password_two" placeholder="Mot de passe" required />
                        </div>
	                    
                        <button type="submit" class="btn btn-primary w-100 signup-btn">S'inscrire</button>

                        <div class="text-center mt-3">
                            <span>Déjà inscrit ? <a href="login.php" class="login-link">Connecte toi !</a></span>
	                </form>
        </div>
        </div>
</section>
</body>
</html>
