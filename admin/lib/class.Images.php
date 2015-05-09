<?php

/* ------------------------------------------------------------------------ */          
/* 	class.Images.php														*/
/*	Eine Klasse, um mit Bildern rumzuhantieren
/* ------------------------------------------------------------------------ */
/* Manuel Reinhard, manu@sprain.ch
/* Twitter: @sprain
/* Web: www.sprain.ch
/* Beware, known for being one sandwich short of a picnic.
/* ------------------------------------------------------------------------ */
/* History:
/* 2010/05/06 - Manuel Reinhard - corrected the date on the change below this one	
/* 2010/05/05 - Manuel Reinhard - added several minor improvements, thanks to Christian, http://hymnos.existenz.ch/
/* 2010/05/04 - Manuel Reinhard - when it all started
/* ------------------------------------------------------------------------ */  


/**************************************
 * Don't change anything from here on
 * if you don't know what you're doing.
 * Otherwise the earth might disappear
 * in a large black hole. We'll blame you!
 **************************************/
class Image {

	//Set variables
	protected $image = "";
	protected $imageInfo = array();
	protected $fileInfo = array();
	protected $tmpfile = array();
	protected $pathToTempFiles = "";


	/**
	 * Constructor of this class
	 * @param string $image (path to image)
	 */
	public function __construct($image){
	
		//Set path to temp files
		$this->setPathToTempFiles($_SERVER["DOCUMENT_ROOT"]);
	
		//Set tmpfile to save temporary files
		$this->tmpfile = tempnam($this->pathToTempFiles, "classImagePhp_");
	
		//Does file exist?
		if(file_exists($image)){
			$this->image  = $image;
			$this->readImageInfo();
		}else{
			throw new Exception("File does not exist: ".$image);
		}
	}//function



	/**
	 * Constructor of this class
	 * @param string $image (path to image)
	 */
	public function __destruct(){
		if(file_exists($this->tmpfile)){
			unlink($this->tmpfile);
		}//if
	}//function
	



	/**
	 * Read and set some basic info about the image
	 * @param string $image (path to image)
	 */
	protected function readImageInfo(){
		
		//get data
		$data = getimagesize($this->image);
		
		//make readable
		$this->imageInfo["width"] = $data[0];
		$this->imageInfo["height"] = $data[1];
		$this->imageInfo["imagetype"] = $data[2];
		$this->imageInfo["htmlWidthAndHeight"] = $data[3];
		$this->imageInfo["mime"] = $data["mime"];
		$this->imageInfo["channels"] = $data["channels"];
		$this->imageInfo["bits"] = $data["bits"];
		
		return true;
		
	}//function	
	
	
	
