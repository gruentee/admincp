<?php
/**
 * Funktionen zum Editieren von Bildern
 * 
 * Alle Funktionen setzen eine intakte MySQL-Verbindung  $connection vorraus
 * voraus.
 *
 */

function getPictureFromDB($id) // Bild aus DB abrufen
{
    $sql = "SELECT *
            FROM pictures
            WHERE id=\"$id\"";
    global $connection;
    if(!$query = mysqli_query($connection, $sql))
    {
        echo __FUNCTION__ .": Fehler beim Senden des Querys: " . mysqli_error($connection);
    }
    else
    {
        if(mysqli_num_rows($query) < 1)
        {
            echo "Das Bild mit der ID $id ist nicht vorhanden!";
            return false;
        }
        else
        {
            $result = mysqli_fetch_array($query);
            return $result;
        }
    }
}

/**
 * fetchJsonData
 * Holt alle Datensaetze aus der Datenbank und gibt sie als JSON zurueck
 * 
 * @return bool false if data could not be fetched or data set is empty
 * @return str json-encoded data
 */
function fetchJsonData() {
    $connection = $GLOBALS['connection'];
    $sql_get_all = "SELECT id, datei_pic AS src, titel AS title, special FROM pictures";
    $json_data = array();
    if ($query = mysqli_query($connection, $sql_get_all)) {
        while ($data = @mysqli_fetch_assoc($query)) {
            array_push($json_data, $data);
        }
    }
    return !empty($json_data) ? json_encode($json_data) : false;
}

/**
 * saveToJson
 * Write data to JSON file
 * 
 * @param $data str json-encoded data to be written
 * @param $outfile str filename of json file to be created
 **/
function saveToJson($data, $outfile) {
    $connection = $GLOBALS['connection'];
    $fh = fopen($outfile, 'w');
    if($fh != false) {
        @fwrite($fh, $data);
        @fclose($fh);
        return true;
    } else {
        return false;
    }
}

function savePicture($data) // Bild speichern
{
    global $connection;
    $sql = "UPDATE pictures
            SET datei_pic=\"$data[datei_pic]\", titel=\"$data[titel]\", beschreibung=\"$data[beschreibung]\"
            WHERE id=\"$data[id]\"";
    if(!$query = mysqli_query($connection, $sql))
    {
        echo __FUNCTION__ .": Fehler beim Senden des Querys: " . mysqli_error($connection);
        return false;
    }
    else
    {
        return true;
    }
}

function addPicture($data)
{
    global $connection;
    $sql = "INSERT INTO pictures
            SET titel=\"$data[titel]\", beschreibung=\"$data[beschreibung]\", datei_pic=\"$data[datei_pic]\"";

    if(!$query = mysqli_query($connection, $sql))
    {
        echo __FUNCTION__ . ': Fehler beim Senden des Querys: ' . mysqli_error($connection);
        return false;
    }
    else
    {
        return true;
    }
}

function uploadFile($file)
{
    global $picDir, $thumbDir, $picMaxWidth, $thumbWidth;
    if(!is_file($file['tmp_name']) OR $file['error'] > 0) // TMP-Datei existiert nicht oder Fehler in $_FILES-Array
    {
        $rueckgabe['err'] = true;
        $rueckgabe['err_code'] = 'Fehler beim Hochladen der Datei:<br />Error-Code: '.$file['error'];
    }
    else
    {
        $fileType = checkFileType($file['tmp_name']);
        if(!$fileType) // Richtiger Dateityp?
        {
            $rueckgabe['err'] = true;
            $rueckgabe['err_code'] = 'Es k&ouml;nnen nur JPG, GIF oder PNG-Dateien hochgeladen werden.';
        }
        else // Kein Fehler, Bild hochladen
        {
            if($uploaded = move_uploaded_file($file['tmp_name'], $picDir.basename($file['name'])))
            {
                $rueckgabe['bild'] = basename($file['name']);
                $rueckgabe['err'] = false;

                $gis = getimagesize($picDir.$rueckgabe['bild']); 
                if($gis[0] > $picMaxWidth) // Bild verkleinern
                {
                    scaleImage($picDir.$rueckgabe['bild'], $picMaxWidth, $picDir);
                }
                // Thumbnail erstellen
                if(!generateCropThumbnail($picDir.$rueckgabe['bild'], $thumbWidth, $thumbDir))
                {
                    $rueckgabe['err'] = true;
                    $rueckgabe['err_code'] = 'Fehler beim Erstellen des Thumbnails!';
                }
            }
            else
            {
                $rueckgabe['err'] = true;
                $rueckgabe['err_code'] = 'Fehler beim Hochladen der Datei:<br />move_uploaded_file: '.$uploaded;
                $rueckgabe['err_code'] += $file['tmp_name'] ."\n". $picDir.basename($file['name']);
            }
        }
    }
    return $rueckgabe;
}

