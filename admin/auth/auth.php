<?php
/**
 * login.php
 * 
 * Authentifizierungslogik
 * 
 * password-compat (https://github.com/ircmaxell/password_compat)
 * benötigt PHP >= 5.3.7
 * 
 */
require '../../vendor/autoload.php';

// MySQL-Verbindung
require_once('../config/mysql.inc.php');

session_start();


//~ define('VALID_REFERRER', "http://$_SERVER[SERVER_NAME]$_SERVER[SCRIPT_NAME]");

// Seite, zu der im Erfolgsfall weitergeleitet wird
define('TARGET_PAGE', '../list.php'); 

$err = array();

if(isset($_POST['login']))
{
    $forward_to = ($_SERVER['HTTP_REFERER'] !== '') ? TARGET_PAGE : $_SERVER['HTTP_REFERER'];

    // Form-Token checken
    if($_POST['token'] !== $_SESSION['token']) {
        $err = "Formular ungültig, bitte erneut versuchen!";
    }
    
    // TODO: input validation
    if(!ctype_alnum($_POST['username'])) {
        $err[] = "Benutzername darf nur Zahlen und Buchstaben enthalten.";
    }
    
    if(count($err) < 1) 
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $sql_select_username = sprintf("SELECT * FROM user 
                                WHERE user_name='%s'
                                AND active=1", $username);

        $query = mysqli_query($connection, $sql_select_username)
            or die("Fehler bei MySQL-Abfrage: " . mysqli_error($connection));

        if(mysqli_num_rows($query) == 1) // Usernamen aus DB holen
        {
            $data = mysqli_fetch_array($query);
            $hash = $data['pw_hash'];
            // Passwort ok und user freigeschaltet
            if(password_verify($password, $hash)) 
            {
                $_SESSION['login'] = 'ok';
                $_SESSION['username'] = $username;
                header('HTTP/1.1 302	Redirect');
                header("Location: $forward_to");
            }
            else 
            {
                $err[] = "Passwort ungültig!";
            }
        }
        else
        {
            $err[] = "Es wurde kein aktiver Benutzer mit dem Benutzernamen $username gefunden.";
        }
    }
}
// Login-Formular
include("login_form.inc.php");
?>
