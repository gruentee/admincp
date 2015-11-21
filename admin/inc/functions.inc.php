<?php
/*
 * functions.inc.php
 * Stellt die benoetigten Funktionen bereit
 *
 */

function getPictureFromDB($id) // Bild aus DB abrufen
{
    $sql = "SELECT * 
            FROM pictures 
            WHERE id=\"$id\"";
    if(!$query = mysql_query($sql))
    {
        echo "Fehler beim Senden des Querys: ".mysql_error();    
    }
    else
    {
        if(mysql_num_rows($query) < 1)
        {
            echo "Das Bild mit der ID $id ist nicht vorhanden!";  
            return false;      
        }    
        else
        {
            $result = mysql_fetch_array($query);
            return $result;        
        }
    }
}

function savePicture($data) // Bild speichern
{
    $sql = "UPDATE pictures 
			SET datei_pic=\"$data[datei_pic]\", titel=\"$data[titel]\", beschreibung=\"$data[beschreibung]\"
			WHERE id=\"$data[id]\"";
    if(!$query = mysql_query($sql))
    {
        echo "Fehler beim Senden des Querys: ".mysql_error();
        return false;        
    }   
    else
    {
        return true;    
    }
}

function addPicture($data)
{
	$sql = "INSERT INTO pictures
			SET titel=\"$data[titel]\", beschreibung=\"$data[beschreibung]\", datei_pic=\"$data[upload_pic]\"";
   
    if(!$query = mysql_query($sql))
    {
        echo "Fehler beim Senden des Querys: ".mysql_error();
        return False;        
    }   
    else
    {
        return True;    
    }			 
}

function checkFileType($file)
{
	$filetype = getimagesize($file);
	if($filetype[2] >= 1 OR $filetype <= 3)
    {
    	return true;
	}
    else
	{
        return false;
	}
}

function uploadFile($file)
{
	if(!is_file($file['tmp_name']))
	{
		$rueckgabe['err'] = true;
		$rueckgabe['err_code'] = 'Fehler: Datei existiert nicht!';
	}
	elseif(!checkFileType($file['tmp_name']))
	{
		$rueckgabe['err'] = true;		
		$rueckgabe['err_code'] = 'Es k&ouml;nnen nur JPG, GIF oder PNG-Dateien hochgeladen werden.';
	}
	else
	{
		if(move_uploaded_file($file['tmp_name'], $picDir.basename($file['name'])))
		{
			$rueckgabe['bild'] = $picDir.basename($file['name']);
			$rueckgabe['err'] = false;
		}
		else
		{
			$rueckgabe['err'] = $file['error'] == 0 ? false : true;
			$rueckgabe['err_code'] = 'Fehler beim Hochladen der Datei<br />';
		}
	}
	return $rueckgabe;
}


function generateThumbnail($pic) // TODO: generateThumbnail implementieren
{
	return true;
}

function verifyFormData($formData)
{
	$fehler = array();
	array_pop($formData); // Letztes Element (Submit-Button loeschen)
	foreach($formData as $field => $value)
	{
		if(strlen($value) < 1)
		{
			$fehler[] = "Das Feld $field wurde nicht richtig ausgef&uuml;llt.";
		}
		
		if(isset($_FILES['upload_pic'])) // Bild angehaengt? 
		{
			$bild = uploadFile($_FILES['upload_pic']);
			if($bild['err'] == true) // Fehler beim Hochladen
			{
				$fehler[] = $bild['err_code'];
			}
			else
			{
				$formdata['upload_pic'] = $bild['bild']; // Pfad zum Bild in $formData-Array einfuegen
			}
		} 
	}
	
	if(count($fehler) > 0)
	{
		$formData['err'] = $fehler;
	}
	return $formData;
}

/**
 * For debugging purposes
 *
 * @param $array to be printed
 */
function outputArray($array)
{	
	foreach ($array as $key => $value)
	{
		if(is_array($value))
		{
			outputArray($value);
		}
		else
		{
			echo "$key => $value <br />";
		}
	}
}
?>
