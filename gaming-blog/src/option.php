<?php
    //Connexion à la database
        require_once(__DIR__ . '/connection.php');
    
    if(isset($_COOKIE['auth']) && !isset($_SESSION['connect'])) {

        //Variable
        $secret = htmlspecialchars($_COOKIE['auth']);

        //Vérifier que le secret existe
        $req = $db->prepare('SELECT COUNT(*) AS secretNumber FROM users WHERE secret = ?');
        $req->execute([$secret]);
        $user = $req->fetch(); //à la place de la boucle while

        while($users = $req->fetch()) {

            if($users['secretNumber'] == 1) {

                // Lire tout ce qui concerne l'utilisateur
                if($user) {
                    $_SESSION['connect']  = 1;
                    $_SESSION['email']    = $user['email'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_id']  = $user['id'];
                    $_SESSION['role']     = $user['role'];
                }

            }

        }

    }