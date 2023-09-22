<link rel="stylesheet" type="text/css" href="style.css">
<?php

session_start(); // Démarrez la session

// Connexion à la base de données
$serveur = "localhost";
$utilisateur = "root";
$mot_de_passe = "";
$base_de_donnees = "memory";

$connexion = new mysqli($serveur, $utilisateur, $mot_de_passe, $base_de_donnees, 3306);

if ($connexion->connect_error) {
    die("La connexion à la base de données a échoué : " . $connexion->connect_error);
}

function back()
{
    header("Location: index.html");
}

function generateForm($mode, $playerCount = 1)
{
    if ($mode === "solo") {
        echo '<div id="backPage" onclick="window.location.href=\'index.html\'"> <-- </div>';
        echo '<h2>Votre pseudo</h2>';
        echo ' <span class="point first"></span>
        <span class="point second"></span>
        <span class="point third"></span>
        <span class="point fourth"></span>
        <span class="point fived"></span>
        <span class="point sixed"></span>';
    } else if ($mode === "multi") {
        echo ' <span class="point first"></span>
        <span class="point second"></span>
        <span class="point third"></span>
        <span class="point fourth"></span>
        <span class="point fived"></span>
        <span class="point sixed"></span>';
        echo '<div id="backPage" onclick="window.location.href=\'index.html\'"> <-- </div>';
        echo '<h2>Choix du nombre de joueurs</h2>';
    }
    echo '<form method="post" class="formSolo" action="functions.php">';
    if ($mode === "solo") {
        echo ' <div class="background"></div>
        <div class="overlay"></div>
        ';
        echo '<input type="text" name="pseudo" placeholder="Tapez votre pseudo !" id="pseudo" required><br>';
        echo '<input type="hidden" name="mode" value="solo">';
    } elseif ($mode === "multi") {
           
        echo '<input type="hidden" name="mode" value="multi">';
        echo '<input type="hidden" name="player_count" value="' . $playerCount . '">';
    }
    if ($mode === "multi" || $playerCount > 1) {
        for ($i = 1; $i <= $playerCount; $i++) {
            echo ' <div class="background"></div>
            <div class="overlay"></div>
        ';
            
            echo '<label for="pseudo' . $i .'">Joueur ' . $i .' : </label>';
            echo '<input type="text" placeholder="Choisissez un pseudo" name="pseudo' . $i . '" id="pseudo' . $i .'" required><br>';
        }
    }
    echo '<input type="submit" value="Start">';
    echo '</form>';
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["player_count"])) {
        $playerCount = $_POST["player_count"];
        generateForm("multi", $playerCount);

        if ($connexion) { // Assurez-vous que la connexion est établie avec succès
            for ($i = 1; $i <= $playerCount; $i++) {
                if (isset($_POST["pseudo" . $i])) {
                    $pseudo = $_POST["pseudo" . $i];

                    // Utilisez mysqli_real_escape_string pour éviter les injections SQL
                    $pseudo = $connexion->real_escape_string($pseudo);

                    // Vérifiez si le pseudo existe déjà dans la base de données
                    $checkQuery = "SELECT * FROM user WHERE pseudo = '$pseudo'";
                    $result = $connexion->query($checkQuery);

                    if ($result->num_rows > 0) {
                        // Le pseudo existe déjà, effectuez une mise à jour
                        $updateQuery = "UPDATE user SET pseudo = '$pseudo' WHERE pseudo = '$pseudo'";
                        if ($connexion->query($updateQuery) === TRUE) {
                            $_SESSION["pseudo"] = $pseudo;
                            header("Location: jeu.php");
                        } else {
                            echo "Erreur lors de la mise à jour du pseudo du joueur $i : " . $connexion->error . "<br>";
                        }
                    } else {
                        // Le pseudo n'existe pas, effectuez une insertion
                        $insertQuery = "INSERT INTO user (pseudo) VALUES ('$pseudo')";
                        if ($connexion->query($insertQuery) === TRUE) {
                            $_SESSION["pseudo"] = $pseudo;
                        header("Location: jeu.php");
                        } else {
                            echo "Erreur lors de l'insertion du pseudo du joueur $i : " . $connexion->error . "<br>";
                        }
                    }
                }
            }
        }
    } elseif (isset($_POST["mode"])) {
        $mode = $_POST["mode"];
        if ($mode === "solo") {
            generateForm("solo");

            if (isset($_POST["pseudo"])) {
                $pseudo = $_POST["pseudo"];

                // Utilisez mysqli_real_escape_string pour éviter les injections SQL
                $pseudo = $connexion->real_escape_string($pseudo);

                // Vérifiez si le pseudo existe déjà dans la base de données
                $checkQuery = "SELECT * FROM user WHERE pseudo = '$pseudo'";
                $result = $connexion->query($checkQuery);

                if ($result->num_rows > 0) {
                    // Le pseudo existe déjà, effectuez une mise à jour
                    $updateQuery = "UPDATE user SET pseudo = '$pseudo' WHERE pseudo = '$pseudo'";
                    if ($connexion->query($updateQuery) === TRUE) {
                        $_SESSION["pseudo"] = $pseudo;
                        header("Location: jeu.php");
                    } else {
                        echo "Erreur lors de la mise à jour du pseudo du joueur $i : " . $connexion->error . "<br>";
                    }
                } else {
                    // Le pseudo n'existe pas, effectuez une insertion
                    $insertQuery = "INSERT INTO user (pseudo) VALUES ('$pseudo')";
                    if ($connexion->query($insertQuery) === TRUE) {
                        $_SESSION["pseudo"] = $pseudo;
                        header("Location: jeu.php");
                    } else {
                        echo "Erreur lors de l'insertion du pseudo : " . $connexion->error . "<br>";
                    }
                }
            }
        } elseif ($mode === "multi") {
            echo ' <div class="background"></div>
            <div class="overlay"></div>
        ';
        echo '<div id="backPage" onclick="window.location.href=\'index.html\'"> <-- </div>';

            echo '<h2>Choix du nombre de joueurs</h2>';
            echo '<form method="post" action="functions.php">';
            for ($i = 2; $i <= 2; $i++) {
                echo '<button class="mode" name="player_count" value="' . $i . '">' . $i ." (Avec un ordi)". '</button>';
            }
            echo '</form>';
        }
    }
}
?>