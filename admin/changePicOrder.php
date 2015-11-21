<?php
/* 
 *      changePicOrder.php
 *      Aendert Position eines Bildes in der DB
 *
 */

include('config/mysql.inc.php');

require('./auth/auth_required.inc.php');


define('PUB_ROOT', '../public/data/');


$action = isset($_GET['action']) ? $_GET['action'] : false;

// TODO: refactor: move to lib folder
function checkId($id) {
    if(!is_numeric($id))
    {
        return false;
    }
    else
    {
        if(!inDB($id))
            return false;
        else
            return True;
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
    global $connection;

    $sql_get_all = 'SELECT id, datei_pic AS src, titel AS title, special FROM pictures';
    $json_data = array();
    if ($query = mysqli_query($connection, $sql_get_all)) {
        while ($data = @mysqli_fetch_assoc($query)) {
            array_push($json_data, $data);
        }
    }
    return json_encode($json_data);
}

/**
 * saveToJson
 * Write data to JSON file
 * 
 * @param $data str json-encoded data to be written
 * @param $outfile str filename of json file to be created
 **/
function saveToJson($data, $outfile) {
    $fh = fopen($outfile, 'w');
    if($fh != false) {
        @fwrite($fh, $data);
        @fclose($fh);
        return true;
    } else {
        return false;
    }
}

function inDB($id) // Nicht zum direkten Aufruf gedacht, da keine Ueberpruefung von $id
{
    global $connection;

    $sql = "SELECT id FROM pictures WHERE id='$id'";
    if(!$query = mysqli_query($connection, $sql))
    {
        return False;
    }
    else
    {
        $result = mysqli_fetch_array($query);
        return !is_null($result);
    }
}

switch ($action)
{   
    case false:
        echo "Fehlender Parameter! action = $action";
        break;
        
    default:
        if(!checkId($_GET['other']) || !checkId($_GET['curr']))
        {
            echo "Fehlerhafte ID übergeben! other: ".$_GET['other']." curr: ".$_GET['curr'];
        }
        else
        {
            $sql['delete'] = "DELETE FROM pictures WHERE id={$_GET['other']}";
            $sql['select'] = "SELECT * FROM pictures WHERE id={$_GET['other']}";
            $sql['update'] = "UPDATE pictures SET id={$_GET['other']} WHERE id={$_GET['curr']}";
            $other_data;            
            if($query = mysqli_query($connection, $sql['select'])) // Datensatz drueber holen
            {
                $other_data = mysqli_fetch_array($query);
                $titel = mysqli_real_escape_string($connection, $other_data['titel']);
                $beschreibung = mysqli_real_escape_string($connection, $other_data['beschreibung']);
                $sql['insert'] = "INSERT INTO pictures (id, datei_pic, titel, beschreibung) VALUES ({$_GET['curr']}, '{$other_data['datei_pic']}', '$titel', '$beschreibung')";
                $response = array(
                    "success" => 0,
                    "error_msg"   => ""
                );

                if($query = mysqli_query($connection, $sql['delete'])) // Datensatz drueber loeschen
                {
                    if($query = mysqli_query($connection, $sql['update'])) // Aktuellen Datensatz aktualisieren
                    {
                        // Datensatz drueber (ehem.) unter aktuellem einfuegen
                        if($query = mysqli_query($connection, $sql['insert']))
                        {
                            $response['success'] = 1;
                        }
                        else
                        {
                            $response['success'] = 0; // Fehlercode 0 an JS zurueckgeben
                            $response['error_msg'] = "Einf&uuml;gen des alten Datensatzes fehlgeschlagen! "
                                . mysqli_error($connection) . "\n SQL.: " . $sql['insert'];
                        }
                    }
                    else
                    {
                        $response['success'] = 0;
                        $response['error_msg'] = "Aktualisieren des aktuellen Datensatzes fehlgeschlagen! " .
                            mysqli_error($connection);
                    }
                }
                else
                {
                    $response['success'] = 0;
                    $response['error_msg'] = "L&ouml;schen des alten Datensatzes fehlgeschlagen! " . mysqli_error
                        ($connection);
                }
            }
            else
            {
                $response['success'] = 0;
                $response['error_msg'] = "Holen des alten Datensatzes fehlgeschlagen! " . mysqli_error($connection);
                break;
            }

            echo json_encode($response);
            // Alles in JSON-Datei (Cache) auslagern
            //~ $json_file = PUB_ROOT . "data.json";
            //~ if($json_data = fetchJsonData()) {
                //~ die($json_data);
                //~ if(!saveToJson($json_data, $json_file)) {
                    //~ echo "Fehler: JSON-Datei konnte nicht geschrieben werden!<br />";
                //~ }
            //~ }
        }
        break;
}
?>