/**
 * Check file type
 * 
 */
function checkFileType($file)
{
    $filetype = getimagesize($file);
    if(is_array($filetype))
    {
        if($filetype[2] > 3) // 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF
        {
            return false; // Falsche Datei-Endung
        }
        else
        {
            return true;
        }
    }
    else
    {
        return false;
    }
}

function generateCropThumbnail($pic, $width, $destination)
{
    try
    {
        $im = new Image($pic); // Image instanziieren

        try
        {
            $im->resize( $width, $width, 'crop', 'c', 'c' ); // Thumbnail erstellen
            try
            {
                $im->save($destination.substr_replace(basename($pic), '', -4, 4)); // Bild erstellen
            }
            catch (Exception $fehler)
            {
                echo "Fehler beim Schreiben des Bildes: ".$fehler->getMessage();
                return false;
            }
        }
        catch (Exception $fehler)
        {
            echo "Fehler beim croppen des Bildes: ".$fehler->getMessage();
            return false;
        }
    }
    catch (Exception $fehler)
    {
        echo "Fehler beim Instanziieren von Image: ".$fehler->getMessage();
        return false;
    }
    return true;
}

/**
 * scaleImage
 * 
 * Scale image to specified width.
 * height get's calculated automatically
 * 
 * @param $pic path to image file
 * @param $width width of the output image
 * @param $dest path to destination folder
 **/
function scaleImage($pic, $width, $dest)
{
    try
    {
        $im = new Image($pic); // imagick-Instanz
        try
        {
            $im->resize( $width, $width ); // Bildergroesse anpassen
            try
            {
                // Construct path to image file: strip of file-type extension
                $dest_path = $dest . substr_replace(basename($pic), '', -4, 4);
                $im->save($dest_path); // Bild erstellen
            }
            catch (Exception $fehler)
            {
                echo "Fehler beim Schreiben des Bildes: ".$fehler->getMessage();
                return false;
            }
        }
        catch (Exception $fehler)
        {
            echo "Fehler beim Verkleinern des Bildes: ".$fehler->getMessage();
            return false;
        }
    }
    catch (Exception $fehler)
    {
        echo "Fehler beim Instanziieren von Image: ".$fehler->getMessage();
        return false;
    }
    return true;
}

/**
 * Formular-Validierung
 * 
 **/
function verifyFormData($formData)
{
    global $connection;
    $fehler = array();
    array_pop($formData); // Letztes Element (Submit-Button loeschen)
    foreach($formData as $field => $value)
    {
        if(strlen($value) < 1)
        {
            $fehler[] = "Das Feld $field wurde nicht richtig ausgef&uuml;llt.";
        }
        $value = mysqli_escape_string($connection, $value);
    }
    if(isset($_FILES['upload_pic'])) // Bild angehaengt?
    {
        $bild = uploadFile($_FILES['upload_pic']);
        if($bild['err'] > true) // Fehler beim Hochladen
        {
            $fehler[] = $bild['err_code'];
        }
        else
        {
            $formData['datei_pic'] = $bild['bild']; // Pfad zum Bild in $formData-Array einfuegen
        }
    }

    if(count($fehler) > 0)
    {
        $formData['err'] = $fehler;
    }
    return $formData;
}

function set_special_pic_flag($picID, $flag) {
    global $connection;
    // Setzt das DB-Feld special fuer das Bild mit der ID $picID
    if( is_numeric($flag) AND ( $flag == 1 || $flag == 0 ) ) {

        $sql = "UPDATE pictures SET special=$flag";
        if(mysqli_query($connection, $sql)) {
            return True;
        } else {
            return False;
        }
    }
    else { // Falscher $flag-Wert uebergeben
        return False;
    }
}
