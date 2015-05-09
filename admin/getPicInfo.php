<?php
/*
 * getPicInfo.php
 * Holt Daten zu einem bestimmten Bild aus der DB; gedacht zum Aufruf per AJAX
 *
 */
 
// DB-Verbindung
require('inc/mysql.inc.php');
 
if(!isset($_GET['id']) OR !is_numeric($_GET['id']))
{
    die('Fehlende Parameter!');// DB-Verbindung
}

$sql = "SELECT * FROM pictures WHERE id=$_GET[id]";

if($query = mysql_query($sql))
{
    $data = mysql_fetch_array($query);
    echo "<div id=\"picInfo\">";
    echo "<img src=\"$data[datei_pic]\" alt=\"$data[titel]\" />";
    echo "<h2>$data[titel]</h2>";
    echo "<p>$data[beschreibung]</p>";
    echo "</div>";
}
// something went wrong …s
?>