	/************************************
	/* SETTERS
	/************************************
	
	/**
	 * Sets path to temp files
	 * @param string $path
	 */
	public function setPathToTempFiles($path){
		$path = realpath($path).DIRECTORY_SEPARATOR;
		$this->pathToTempFiles = $path;
		return true;
	}
	
	
	/**
	 * Sets new main image
	 * @param string $pathToImage
	 */
	protected function setNewMainImage($pathToImage){
	
		//set now
		$this->image = $pathToImage;
		
		//Read new image info
		$this->readImageInfo();
		
		return true;
		
	}//function
	
	
	
	
	/************************************
	/* ACTIONS
	/************************************
	
	/**
	 * Resizes an image
	 * Some portions of this function as found on
	 * http://www.bitrepository.com/resize-an-image-keeping-its-aspect-ratio-using-php-and-gd.html
	 * @param int $max_width
	 * @param int $max_height
	 * @param string $method
	 *				 fit = Fits image into width and height while keeping original aspect ratio. Expect your image not to use the full area.	
	 *				 crop = Crops image to fill the area while keeping original aspect ratio. Expect your image to get, well, cropped.
	 *               fill = Fits image into the area without taking care of any ratios. Expect your image to get deformed.
	 *
	 * @param string $cropAreaLeftRight
	 * 				 l = left
	 *               c = center
	 *               r = right
	 *
	 * @param string $cropAreaBottomTop
	 * 				 t = top
	 *               c = center
	 *               b = bottom
	 */
	public function resize($max_width, $max_height, $method="fit", $cropAreaLeftRight="c", $cropAreaBottomTop="c"){
	
		//Get data
		$width  = $this->getWidth();
		$height = $this->getHeight();
		$type   = $this->getType();
		$mime   = $this->getMimeType();
		$landscape = false;
		$newImage_landscape = false;
		$newImage_square = false;
		
		//Set new data
		$newImage_width = $max_width;
		$newImage_height = $max_height;
		$srcX = 0;
		$srcY = 0;
	
		
		//is the image landscape or portrait?
		if($this->getRatioWidthToHeight() >= 1){
			$landscape = "true";
		}//if
		
	
		//Get ratio of max_width : max_height
		$ratioOfMaxSizes = $max_width / $max_height;
		if($ratioOfMaxSizes > 1){
			$newImage_landscape = true;
		}elseif($ratioOfMaxSizes == 1){
			$newImage_square = true;
		}//if
		
		//Want to fit in the area?
		if($method == "fit"){
			
			if($ratioOfMaxSizes >= $this->getRatioWidthToHeight()){
				$max_width = $max_height * $this->getRatioWidthToHeight();
			}else{
				$max_height = $max_width * $this->getRatioHeightToWidth();
			}//if
		
			//set image data again
			$newImage_width = $max_width;
			$newImage_height = $max_height;
		
		
		//or want to crop it?	
		}elseif($method == "crop"){
	
			//set width and height
			if($newImage_landscape){
				$max_height = $max_width * $this->getRatioHeightToWidth();
			}elseif($newImage_square){
				if($landscape){
					$max_width = $max_height * $this->getRatioWidthToHeight();
				}else{
					$max_height = $max_width * $this->getRatioHeightToWidth();
				}//if
			}else{
				$max_width = $max_height * $this->getRatioWidthToHeight();
			}//if
			
			
			//which area to crop?
			if($cropAreaLeftRight == "r"){
				$srcX = $width - (($newImage_width / $max_width) * $width);
			}elseif($cropAreaLeftRight == "c"){
				$srcX = ($width/2) - ((($newImage_width / $max_width) * $width) / 2);
			}//if//if
			
			if($cropAreaBottomTop == "b"){
				$srcY = $height - (($newImage_height / $max_height) * $height);
			}elseif($cropAreaBottomTop == "c"){
				$srcY = ($height/2) - ((($newImage_height / $max_height) * $height) / 2);
			}//if//if
		
		}//if
			
	
		//set some function stuff
		list($image_create_func, $image_save_func) = $this->getFunctionNames();
		
		
		//Let's get it on, create image!
		$imageC = ImageCreateTrueColor($newImage_width, $newImage_height);
		$newImage = $image_create_func($this->image);
		ImageCopyResampled($imageC, $newImage, 0, 0, $srcX, $srcY, $max_width, $max_height, $width, $height);
		
		
		//Set image
		if(!$image_save_func($imageC, $this->tmpfile)){
			throw new Exception("Cannot save file ".$this->tmpfile);
		}//if
		
		//Set new main image
		$this->setNewMainImage($this->tmpfile);
		
		//Free memory! And free Nelson Mandela, too!
		imagedestroy($imageC);
	
	}//function
	
	
	
	
	/**
	 * Roates an image
	 */
	public function rotate($degrees){
	
		//set some function stuff
		list($image_create_func, $image_save_func) = $this->getFunctionNames();
		
		//do it
		$source = $image_create_func($this->image);
		$rotate = imagerotate($source, $degrees, 0);		
		$image_save_func($rotate, $this->tmpfile);
		
		//Set new main image
		$this->setNewMainImage($this->tmpfile);
		
		return true;
	
	}//function
	
	/**
	 * Sends image data to browser
	 */
	public function display(){
		$mime = $this->getMimeType();
		header("Content-Type: ".$mime);
		readfile($this->image);
	}//function
	
	
	/**
	 * Sends html code to display image
	 */
	public function displayHTML($alt="", $title="", $class=""){
		$code = '<img src="'.$this->image.'" alt="'.$alt.'" title="'.$title.'" class="'.$class.'" id="'.$id.'" width="'.$this->getWidth().'" height="'.$this->getHeight().'"/>';
		print $code;
		return true;
	}//function
	
	
	
	/**
	 * Saves image to file
	 */
	public function save($filename, $path="", $extension=""){
	
		//add extension
		if($extension == ""){
			$filename .= $this->getExtension(true);
		}else{
			$filename .= ".".$extension;
		}//if
	
		//add trailing slash if necessary
		if($path != ""){
			$path = realpath($path).DIRECTORY_SEPARATOR;
		}//if
	
		//create full path
		$fullPath = $path.$filename;
	
		//Copy file
		if(!copy($this->image, $fullPath)){
			throw new Exception("Cannot save file ".$fullPath);
		}//if
		
		//Set new main image
		$this->setNewMainImage($fullPath);
		
		return true;
		
	}//function
	
	

	
	
	/************************************
	/* CHECKERS
	/************************************
	
	/**
	 * Checks whether image is RGB
	 * @return bool
	 */
	public function isRGB(){
		if($this->imageInfo["channels"] == 3){
			return true;
		}//if
		return false;
	}//function	
	
	
	/**
	 * Checks whether image is RGB
	 * @return bool
	 */
	public function isCMYK(){
		if($this->imageInfo["channels"] == 4){
			return true;
		}//if
		return false;
	}//function	
	
