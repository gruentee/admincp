<?php
/*
 * set_sflag.php
 * 
 * Abfragen und Manipulieren des DB-Felds special per AJAX-Call 
 * von list.js
 *
 */

// DB-Verbindung
require('inc/mysql.inc.php');

###########################
/* Funktionsdefinitionen */
###########################

function set_sflag($picID, $flag) {
    // Setzt das DB-Feld special fuer das Bild mit der ID $picID 
    if( is_numeric($flag) AND ( $flag == 1 || $flag == 0 ) ) {
        
        $sql = "UPDATE pictures SET special=$flag
                WHERE id=$picID";
        if(mysql_query($sql)) {
            return True;
        } else {
            print( "Fehler: ".mysql_error() );
            return False;
        }
    }
    else { // Falscher $flag-Wert uebergeben
        return False;
    }
}

function get_sflag() {
    $sql = "SELECT id FROM pictures WHERE special=1";
    $result = mysql_query( $sql );
    if( $result ) {
        if( mysql_num_rows( $result ) > 0) {
            $res = mysql_fetch_array( $result );            
            //print_r($res); // DEBUG
            return $res['id'];
        } else { // Fehler
            return 0;
        }
    } else {
        print( "Fehler: ".mysql_error() );
    }
}

#########################
/* Haupt-Programmfluss */
#########################
switch ($_GET['mode'])
{
    case "set":
        $id = stripslashes($_GET['id']);
        $sflag =  stripslashes($_GET['sflag']);

        if( !isset($id )) {
            mysql_close();
            exit( "Falscher Parameter \"$id\" uebergeben! exit()" );
        }
        else
        {
            if( $sflag > 0 ) { // sflag positiv
                // Da nur ein Bild sflag = 1 besitzen darf, dieses suchen
                $old_sflag = get_sflag();
                if( $old_sflag > 0 ) { // Bild mit sflag vorhanden
                    //~ print ("bla"); // DEBUG
                    // sflag des alten Bildes auf 0 setzten
                    if( set_sflag($old_sflag, 0) ) { 
                        //~ print("bla"); // DEBUG
                        // Neues sflag setzen
                        if( !set_sflag( $id, 1 ) ) { // Fehler
                            echo "Setzen des sflag fehlgeschlagen: ".mysql_error();
                        }
                        else {
                            echo $old_sflag; // TODO: ID des alten Bildes zurueckgeben - unsicher?
                        }
                    } else { // Fehler
                        echo "Setzen des alten sflag auf 0 fehlgeschlagen: ".mysql_error();
                    }
                }
            }
        }
        break;
    default:
        echo get_sflag();
}

//~ get_sflag(); // DEBUG
//~ print( $id ); // DEBUG
//~ print( $sflag ); // DEBUG

// DB-Verbindung schliessen
mysql_close();
?>
