<?php
/*
 * edit.php
 *
 * Zeigt das Formular zum Bearbeiten und Hochladen von Bildern an
 * und stellt die Funktionen zum Bearbeiten, Hochladen und Loeschen bereit.
 *
 */

// DB-Verbindung
require_once('config/mysql.inc.php');

// Image-Klasse
require_once('lib/class.Image.php');

// Editier-Funktionen
require_once('lib/lib.edit.php');

// Header einbinden
include_once('template/header.inc.php');

define('PUB_ROOT', '../public/');

$id             = is_numeric($_GET['id']) ? $_GET['id'] : false;
$action           = $_GET['action'];

/***********************************************************************
 *
 *  Programm-Logik
 *
 **********************************************************************/

switch ($action)
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
            include('./template/form.inc.php');
        }
        else // Formular abgesendet
        {
            $data = verifyFormData($_POST);
            if(isset($data['err'])) //Validierungsfehler
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
                    echo "Daten erfolgreich gespeichert! <br />";
                }
                else
                {
                    echo "Fehler sind aufgetreten!<br />";
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
            include('./template/form.inc.php');
        }
        break;
    case "delete": // Bild loeschen
        // TODO: lösch-bestätigung neu implementieren
        if($_GET['really']) // Loeschen bestaetigt
        {
            if(getPictureFromDBureFromDB($id))
            {
                $sql = "DELETE
                        FROM pictures
                        WHERE id=\"$id\"";
                if($query = mysqli_query($connection, $sql))
                {
                    print '<script>window.alert("Bild erfolgreich entfernt!");</script>';
                    print '<meta http-equiv="refresh" content="0; URL=list.php" />';
                }
                else
                {
                    print "<p>L&ouml;schen fehlgeschlagen!<br />MySQL sagt:" . mysqli_error($connection) . "</p>";
                }
            }
        }
        else // Bestaetigungs-Buttons anzeigen
        {
            print '<p>Bild wirklich l&ouml;schen?</p>';
            print '<form method="GET" action="edit.php">';
            print '<input type="submit" name="really" value="Ja, sicher!" />&nbsp;';
            print "<input type=\"button\" onclick=\"javascript: window.location.replace('list.php');\" value=\"Nein, doch nicht!\" />";
            print '<input type="hidden" name="action" value="delete" />';
            print '<input type="hidden" name="id" value="'.$id.'" />';
            print '</form>';
        }
        break;
}

// Alles in JSON-Datei (Cache) auslagern
$json_file = PUB_ROOT . "data/data.json";
$json_data = fetchJsonData();
if(0 !== count($json_data))  {
    if(!saveToJson($json_data, $json_file)) {
        echo "Fehler: JSON-Datei konnte nicht geschrieben werden!<br />";
    }
}


// Footer einbinden
include('template/footer.inc.php');
?>
