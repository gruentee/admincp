<?php
/*
 * mysql.inc.php
 *
 * Stellt Verbindung zum MySQL-Server her.
 *
 */

// TODO: refactor to array
$host = "";

$user = "";

$pw   = "";

$db   = "";


if(!$connection = @mysqli_connect($host, $user, $pw))
{
    echo 'Verbindung zum DB-Server fehlgeschlagen!<br />Fehler: ' . mysqli_error($connection);
}
else
{
    mysqli_set_charset($connection, 'utf8');
    if(!@mysqli_select_db($connection, $db))
    {
        echo "Ausw&auml;hlen der Datenbank \"$db\" fehlgeschlagen!<br />Fehler: ". mysqli_error($connection);
    } 
}
?>
