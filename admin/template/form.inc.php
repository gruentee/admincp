<div id="form_header">
    <?=$picData['titel'];?>
</div>
<form action="edit.php?action=<?php echo $action;?>" method="POST" enctype="multipart/form-data">
    <div id="form_col_left">
<?php
  // $id uebergeben, falls Bild bearbeitet wird
  if($action == "edit")
  {
    echo "<input type=\"hidden\" name=\"id\" value=\"$id\" />";
  }
?>
        <p>
            <label for="title">Titel</label>
            <input type="text" name="titel" id="titel" size="20" value="<?=$picData['titel'];?>" />
        </p>
        <p>
            <label for="beschreibung">Beschreibung</label>
        </p>
        <textarea name="beschreibung" id="beschreibung" rows="10" cols="20"><?=$picData['beschreibung'];?></textarea>
<?php
  // Upload-Feld, falls Bild hinzugefuegt wird
  if($action == "add")
  {
      echo "<input type=\"file\" name=\"upload_pic\" id=\"upload_pic\" size=\"20\" />\n";
  }
  else
  {
      echo "<input type=\"hidden\" name=\"datei_pic\" value=\"$picData[datei_pic]\" size=\"20\" />\n";
  }
?>
    </div>
    <div id="form_col_right">
<?php
  // Bild anzeigen, falls im Bearbeiten-Modus
  if($action == "edit")
  {
      print '<img src="../photos/thumbs/' . $picData['datei_pic'] . '"
      alt="' . $picData['titel'] . '" />';
  }
?>
    </div>
    <div class="cleaner">&nbsp;</div>
    <div id="form_footer">
        <input type="submit" name="submit" value="&Uuml;bernehmen" class="button" /> <input type="button" onclick="javascript: window.location.replace('list.php');" value="Abbrechen" class="button" />
    </div>
</form>
