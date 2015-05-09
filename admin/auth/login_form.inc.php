<?php
/**
 * login.php
 * 
 * Zeigt das Login-Formular an
 *
 **/

// Falls bereits eingeloggt weiterleiten
if($_SESSION['login'] == 'ok') {
  header('HTTP/1.1	302	Redirect');
  header("Location: ../list.php");
}
// Session-Token gegen CSRF 
$_SESSION['token'] = md5(uniqid(mt_rand(), true));
?>
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de" xml:lang="en">
  <head>
    <title>Admin-Panel::Constantin Kraft &bull; Webdesign & Entwicklung</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="../css/style.css" rel="stylesheet" type="text/css" />
  </head>
  <body>
    <div id="login_box">
      <form id="login_form" action="auth.php" method="post">
        <p>Benutzer: Gast <br/>Passwort: Benutzer123</p>
        <div>
          <?php
            if(isset($err)) {
              foreach($err as $error) {
                echo sprintf("<li>%s</li>", $error);
              }
            }
          ?>
          <input type="text" name="username" />
        </div>
        <input type="password" name="password" />
        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
        <input type="submit" name="login" value="Login" />
      </form>
    </div>
  </body>
</html>
