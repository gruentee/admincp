<?php
/**
 * Template für Header auf jeder Seite
 * 
 */

// Authentifizierung
require('./auth/auth_required.inc.php');

echo '<?xml version="1.0" encoding="utf-8" ?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>Portfolio::Admin-Panel | Constantin Kraft &bull; Webdesign Entwicklung</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="js/ui.js"></script>
    <?php
        $pathinfo = pathinfo($_SERVER['PHP_SELF']);
        if($pathinfo['basename'] == 'edit.php') {
            include_once('inc/tinymce.js.inc');
        }
    ?>
  </head>
<body>
  <div id="header">
    <h1>{ <span class="sans">admin panel</span> }</h1>
    <span class="greeting">
      Hallo <i><?php echo $_SESSION['username'];?>!</i>
    </span>
    <ul id="menu">
      <li>
        <a href="list.php">
          <img src="css/img/b_home.png" alt="" />&Uuml;bersicht
        </a>
      </li>
      <li>
        <a href="#" id="add_button">
          <img src="css/img/b_snewtbl.png" alt="" />Bild hinzuf&uuml;gen
        </a>
      </li>
      <li>
        <a href="./auth/logout.php">
            <img src="css/img/s_logoff.png" alt="" />Abmelden
        </a>
      </li>
      </ul>

    <div class="cleaner">&nbsp;</div>
  </div>
  <div id="msg_wrapper"></div>
  <div id="content">
