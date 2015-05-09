<?php
/*
 * edit.php
 *
 * Zeigt das Formular zum Bearbeiten und Hochladen von Bildern an
 * und stellt die Funktionen zum Bearbeiten, Hochladen und Loeschen bereit.
 *
 */

// Error-Reporting anpassen
error_reporting(E_ERROR | E_PARSE);

// DB-Verbindung
require_once('inc/mysql.inc.php');

// Image-Klasse
require_once('lib/class.Images.php');

// Editier-Funktionen
require_once('lib/lib.edit.php');

// Header einbinden
include_once('inc/header.inc.php');

define('PUB_ROOT', '../public/');

$id             = is_numeric($_GET['id']) ? $_GET['id'] : false;
$mode           = $_GET['mode'];
$picDir         = PUB_ROOT."photos/pics/";
$thumbDir       = PUB_ROOT."photos/thumbs/";
$picMaxWidth    = 600;
$thumbWidth     = 100;




/***********************************************************************
 *
 *  Programm-Logik
 *
 **********************************************************************/

switch ($mode)
{
    case "edit": // Editier-Modus
        if(!isset($_POST['submit'])) // Formular noch nicht abgesendet
        {
            if($id !== false)
            {
                $picData = getPictureFromDB($id);
            }
            else
            {
                echo "Fehler:<br />Die \$id muss einen numerischen Wert haben!";
                exit();
            }
            include('./inc/form.inc.php');
        }
        else // Formular abgesendet
        {
            $data = verifyFormData($_POST);
            //~ outputArray($data);
            //~ outputArray($_POST);
            if(isset($data['err'])) // Fehler
            {
                print "<p class=\"fehler\">";
                print "Fehler beim Ausf&uuml;llen des Formulares:<br />";
                foreach($data['err'] as $fehler)
                {
                    echo $fehler.'<br />';
                }
                print "</p>";
            }
            else
            {
                if(savePicture($data))
                {
                    print "Daten erfolgreich gespeichert! <br />";
                }
                else
                {
                    echo "Fehler sind aufgetreten!<br />";
                    //~ outputArray($data); // DEBUG
                }
            }
        }
        break;

    case "add": // Bild hinzufuegen
        $picData = "";
        if(isset($_POST['submit']))
        {
            $data = verifyFormData($_POST);
            if($data['err']) // Fehler
            {
                print "<p class=\"fehler\">";
                print "<b>Fehler beim Ausf&uuml;llen des Formulares:</b><br />";
                foreach($data['err'] as $fehler)
                {
                    echo $fehler.'<br />';
                }
                print "</p>";
                //~ outputArray($data['err']); // DEBUG
                //~ outputArray($_FILES['upload_pic']); // DEBUG
            }
            else
            {
                if(addPicture($data))
                {
                    print "Bild erfolgreich hinzugef&uuml;gt! <br />";
                }
                else
                {
                    echo "Fehler sind aufgetreten!<br />";
                }
            }
        }
        else
        {
            include('./inc/form.inc.php');
        }
        break;
    case "delete": // Bild loeschen
        if($_GET['really']) // Loeschen bestaetigt
        {
            if(getPictureFromDB($id))
            {
                $sql = "DELETE
                        FROM pictures
                        WHERE id=\"$id\"";
                if($query = mysql_query($sql))
                {
                    print '<script language="JavaScript">window.alert("Bild erfolgreich entfernt!");</script>';
                    print '<meta http-equiv="refresh" content="0; URL=list.php" />';
                }
                else
                {
                    print "<p>L&ouml;schen fehlgeschlagen!<br />MySQL sagt:".mysql_error()."</p>";
                }
            }
        }
        else // Bestaetigungs-Buttons anzeigen
        {
            print '<p>Bild wirklich l&ouml;schen?</p>';
            print '<form method="GET" action="edit.php">';
            print '<input type="submit" name="really" value="Ja, sicher!" />&nbsp;';
            print "<input type=\"button\" onclick=\"javascript: window.location.replace('list.php');\" value=\"Nein, doch nicht!\" />";
            print '<input type="hidden" name="mode" value="delete" />';
            print '<input type="hidden" name="id" value="'.$id.'" />';
            print '</form>';
        }
        break;
    case 'impress':
        $file = PUB_ROOT . 'impressum.txt';

        if(!isset($_POST['ch_impress'])) {
            if(!$curr_txt = @file_get_contents($file)) {
                echo "<strong>Fehler:</strong> Datei $file konnte nicht ge&ouml;ffnet werden!";
            } else {
                //~ $curr_txt = file_get_contents($fh);
                print '<h2>Impressum &auml;ndern</h2>';
                print '<form action="?mode=impress" method="post" enctype="application/x-www-form-urlencoded" accept-charset="ISO-8599-1"><textarea name="impress_txt" id="edit_form" cols="50" rows="10">';
                print $curr_txt;
                print '</textarea>';
                print '<p><input type="submit" name="ch_impress" value="&Auml;ndern" /></p></form>';
            }
        }
        else {
            if(!$fh = @fopen($file, 'w')) {
                echo "<strong>Fehler:</strong> Datei $file konnte nicht ge&ouml;ffnet werden!";
            } else {
                //~ if(fwrite($fh, htmlspecialchars($_POST['impress_txt'], ENT_COMPAT, 'ISO-8859-1'))) {
                $patterns = array('ä', 'ö', 'ü', 'ß');
                $replace  = array('&auml;', '&ouml;', '&uuml;', '&szlig;');
                //~ $impress_txt = preg_replace($patterns, $replace, $_POST['impress_txt']);
                $impress_txt = $_POST['impress_txt'];
                if(fwrite($fh, $impress_txt)) {
                    print '<strong>Impressum erfolgreich ge&auml;ndert!</strong>';
                } else {
                    print '<strong>Fehler beim &Auml;ndern des Impressums!</strong>';
                }
                @fclose($fp);
            }
        }
        break;
}

// Alles in JSON-Datei (Cache) auslagern
$json_file = PUB_ROOT . "data/data.json";
if($json_data = fetchJsonData()) {
    //~ die($json_data);
    if(!saveToJson($json_data, $json_file)) {
        echo "Fehler: JSON-Datei konnte nicht geschrieben werden!<br />";
    }
}

// DEBUG
//~ print '<u><b>DEBUG-INFORMATIONEN:</b></u><br /><br />';
//~ echo 'Formular-Eingaben ($picdata): '.outputArray($_POST).'<br />';
//~ echo 'Validierte Formular-Eingaben ($validData): '.outputArray($data).'<br />';
//~ echo 'mysql_affected_rows(): '.mysql_affected_rows($conn).'<br />';
//~ echo 'mysql_info(): '.mysql_info($conn).'<br />';
//~ echo '$id: '.$id.'<br /><br />';
//~ echo '$data: '.$data['err_code'].'<br />';
//~ echo '<pre>';
//~ echo '$_FILES: '.print_r($_FILES).'<br />';
//~ echo '$data: '.print_r($data).'<br />';
//~ echo '</pre>';
//~ echo $picDir;

// Footer einbinden
include('inc/footer.inc.php');
?>
