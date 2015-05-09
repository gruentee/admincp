<?php
/* 
 *      changePicOrder.php
 *      Aendert Position eines Bildes in der DB
 *
 */

include('./inc/mysql.inc.php');

define('PUB_ROOT', '../public/data/');


$mode = isset($_GET['mode']) ? $_GET['mode'] : False;


function checkId($id) {
    if(!is_numeric($id))
    {
        return False;
    }
    else
    {
        if(!inDB($id))
            return False;
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
    $sql_get_all = "SELECT id, datei_pic AS src, titel AS title, special FROM pictures";
    $json_data = array();
    if ($query = mysql_query($sql_get_all)) {
        while ($data = @mysql_fetch_assoc($query)) {
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
    $sql = "SELECT id FROM pictures WHERE id='$id'";
    if(!$query = mysql_query($sql))
    {
        return False;
    }
    else
    {
        $result = mysql_fetch_array($query);
        if(is_array($result))
        {
            return True;
        }
        else
        {
            return False;
        }
    }
}

switch ($mode)
{   
    case False:
        echo "Fehlender Parameter! mode = $mode";
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
            if($query = mysql_query($sql['select'])) // Datensatz drueber holen
            {
                $other_data = mysql_fetch_array($query);
                $titel = mysql_real_escape_string($other_data['titel']);
                $beschreibung = mysql_real_escape_string($other_data['beschreibung']);
                $sql['insert'] = "INSERT INTO pictures (id, datei_pic, titel, beschreibung) VALUES ({$_GET['curr']}, '{$other_data['datei_pic']}', '$titel', '$beschreibung')";
                // die(mysql_escape_string($sql['insert'])); // DEBUG

                $response = array(
                    "success" => 0,
                    "error_msg"   => ""
                );

                //~ $response['success'] = 0;
                //~ $response['error_msg'] ="Something went wrong!";
                //~ die(json_encode($response));
                
                if($query = mysql_query($sql['delete'])) // Datensatz drueber loeschen
                {
                    if($query = mysql_query($sql['update'])) // Aktuellen Datensatz aktualisieren
                    {
                        if($query = mysql_query($sql['insert'])) // Datensatz drueber (ehem.) unter aktuellem einfuegen
                        {
                            $response['success'] = 1;// Fehlercode 0 an JS zurueckgeben
                        }
                        else
                        {
                            $response['success'] = 0;
                            $response['error_msg'] = "Einf&uuml;gen des alten Datensatzes fehlgeschlagen! ".mysql_error()."\n SQL.: ".$sql['insert'];
                        }
                    }
                    else
                    {
                        $response['success'] = 0;
                        $response['error_msg'] = "Aktualisieren des aktuellen Datensatzes fehlgeschlagen! ".mysql_error();
                    }
                }
                else
                {
                    $response['success'] = 0;
                    $response['error_msg'] = "L&ouml;schen des alten Datensatzes fehlgeschlagen! ".mysql_error();
                }
            }
            else
            {
                $response['success'] = 0;
                $response['error_msg'] = "Holen des alten Datensatzes fehlgeschlagen! ".mysql_error();
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