	/**
	 * Checks ratio width:height
	 * Examples:
	 * Ratio must be 4:3 > checkRatio(4,3)
	 * Ratio must be 4:3 or 3:4 > checkRatio(4,3, true)
	 * @return bool
	 */
	public function checkRatio($ratio1, $ratio2, $ignoreOrientation=false){
		
		//get actual ratio
		$actualRatioWidthToHeight = $this->getRatioWidthToHeight();
		$actualRatioHeightToWidth = $this->getRatioHeightToWidth();
		
		//get ratio it should have
		$shouldBeRatio = $ratio1 / $ratio2;
		
		//does it match?
		if($actualRatioWidthToHeight == $shouldBeRatio){
			return true;
		}//if
		
		if($ignoreOrientation == true && $actualRatioHeightToWidth == $shouldBeRatio){
			return true;
		}//if
		
		return false;
		
		
	}//function
	
	
	
	/************************************
	/* GETTERS
	/************************************
	
	
	/**
	 * Returns function names
	 */
	protected function getFunctionNames(){
	
		//set some function stuff
		switch ($this->getType()){
			case 'jpeg':
			    $image_create_func = 'ImageCreateFromJPEG';
			    $image_save_func = 'ImageJPEG';
			    break;
			
			case 'png':
			    $image_create_func = 'ImageCreateFromPNG';
			    $image_save_func = 'ImagePNG';
			    break;
			
			case 'bmp':
			    $image_create_func = 'ImageCreateFromBMP';
			    $image_save_func = 'ImageBMP';
			    break;
			
			case 'gif':
			    $image_create_func = 'ImageCreateFromGIF';
			    $image_save_func = 'ImageGIF';
			    break;
			
			case 'vnd.wap.wbmp':
			    $image_create_func = 'ImageCreateFromWBMP';
			    $image_save_func = 'ImageWBMP';
			    break;
			
			case 'xbm':
			    $image_create_func = 'ImageCreateFromXBM';
			    $image_save_func = 'ImageXBM';
			    break;
			
			default: 
				$image_create_func = 'ImageCreateFromJPEG';
			    $image_save_func = 'ImageJPEG';
		}//switch
		
		
		return array($image_create_func, $image_save_func);
	
	}//function
	
	
	
	/**
	 * return info about the image
	 */
	public function getImageInfo(){
		return $this->imageInfo;
	}//function	
	
	/**
	 * return info about the file
	 */
	public function getFileInfo(){
		return $this->fileInfo;
	}//function	
	
	
	/**
	 * Gets width of image
	 * @return int
	 */
	public function getWidth(){
		return $this->imageInfo["width"];
	}//function	
	
	/**
	 * Gets height of image
	 * @return int
	 */
	public function getHeight(){
		return $this->imageInfo["height"];
	}//function	
	
	/**
	 * Gets type of image
	 * @return string
	 */
	public function getExtension($withDot=false){
	
		$extension = image_type_to_extension($this->imageInfo["imagetype"]);
		$extension = str_replace("jpeg", "jpg", $extension);
		if(!$withDot){
			$extension = substr($extension, 1);
		}//if	
		
		return $extension;
	}//function	


	/**
	 * Gets mime type of image
	 * @return string
	 */
	public function getMimeType(){
		return $this->imageInfo["mime"];
	}//function	

	/**
	 * Gets mime type of image
	 * @return string
	 */
	public function getType(){
		return substr(strrchr($this->imageInfo["mime"], '/'), 1);
	}//function	


	/**
	 * Get filesize
	 * @return string
	 */
	public function getFileSizeInBytes(){
		return filesize($this->image);
	}//function	


	/**
	 * Get filesize
	 * @return string
	 */
	public function getFileSizeInKiloBytes(){
		$size = $this->getFileSizeInBytes();
		return $size/1024;
	}//function	

	
	/**
	 * Returns a human readable filesize
	 * @author      wesman20 (php.net)
	 * @author      Jonas John
	 * @author		Manuel Reinhard
	 * @version     0.3
	 * @link        http://www.jonasjohn.de/snippets/php/readable-filesize.htm
	 */
	public function getFileSize() {
	 
	 	$size = $this->getFileSizeInBytes();
	 	
	    // Adapted from: http://www.php.net/manual/en/function.filesize.php
	    $mod = 1024;
	 
	    $units = explode(' ','B KB MB GB TB PB');
	    for ($i = 0; $size > $mod; $i++) {
	        $size /= $mod;
	    }//for
	 
	 	//round differently depending on unit to use
	 	//(added by Manuel Reinhard)
	 	if($i < 2){
	 		$size = round($size);
	 	}else{
	 		$size = round($size, 2);
	 	}//if
	 	
	 	//return
	    return $size . ' ' . $units[$i];
	}//function
	
	
	/**
	 * Gets ratio width:height
	 * @return float
	 */
	public function getRatioWidthToHeight(){
		return $this->imageInfo["width"] / $this->imageInfo["height"];
	}//function	

	/**
	 * Gets ratio height:width
	 * @return float
	 */
	public function getRatioHeightToWidth(){
		return $this->imageInfo["height"] / $this->imageInfo["width"];
	}//function	



}//class

?>