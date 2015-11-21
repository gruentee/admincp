<?php
/*
 * set_sflag.php
 * 
 * Abfragen und Manipulieren des DB-Felds special per AJAX-Call 
 * von ui.js
 *
 */
require('auth/auth_required.inc.php');
// DB-Verbindung
require('config/mysql.inc.php');

###########################
/* Funktionsdefinitionen */
###########################

function set_sflag($picID, $flag) {
    global $connection;

    // Setzt das DB-Feld special fuer das Bild mit der ID $picID 
    if( is_numeric($flag) AND ( $flag == 1 || $flag == 0 ) ) {
        
        $sql = "UPDATE pictures SET special=$flag
                WHERE id=$picID";
        if(mysqli_query($connection, $sql)) {
            return true;
        } else {
            print( "Fehler: " . mysqli_error($connection) );
        }
    }
    return false;
}

/**
 * Get id of flagged picture
 *
 * @return int id of returned picture record, 0 if none is found
 */
function get_sflag_id() {
    global $connection;

    $sql = "SELECT id FROM pictures WHERE special=1";
    $result = mysqli_query($connection, $sql);
    if( $result ) {
        if( mysqli_num_rows( $result ) > 0) {
            $res = mysqli_fetch_array( $result );
            return $res['id'];
        } else
        {
            return 0;
        }
    } else {
        echo 'MySQL-Fehler: ' . mysqli_error($connection);
    }
}

#########################
/* Haupt-Programmfluss */
#########################
switch ($_GET['action'])
{
    case "set":
        $id = stripslashes($_GET['id']);
        $sflag =  stripslashes($_GET['sflag']);

        if(!isset($id)) {
            echo "Falscher Wert \"$id\" uebergeben!";
        }
        else
        {
            if( $sflag > 0 ) { // sflag positiv
                // Da nur ein Bild sflag = 1 besitzen darf, dieses suchen
                $old_pic_id = get_sflag_id();
                if( $old_pic_id > 0 ) { // Bild mit sflag vorhanden
                    if( set_sflag($old_pic_id, 0) ) {
                        // Neues sflag setzen
                        if( !set_sflag( $id, 1 ) ) { // Fehler
                            echo "Setzen des sflag fehlgeschlagen: " . mysqli_error($connection);
                        }
                        else {
                            echo $old_pic_id; // TODO: ID des alten Bildes zurueckgeben - unsicher?
                        }
                    } else { // Fehler
                        echo "Setzen des alten sflag auf 0 fehlgeschlagen: " . mysqli_error($connection);
                    }
                }
                else
                {
                    echo "Bild mit ID $old_pic_id nicht vorhanden!";
                }
            }
        }
        break;
    default:
        echo get_sflag_id();
}
// DB-Verbindung schliessen
mysqli_close($connection);
?>
