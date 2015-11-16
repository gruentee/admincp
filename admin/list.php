<?php
/*
 *      list.php
 *      Listet die Bilder aus der Datenbank auf
 *
 */

require('config/config.inc.php');

// DB-Verbindung
require('config/mysql.inc.php');


// Header einbinden
include('template/header.inc.php');


$sql = 'SELECT * FROM pictures';
?>

      <div id="pics_container">
        <ul id="pics">
       <?php
          if($query = mysqli_query($connection, $sql))
          {
                while($data = mysqli_fetch_array($query))
                {
                    $sflag = false;
          ?>
          <li>
            <img src="../photos/thumbs/<?=$data['datei_pic'];?>"
              id="<?php echo $data['id'];?>" alt="<?=$data['titel']; ?>">
            <ul class="pic_nav">
              <li>
                <a href="#" class="button_movel" title="nach links bewegen">&nbsp;</a>
              </li>
              <li>
                <a href="#" class="button_mover" title="nach rechts bewegen">&nbsp;</a>
              </li>
              <li>
                <a href="edit.php?mode=edit&id=<?=$data['id'];?>" class="button_edit" title="Bearbeiten">&nbsp;</a>
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
            echo 'Beim Abfragen der Bilder ist ein Fehler aufgetreten!<br />MySQL sagt:<br />' . mysqli_error
                ($connection);
        }
        ?>
        </ul>
      </div>
      <div style="clear: both;">&nbsp;</div>
<?php
    // Footer einbinden
    include('template/footer.inc.php');
?>