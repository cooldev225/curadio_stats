<?php

/**
 * CUGATE
 *
 * @package		CuLib
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

/**
 * IMAGE-FFMPEG Class
 *
 * @package		CuLib
 * @subpackage	External Library
 * @category	IMAGE
 * @author		Khvicha Chikhladze
 */

class cug__img_ffmpeg {
	private $image;
	public $width;
	public $height;
	public $mimetype;
	
	
	function __construct($filename) {
		
		$info= @getimagesize($filename);
		
			if($info) {
				
				switch($info['mime']) {
					case 'image/jpeg':
					case 'image/png':
					case 'image/gif':
					case 'image/tiff':
					case 'image/bmp':
					case 'image/x-ms-bmp':
					case 'image/vnd.microsoft.icon':
						$this->image = $filename;
						$this->width = $info[0];
						$this->height = $info[1];
						$this->mimetype = $info['mime'];
					break;
					
					default:
						$this->mimetype = "";
					break;
				}

			}
	}

	//-----------------------
	public function convert($save_path) {
		global $ffmpeg_tool;
		
		$command = "$ffmpeg_tool -loglevel 0 -y -i \"".$this->image."\" \"$save_path\"";
		exec($command, $buffer);
		
			if(file_exists($save_path) && filesize($save_path) > 0) {
				return TRUE;
			}
			else {
				return FALSE;
			}
	}
	//-----------------------
	public function resize($new_width, $new_height, $save_path) {
		global $ffmpeg_tool;
		
		$command = "$ffmpeg_tool -loglevel 0 -y -i \"".$this->image."\" -vf scale=$new_width:$new_height \"$save_path\"";
		exec($command, $buffer);
		
			if(file_exists($save_path) && filesize($save_path) > 0) {
				return TRUE;
			}
			else {
				return FALSE;
			}
	}
}


# ========================================================================#
#
#  Author:    Rajani .B
#  Version:	 1.0
#  Date:      07-July-2010
#  Purpose:   Resizes and saves image
#  Requires : Requires PHP5, GD library.
#  Usage Example:
#                     include("classes/resize_class.php");
#                     $resizeObj = new cug__img('images/cars/large/input.jpg');
#                     $resizeObj -> resizeImage(150, 100, 0);
#                     $resizeObj -> saveImage('images/cars/large/output.jpg', 100);
#
#
# ========================================================================#


