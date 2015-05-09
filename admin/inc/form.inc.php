<div id="form_header">
<?php echo $picData['titel'];?>
</div>
<form action="edit.php?mode=<?php echo $mode;?>" method="POST" enctype="multipart/form-data">
    <div id="form_col_left">
<?php
  // $id uebergeben, falls Bild bearbeitet wird
  if($mode == "edit")
  {
    echo "<input type=\"hidden\" name=\"id\" value=\"$id\" />";
  }
?>
        <p>
            <label for="title">Titel</label>
            <input type="text" name="titel" id="titel" size="20" value="<?php echo $picData['titel'];?>" />
        </p>
        <p>
            <label for="beschreibung">Beschreibung</label>
        </p>
        <textarea name="beschreibung" id="beschreibung" rows="10" cols="20"><?php echo $picData['beschreibung'];?></textarea>
<?php
  // Upload-Feld, falls Bild hinzugefuegt wird
  if($mode == "add")
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
  if($mode == "edit")
  {
      print "<img src=\"http://www.nix-pauleit.de/photos/thumbs/".$picData[datei_pic]."\" alt=\"$picData[titel]\" />";
  }
?>
    </div>
    <div class="cleaner">&nbsp;</div>
    <div id="form_footer">
        <input type="submit" name="submit" value="&Uuml;bernehmen" class="button" /> <input type="button" onclick="javascript: window.location.replace('list.php');" value="Abbrechen" class="button" />
    </div>
</form>
