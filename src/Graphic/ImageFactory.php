<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Graphic;

use Aws\S3\S3Client;
use Rf\Core\Exception\BaseException;

/**
 * Class ImageFactory
 * 
 * @since 1.0
 * 
 * @package Rf\Core\Graphic
 */
class ImageFactory {

    /**
     * @var resource $image Image to transform/transformed
     * @since 1.0
     */
    private $image;

    /**
     * @var resource $source Image before the transformations
     * @since 1.0
     */
    private $source;

    /**
     * @var string Path to the source image
     * @since 1.0
     */
    private $sourcePath;

    /**
     * @var string Image format
     * @since 1.0
     */
    private $format;

    /**
     * @var array Available image destination formats
     * @since 1.0
     */
    private $availableFormats = array('gif', 'jpg', 'jpeg', 'png');

    /**
     * @var array Available image source formats
     * @since 1.0
     */
    private $availableFormatsFrom = array('gif', 'jpg', 'jpeg', 'png');

    /**
     * Get an image to transform from a specified format
     *
     * @TODO: Remove from parameter
     *
     * @param string $format Source format
     * @param bool $from Create from path
     * @param string $path Source path
     * @throws BaseException
     */
    public function __construct($format, $from = false, $path = null) {

        $this->imageCreate($format, $from, $path);

    }

    /**
     * Get an image to transform from a specified format
     *
     * @since 1.0
     *
     * @param string $format Source format
     * @param bool $from Create from path
     * @param string $path Source path
     * @throws BaseException
     */
    public function imageCreate($format, $from = false, $path = null) {

        // Remove the case sensitive factor
        $format = strtolower($format);

        if(!in_array($format, $this->availableFormats)) {
            throw new BaseException('Image', 'Format incompatible');
        } elseif($format === 'jpg') {
            $format = 'jpeg';
        }

        if($from && isset($path)) {
            if(!in_array($format, $this->availableFormatsFrom)) {
                throw new BaseException('Image', 'Format incompatible');
            }
            $this->format = $format;
            $this->sourcePath = $path;
            $function = 'imagecreatefrom' . $this->format;
            $this->source = $function($this->sourcePath);
            $this->image = $this->source;
        }

        $this->format = $format;

    }

    public function getWidth() {

        return imagesx($this->image);

    }

    public function getHeight() {

        return imagesy($this->image);

    }
    
    /**
     * Resize the current image
     *
     * @param int $width Width or maximum with of the destination image
     * @param int $height Height or maximum height of the destination image
     * @param bool $force Force the resize to the specified dimensions
     *
     * @throws \Exception
     */
    public function imageResize($width, $height, $force = false) {

        if(!isset($this->image)) {
            throw new \Exception('Source missing for resize');
        }
        
        // Get current size
        $w = imagesx($this->image);
        $h = imagesy($this->image);
        
        // Calculate destination dimensions
        if($force) {
            $wdest = $width;
            $hdest = $height;
        } else {
            $wdest = (($w / $h) > 1) ? $width : ($height * $w / $h);
            $hdest = (($w / $h) < 1) ? $height : $width * $h / $w;
        }
        
        // Create resized image
        $image = imagecreatetruecolor($wdest, $hdest);
        imagecopyresampled($image, $this->image, 0, 0, 0, 0, $wdest, $hdest, $w, $h);
        $this->image = $image;

    }

    /**
     * Crop the current image
     *
     * @param int $x Start of the crop on X axis
     * @param int $y Start of the crop on Y axis
     * @param int $w Length of the crop on X axis
     * @param int $h Length of the crop on Y axis
     *
     * @throws \Exception
     */
    public function imageCrop($x, $y, $w, $h) {

        if(!isset($this->image)) {
            throw new \Exception('Source missing for crop');
        }
        
        // Create cropped image
        $image = imagecreatetruecolor($w, $h);
        imagecopy($image, $this->image, 0, 0, $x, $y, $w, $h);
        $this->image = $image;

    }

    /**
     * Add a png logo to the current image
     *
     * @param string $logoFile Path to the logo file
     *
     * @TODO: Add some position parameters
     */
    public function addLogo($logoFile) {

        $logo = imagecreatefrompng($logoFile);
        $posX = imagesx($this->image) - imagesx($logo);
        $posY = imagesy($this->image) - imagesy($logo);

        imagecopy($this->image, $logo, $posX, $posY, 0, 0, imagesx($logo), imagesy($logo));

    }

    /**
     * Save the current image to a file
     *
     * @param string $path Destination path
     * @param int $quality Quality on a scale from 1 to 100
     * @param null|string $newFormat Destination format if different of source
     *
     * @return mixed
     */
    public function imageSave($path, $quality, $newFormat = null) {

	    // Create the directory if it does not exists
	    if (!is_dir(dirname($path))) {
		    mkdir(dirname($path), 0755, true);
	    }

        // Remove the case sensitive factor
	    $newFormat = strtolower($newFormat);

        if(in_array($newFormat, $this->availableFormats)) {
            $function = 'image' . $newFormat;
        } else {
            $function = 'image' . $this->format;
        }

        return $function($this->image, $path, $quality);

    }

    /**
     * Save the current image to a file
     *
     * @param string $path Destination path
     * @param int $quality Quality on a scale from 1 to 100
     * @param null|string $newFormat Destination format if different of source
     * @return mixed
     */
    public function imageSaveFileSystem($path, $quality, $newFormat = null) {

        // Remove the case sensitive factor
        $format = strtolower($newFormat);

        if(in_array($format, $this->availableFormats)) {
            $function = 'image' . $format;
        } else {
            $function = 'image' . $this->format;
        }

        if($function == 'imagepng') {
	        $quality = 10 - (ceil($quality / 10) - 1);
        }

        return $function($this->image, $path, $quality);

    }

    /**
     * Save the current image to S3
     *
     * @param string $bucket
     * @param string $key
     * @return mixed
     */
    public function imageSaveS3($bucket, $key) {

        ob_start();
        imagejpeg($this->image);
        $image = ob_get_clean();

        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => 'eu-west-1',
            'credentials' => array(
                'key' => rf_config('custom.aws-access_key_id'),
                'secret' => rf_config('custom.aws-secret_access_key')
            )
        ]);

        $result = $s3->putObject(array(
            'Bucket' => $bucket,
            'Key'    => $key,
            'Body'   => $image,
            'ACL'    => 'public-read',
            'ContentType' => 'image/jpeg'
        ));

        return $result;
    }
}