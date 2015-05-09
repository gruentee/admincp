<?php
/*
 *      list.php
 *      Listet die Bilder aus der Datenbank auf
 *
 */

define("BASE_URL", "..");

// Header einbinden
include('./inc/header.inc.php');

// DB-Verbindung
require('inc/mysql.inc.php');

$sql = 'SELECT * FROM pictures';
?>

      <div id="pics_container">
        <ul id="pics">
       <?php
          if($query = mysql_query($sql))
          {
                while($data = mysql_fetch_array($query))
                {
                    $sflag = false;
          ?>
          <li>
            <img src="<?php echo BASE_URL;?>/photos/thumbs/<?php echo $data['datei_pic'];?>"
              id="<?php echo $data['id'];?>" alt="<?php echo $data['titel']; ?>">
            <ul class="pic_nav">
              <li>
                <a href="#" class="button_movel" title="nach links bewegen">&nbsp;</a>
              </li>
              <li>
                <a href="#" class="button_mover" title="nach rechts bewegen">&nbsp;</a>
              </li>
              <li>
                <a href="edit.php?mode=edit&id=<?php echo $data['id'];?>" class="button_edit" title="Bearbeiten">&nbsp;</a>
              </li>
              <li>
                <a href="#" class="button_delete" title="Löschen">&nbsp;</a>
              </li>
              <li>
                <a href="#" class="button_star" title="Markieren">&nbsp;</a>
              </li>
            </ul>
          </li>
          <?php
                }
        }
        else
        {
            echo 'Beim Abfragen der Bilder ist ein Fehler aufgetreten!<br />MySQL sagt:<br />'.mysql_error();
        }
        ?>
        </ul>
      </div>
      <div style="clear: both;">&nbsp;</div>
<?php
    // Footer einbinden
    include('./inc/footer.inc.php');