Class cug__img
{
	// *** Class variables
	private $image;
	public $width;
	public $height;
	private $imageResized;

	function __construct($fileName)
	{
		// *** Open up the file
		$this->image = $this->openImage($fileName);

		// *** Get width and height
		$this->width  = imagesx($this->image);
		$this->height = imagesy($this->image);
	}

	## --------------------------------------------------------

	private function openImage($file)
	{
	// *** Get extension
		$extension = strtolower(strrchr($file, '.'));

		switch($extension)
		{
			case '.jpg':
			case '.jpeg':
				$img = @imagecreatefromjpeg($file);
			break;
			
			case '.gif':
				$img = @imagecreatefromgif($file);
			break;
			
			case '.png':
				$img = @imagecreatefrompng($file);
			break;
			
			default:
				$img = false;
			break;
			}
			return $img;
			}

			## --------------------------------------------------------

			public function resizeImage($newWidth, $newHeight, $option="auto")
			{
			// *** Get optimal width and height - based on $option
				$optionArray = $this->getDimensions($newWidth, $newHeight, $option);

				$optimalWidth  = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];


				// *** Resample - create image canvas of x, y size
				$this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
				imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);


				// *** if option is 'crop', then crop too
				if ($option == 'crop') {
				$this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
				}
				}

				## --------------------------------------------------------
					
				private function getDimensions($newWidth, $newHeight, $option)
				{

				switch ($option)
				{
					case 'exact':
					$optimalWidth = $newWidth;
					$optimalHeight= $newHeight;
					break;
					case 'portrait':
					$optimalWidth = $this->getSizeByFixedHeight($newHeight);
					$optimalHeight= $newHeight;
					break;
					case 'landscape':
					$optimalWidth = $newWidth;
							$optimalHeight= $this->getSizeByFixedWidth($newWidth);
							break;
							case 'auto':
							$optionArray = $this->getSizeByAuto($newWidth, $newHeight);
							$optimalWidth = $optionArray['optimalWidth'];
							$optimalHeight = $optionArray['optimalHeight'];
							break;
					case 'crop':
							$optionArray = $this->getOptimalCrop($newWidth, $newHeight);
							$optimalWidth = $optionArray['optimalWidth'];
							$optimalHeight = $optionArray['optimalHeight'];
							break;
				}
									return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
				}

				## --------------------------------------------------------

									private function getSizeByFixedHeight($newHeight)
									{
									$ratio = $this->width / $this->height;
									$newWidth = $newHeight * $ratio;
									return $newWidth;
				}

				private function getSizeByFixedWidth($newWidth)
					{
					$ratio = $this->height / $this->width;
					$newHeight = $newWidth * $ratio;
					return $newHeight;
				}

					private function getSizeByAuto($newWidth, $newHeight)
					{
					if ($this->height < $this->width)
					// *** Image to be resized is wider (landscape)
					{
					$optimalWidth = $newWidth;
					$optimalHeight= $this->getSizeByFixedWidth($newWidth);
					}
					elseif ($this->height > $this->width)
					// *** Image to be resized is taller (portrait)
					{
					$optimalWidth = $this->getSizeByFixedHeight($newHeight);
					$optimalHeight= $newHeight;
					}
					else
						// *** Image to be resizerd is a square
						{
						if ($newHeight < $newWidth) {
						$optimalWidth = $newWidth;
						$optimalHeight= $this->getSizeByFixedWidth($newWidth);
					} else if ($newHeight > $newWidth) {
					$optimalWidth = $this->getSizeByFixedHeight($newHeight);
					$optimalHeight= $newHeight;
					} else {
					// *** Sqaure being resized to a square
					$optimalWidth = $newWidth;
					$optimalHeight= $newHeight;
					}
				}

							return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
				}

				## --------------------------------------------------------

				private function getOptimalCrop($newWidth, $newHeight)
				{

				$heightRatio = $this->height / $newHeight;
				$widthRatio  = $this->width /  $newWidth;

				if ($heightRatio < $widthRatio) {
				$optimalRatio = $heightRatio;
				} else {
				$optimalRatio = $widthRatio;
				}

				$optimalHeight = $this->height / $optimalRatio;
				$optimalWidth  = $this->width  / $optimalRatio;

				return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
				}

				## --------------------------------------------------------

				private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight)
					{
					// *** Find center - this will be used for the crop
					$cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
					$cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );

					$crop = $this->imageResized;
					//imagedestroy($this->imageResized);

					// *** Now crop from center to exact requested size
					$this->imageResized = imagecreatetruecolor($newWidth , $newHeight);
					imagecopyresampled($this->imageResized, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);
				}

				## --------------------------------------------------------

				public function saveImage($savePath, $imageQuality="100")
					{
					// *** Get extension
					$extension = strrchr($savePath, '.');
					$extension = strtolower($extension);

					switch($extension)
					{
					case '.jpg':
					case '.jpeg':
						if (imagetypes() & IMG_JPG) {
						imagejpeg($this->imageResized, $savePath, $imageQuality);
					}
					break;

					case '.gif':
						if (imagetypes() & IMG_GIF) {
						imagegif($this->imageResized, $savePath);
					}
					break;

					case '.png':
					// *** Scale quality from 0-100 to 0-9
					$scaleQuality = round(($imageQuality/100) * 9);

				// *** Invert quality setting as 0 is best, not 9
				$invertScaleQuality = 9 - $scaleQuality;

				if (imagetypes() & IMG_PNG) {
				imagepng($this->imageResized, $savePath, $invertScaleQuality);
					}
					break;

						// ... etc

						default:
						// *** No extension - No save.
						break;
					}

					imagedestroy($this->imageResized);
				}


			## --------------------------------------------------------

		}
		


/**
 * Detect Image Type
 * 
 * @param string $file
 * @return string (Image Extension)
 */
function cug_img_detect_type($file) {
	$image_info = getimagesize($file);


	if ($image_info['mime'] == 'image/jpeg') {
		return 'jpg';
	}
	elseif($image_info['mime'] == 'image/gif') {
		return 'gif';
	}
	elseif($image_info['mime'] == 'image/png') {
		return 'png';
	}
	elseif($image_info['mime'] == 'image/tiff') {
		return 'tif';
	}
	else
		return '';
}


/**
 * Validate Image
 * 
 * @param string $file
 * @param array $img_ext_arr (Array of valid image extensions, like: array('jpg', 'png', 'tif'))
 * @return boolean
 */
function cug_img_validate($file, $img_ext_arr=array()) {
    $img_ext = cug_img_detect_type($file);
    $key = array_search($img_ext, $img_ext_arr);
    
    if($key !== false)
        return true;
    else 
        return false;
}
?>
