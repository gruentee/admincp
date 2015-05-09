<?php
/*
 * mysql.inc.php
 *
 * Stellt Verbindung zum MySQL-Server her.
 *
 */
 
$host = "localhost";

$user = "dev";

$pw   = "asdf123";

$db   = "admincp";

if(!$conn = @mysql_connect($host, $user, $pw))
{
    echo 'Verbindung zum DB-Server fehlgeschlagen!<br />Fehler: '.mysql_error();
}
else
{
    mysql_set_charset('utf8');
    if(!@mysql_select_db($db))
    {
        echo "Ausw&auml;hlen der Datenbank \"$db\" fehlgeschlagen!<br />Fehler: ".mysql_error();    
    } 
}
?>
