<?php
   # ========================================================================#
   #
   #  This work is licensed under the Creative Commons Attribution 3.0 Unported
   #  License. To view a copy of this license,
   #  visit http://creativecommons.org/licenses/by/3.0/ or send a letter to
   #  Creative Commons, 444 Castro Street, Suite 900, Mountain View, California,
   #  94041, USA.
   #
   #  All rights reserved.
   #
   #  Author:    Jarrod Oberto
   #  Version:   1.5.1
   #  Date:      10-05-11
   #  Purpose:   Provide tools for image manipulation using GD
   #  Param In:  See functions.
   #  Param Out: Produces a resized image
   #  Requires : Requires PHP GD library.
   #  Usage Example:
   #                     include("lib/php_image_magician.php");
   #                     $magicianObj = new resize('images/car.jpg');
   #                     $magicianObj -> resizeImage(150, 100, 0);
   #                     $magicianObj -> saveImage('images/car_small.jpg', 100);
   #
   #        - See end of doc for more examples -
   #
   #  Supported file types include: jpg, png, gif, bmp, psd (read)
   #
   #
   #
   #  The following functions are taken from phpThumb() [available from
   #    http://phpthumb.sourceforge.net], and are used with written permission
   #  from James Heinrich.
   #    - GD2BMPstring
   #      - GetPixelColor
   #      - LittleEndian2String
   #
   #  The following functions are from Marc Hibbins and are used with written
   #  permission (are also under the Attribution-ShareAlike
   #  [http://creativecommons.org/licenses/by-sa/3.0/] license.
   #    -
   #
   #  PhpPsdReader is used with written permission from Tim de Koning.
   #  [http://www.kingsquare.nl/phppsdreader]
   #
   #
   #
   #  Modificatoin history
   #  Date      Initials  Ver Description
   #  10-05-11  J.C.O   0.0 Initial build
   #  01-06-11  J.C.O   0.1.1   * Added reflections
   #              * Added Rounded corners
   #              * You can now use PNG interlacing
   #              * Added shadow
   #              * Added caption box
   #              * Added vintage filter
   #              * Added dynamic image resizing (resize on the fly)
   #              * minor bug fixes
   #  05-06-11  J.C.O   0.1.1.1 * Fixed undefined variables
   #  17-06-11  J.C.O   0.1.2   * Added image_batch_class.php class
   #              * Minor bug fixes
   #  26-07-11  J.C.O   0.1.4 * Added support for external images
   #              * Can now set the crop poisition
   #  03-08-11  J.C.O   0.1.5 * Added reset() method to reset resource to
   #                original input file.
   #              * Added method addTextToCaptionBox() to
   #                simplify adding text to a caption box.
   #              * Added experimental writeIPTC. (not finished)
   #              * Added experimental readIPTC. (not finished)
   #  11-08-11  J.C.O     * Added initial border presets.
   #  30-08-11  J.C.O     * Added 'auto' crop option to crop portrait
   #                images near the top.
   #  08-09-11  J.C.O     * Added cropImage() method to allow standalone
   #                cropping.
   #  17-09-11  J.C.O     * Added setCropFromTop() set method - set the
   #                percentage to crop from the top when using
   #                crop 'auto' option.
   #              * Added setTransparency() set method - allows you
   #                to turn transparency off (like when saving
   #                as a jpg).
   #              * Added setFillColor() set method - set the
   #                background color to use instead of transparency.
   #  05-11-11  J.C.O   0.1.5.1 * Fixed interlacing option
   #  0-07-12  J.C.O   1.0
   #
   #  Known issues & Limitations:
   # -------------------------------
   #  Not so much an issue, the image is destroyed on the deconstruct rather than
   #  when we have finished with it. The reason for this is that we don't know
   #  when we're finished with it as you can both save the image and display
   #  it directly to the screen (imagedestroy($this->imageResized))
   #
   #  Opening BMP files is slow. A test with 884 bmp files processed in a loop
   #  takes forever - over 5 min. This test inlcuded opening the file, then
   #  getting and displaying its width and height.
   #
   #  $forceStretch:
   # -------------------------------
   #  On by default.
   #  $forceStretch can be disabled by calling method setForceStretch with false
   #  parameter. If disabled, if an images original size is smaller than the size
   #  specified by the user, the original size will be used. This is useful when
   #  dealing with small images.
   #
   #  If enabled, images smaller than the size specified will be stretched to
   #  that size.
   #
   #  Tips:
   # -------------------------------
   #  * If you're resizing a transparent png and saving it as a jpg, set
   #  $keepTransparency to false with: $magicianObj->setTransparency(false);
   #
   #  FEATURES:
   #    * EASY TO USE
   #    * BMP SUPPORT (read & write)
   #    * PSD (photoshop) support (read)
   #    * RESIZE IMAGES
   #      - Preserve transparency (png, gif)
   #      - Apply sharpening (jpg) (requires PHP >= 5.1.0)
   #      - Set image quality (jpg, png)
   #      - Resize modes:
   #        - exact size
   #        - resize by width (auto height)
   #        - resize by height (auto width)
   #        - auto (automatically determine the best of the above modes to use)
   #        - crop - resize as best as it can then crop the rest
   #      - Force stretching of smaller images (upscale)
   #    * APPLY FILTERS
   #      - Convert to grey scale
   #      - Convert to black and white
   #      - Convert to sepia
   #      - Convert to negative
   #    * ROTATE IMAGES
   #      - Rotate using predefined "left", "right", or "180"; or any custom degree amount
   #    * EXTRACT EXIF DATA (requires exif module)
   #      - make
   #      - model
   #      - date
   #      - exposure
   #      - aperture
   #      - f-stop
   #      - iso
   #      - focal length
   #      - exposure program
   #      - metering mode
   #      - flash status
   #      - creator
   #      - copyright
   #    * ADD WATERMARK
   #      - Specify exact x, y placement
   #      - Or, specify using one of the 9 pre-defined placements such as "tl"
   #        (for top left), "m" (for middle), "br" (for bottom right)
   #        - also specify padding from edge amount (optional).
   #      - Set opacity of watermark (png).
   #    * ADD BORDER
   #    * USE HEX WHEN SPECIFYING COLORS (eg: #ffffff)
   #    * SAVE IMAGE OR OUTPUT TO SCREEN
   #
   #
   # ========================================================================#


class imageLib
{
    private $fileName;
    private $image;
    protected $imageResized;
    private $widthOriginal;     # Always be the original width
    private $heightOriginal;
    private $width;         # Current width (width after resize)
    private $height;
    private $imageSize;
    private $fileExtension;

    private $debug = true;
    private $errorArray = array();

    private $forceStretch = true;
    private $aggresiveSharpening = false;

    private $transparentArray = array('.png', '.gif');
    private $keepTransparency = true;
    private $fillColorArray = array('r'=>255, 'g'=>255, 'b'=>255);

    private $sharpenArray = array('jpg');

    private $psdReaderPath;
    private $filterOverlayPath;

    private $isInterlace;

    private $captionBoxPositionArray = array();

    private $fontDir = 'fonts';

    private $cropFromTopPercent = 10;


## --------------------------------------------------------

    function __construct($fileName)
    # Author:     Jarrod Oberto
  # Date:     27-02-08
    # Purpose:    Constructor
    # Param in:   $fileName: File name and path.
    # Param out:  n/a
    # Reference:
    # Notes:
    #
    {
        if (!$this->testGDInstalled()) {
            if ($this->debug) {
                throw new Exception('The GD Library is not installed.');
            } else {
                throw new Exception();
            }
        };

        $this->initialise();

        // *** Save the image file name. Only store this incase you want to display it
        $this->fileName = $fileName;
        $this->fileExtension = fix_strtolower(strrchr($fileName, '.'));

        // *** Open up the file
        $this->image = $this->openImage($fileName);


    // *** Assign here so we don't modify the original
    $this->imageResized = $this->image;

        // *** If file is an image
        if ($this->testIsImage($this->image)) {
            // *** Get width and height
            $this->width  = imagesx($this->image);
            $this->widthOriginal = imagesx($this->image);
            $this->height = imagesy($this->image);
            $this->heightOriginal = imagesy($this->image);


        /*  Added 15-09-08
         *  Get the filesize using this build in method.
         *  Stores an array of size
         *
         *  $this->imageSize[1] = width
         *  $this->imageSize[2] = height
         *  $this->imageSize[3] = width x height
         *
         */
            $this->imageSize = getimagesize($this->fileName);
        } else {
            $this->errorArray[] = 'File is not an image';
        }
    }

## --------------------------------------------------------

  private function initialise()
  {
      $this->psdReaderPath = dirname(__FILE__) . '/classPhpPsdReader.php';
      $this->filterOverlayPath = dirname(__FILE__) . '/filters';

    // *** Set if image should be interlaced or not.
    $this->isInterlace = false;
  }



/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Resize
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/


    public function resizeImage($newWidth, $newHeight, $option = 0, $sharpen = false, $autoRotate = false)
    # Author:     Jarrod Oberto
    # Date:       27-02-08
    # Purpose:    Resizes the image
    # Param in:   $newWidth:
    #             $newHeight:
    #             $option:     0 / exact = defined size;
    #                          1 / portrait = keep aspect set height;
    #                          2 / landscape = keep aspect set width;
    #                          3 / auto = auto;
  #                          4 / crop= resize and crop;
  #
  #         $option can also be an array containing options for
  #         cropping. E.G., array('crop', 'r')
  #
  #         This array only applies to 'crop' and the 'r' refers to
  #         "crop right". Other value include; tl, t, tr, l, m (default),
  #         r, bl, b, br, or you can specify your own co-ords (which
  #         isn't recommended.
  #
  #       $sharpen:    true: sharpen (jpg only);
  #                false: don't sharpen
    # Param out:  n/a
    # Reference:
    # Notes:      To clarify the $option input:
    #               0 = The exact height and width dimensions you set.
    #               1 = Whatever height is passed in will be the height that
    #                   is set. The width will be calculated and set automatically
    #                   to a the value that keeps the original aspect ratio.
    #               2 = The same but based on the width. We try make the image the
  #                  biggest size we can while stil fitting inside the box size
    #               3 = Depending whether the image is landscape or portrait, this
    #                   will automatically determine whether to resize via
    #                   dimension 1,2 or 0
  #               4 = Will resize and then crop the image for best fit
  #
  #       forceStretch can be applied to options 1,2,3 and 4
    #
    {

    // *** We can pass in an array of options to change the crop position
    $cropPos = 'm';
        if (is_array($option) && fix_strtolower($option[0]) == 'crop') {
            $cropPos = $option[1];         # get the crop option
        } elseif (strpos($option, '-') !== false) {
            // *** Or pass in a hyphen seperated option
      $optionPiecesArray = explode('-', $option);
            $cropPos = end($optionPiecesArray);
        }

    // *** Check the option is valid
    $option = $this->prepOption($option);

    // *** Make sure the file passed in is valid
    if (!$this->image) {
        if ($this->debug) {
            throw new Exception('file ' . $this->getFileName() .' is missing or invalid');
        } else {
            throw new Exception();
        }
    };

    // *** Get optimal width and height - based on $option
    $dimensionsArray = $this->getDimensions($newWidth, $newHeight, $option);

        $optimalWidth  = $dimensionsArray['optimalWidth'];
        $optimalHeight = $dimensionsArray['optimalHeight'];

    // *** Resample - create image canvas of x, y size
    $this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
        $this->keepTransparancy($optimalWidth, $optimalHeight, $this->imageResized);
        imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);


    // *** If '4', then crop too
    if ($option == 4 || $option == 'crop') {
        if (($optimalWidth >= $newWidth && $optimalHeight >= $newHeight)) {
            $this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight, $cropPos);
        }
    }

    // *** If Rotate.
    if ($autoRotate) {
        $exifData = $this->getExif(false);
        if (count($exifData) > 0) {
            switch ($exifData['orientation']) {
            case 8:
                $this->imageResized = imagerotate($this->imageResized, 90, 0);
                break;
            case 3:
                $this->imageResized = imagerotate($this->imageResized, 180, 0);
                break;
            case 6:
                $this->imageResized = imagerotate($this->imageResized, -90, 0);
                break;
        }
        }
    }

    // *** Sharpen image (if jpg and the user wishes to do so)
    if ($sharpen && in_array($this->fileExtension, $this->sharpenArray)) {

      // *** Sharpen
      $this->sharpen();
    }
    }

## --------------------------------------------------------

  public function cropImage($newWidth, $newHeight, $cropPos = 'm')
    # Author:     Jarrod Oberto
    # Date:       08-09-11
    # Purpose:    Crops the image
    # Param in:   $newWidth: crop with
    #             $newHeight: crop height
  #       $cropPos: Can be any of the following:
  #             tl, t, tr, l, m, r, bl, b, br, auto
  #           Or:
  #             a custom position such as '30x50'
    # Param out:  n/a
    # Reference:
    # Notes:
    #
  {

    // *** Make sure the file passed in is valid
    if (!$this->image) {
        if ($this->debug) {
            throw new Exception('file ' . $this->getFileName() .' is missing or invalid');
        } else {
            throw new Exception();
        }
    };

      $this->imageResized = $this->image;
      $this->crop($this->width, $this->height, $newWidth, $newHeight, $cropPos);
  }

## --------------------------------------------------------

  private function keepTransparancy($width, $height, $im)
    # Author:     Jarrod Oberto
    # Date:       08-04-11
    # Purpose:    Keep transparency for png and gif image
    # Param in:
    # Param out:  n/a
    # Reference:
    # Notes:
    #
  {
      // *** If PNG, perform some transparency retention actions (gif untested)
    if (in_array($this->fileExtension, $this->transparentArray) && $this->keepTransparency) {
        imagealphablending($im, false);
        imagesavealpha($im, true);
        $transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
        imagefilledrectangle($im, 0, 0, $width, $height, $transparent);
    } else {
        $color = imagecolorallocate($im, $this->fillColorArray['r'], $this->fillColorArray['g'], $this->fillColorArray['b']);
        imagefilledrectangle($im, 0, 0, $width, $height, $color);
    }
  }

## --------------------------------------------------------

    private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight, $cropPos)
    # Author:     Jarrod Oberto
    # Date:       15-09-08
    # Purpose:    Crops the image
    # Param in:   $newWidth:
    #             $newHeight:
    # Param out:  n/a
    # Reference:
    # Notes:
    #
    {

    // *** Get cropping co-ordinates
    $cropArray = $this->getCropPlacing($optimalWidth, $optimalHeight, $newWidth, $newHeight, $cropPos);
        $cropStartX = $cropArray['x'];
        $cropStartY = $cropArray['y'];

    // *** Crop this bad boy
    $crop = imagecreatetruecolor($newWidth, $newHeight);
        $this->keepTransparancy($optimalWidth, $optimalHeight, $crop);
        imagecopyresampled($crop, $this->imageResized, 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight, $newWidth, $newHeight);

        $this->imageResized = $crop;

    // *** Set new width and height to our variables
    $this->width = $newWidth;
        $this->height = $newHeight;
    }

## --------------------------------------------------------

  private function getCropPlacing($optimalWidth, $optimalHeight, $newWidth, $newHeight, $pos='m')
  #
  # Author:   Jarrod Oberto
  # Date:   July 11
  # Purpose:  Set the cropping area.
  # Params in:
  # Params out: (array) the crop x and y co-ordinates.
  # Notes:    When specifying the exact pixel crop position (eg 10x15), be
  #       very careful as it's easy to crop out of the image leaving
  #       black borders.
  #
  {
      $pos = fix_strtolower($pos);

    // *** If co-ords have been entered
    if (strstr($pos, 'x')) {
        $pos = str_replace(' ', '', $pos);

        $xyArray = explode('x', $pos);
        list($cropStartX, $cropStartY) = $xyArray;
    } else {
        switch ($pos) {
        case 'tl':
          $cropStartX = 0;
          $cropStartY = 0;
          break;

        case 't':
          $cropStartX = ($optimalWidth / 2) - ($newWidth /2);
          $cropStartY = 0;
          break;

        case 'tr':
          $cropStartX = $optimalWidth - $newWidth;
          $cropStartY = 0;
          break;

        case 'l':
          $cropStartX = 0;
          $cropStartY = ($optimalHeight/ 2) - ($newHeight/2);
          break;

        case 'm':
          $cropStartX = ($optimalWidth / 2) - ($newWidth /2);
          $cropStartY = ($optimalHeight/ 2) - ($newHeight/2);
          break;

        case 'r':
          $cropStartX = $optimalWidth - $newWidth;
          $cropStartY = ($optimalHeight/ 2) - ($newHeight/2);
          break;

        case 'bl':
          $cropStartX = 0;
          $cropStartY = $optimalHeight - $newHeight;
          break;

        case 'b':
          $cropStartX = ($optimalWidth / 2) - ($newWidth /2);
          $cropStartY = $optimalHeight - $newHeight;
          break;

        case 'br':
          $cropStartX = $optimalWidth - $newWidth;
          $cropStartY = $optimalHeight - $newHeight;
          break;

        case 'auto':
          // *** If image is a portrait crop from top, not center. v1.5
          if ($optimalHeight > $optimalWidth) {
              $cropStartX = ($optimalWidth / 2) - ($newWidth /2);
              $cropStartY = ($this->cropFromTopPercent /100) * $optimalHeight;
          } else {

            // *** Else crop from the center
            $cropStartX = ($optimalWidth / 2) - ($newWidth /2);
              $cropStartY = ($optimalHeight/ 2) - ($newHeight/2);
          }
          break;

        default:
          // *** Default to center
          $cropStartX = ($optimalWidth / 2) - ($newWidth /2);
          $cropStartY = ($optimalHeight/ 2) - ($newHeight/2);
          break;
      }
    }

      return array('x' => $cropStartX, 'y' => $cropStartY);
  }

## --------------------------------------------------------

  private function getDimensions($newWidth, $newHeight, $option)
    # Author:     Jarrod Oberto
    # Date:       17-11-09
    # Purpose:    Get new image dimensions based on user specificaions
    # Param in:   $newWidth:
    #             $newHeight:
    # Param out:  Array of new width and height values
    # Reference:
    # Notes:    If $option = 3 then this function is call recursivly
  #
  #       To clarify the $option input:
    #               0 = The exact height and width dimensions you set.
    #               1 = Whatever height is passed in will be the height that
    #                   is set. The width will be calculated and set automatically
    #                   to a the value that keeps the original aspect ratio.
    #               2 = The same but based on the width.
    #               3 = Depending whether the image is landscape or portrait, this
    #                   will automatically determine whether to resize via
    #                   dimension 1,2 or 0.
  #               4 = Resize the image as much as possible, then crop the
  #         remainder.
  {
      switch (strval($option)) {
            case '0':
      case 'exact':
                $optimalWidth = $newWidth;
                $optimalHeight= $newHeight;
                break;
            case '1':
      case 'portrait':
                $dimensionsArray = $this->getSizeByFixedHeight($newWidth, $newHeight);
        $optimalWidth = $dimensionsArray['optimalWidth'];
        $optimalHeight = $dimensionsArray['optimalHeight'];
                break;
            case '2':
      case 'landscape':
                $dimensionsArray = $this->getSizeByFixedWidth($newWidth, $newHeight);
        $optimalWidth = $dimensionsArray['optimalWidth'];
        $optimalHeight = $dimensionsArray['optimalHeight'];
                break;
            case '3':
      case 'auto':
                $dimensionsArray = $this->getSizeByAuto($newWidth, $newHeight);
        $optimalWidth = $dimensionsArray['optimalWidth'];
        $optimalHeight = $dimensionsArray['optimalHeight'];
                break;
      case '4':
      case 'crop':
                $dimensionsArray = $this->getOptimalCrop($newWidth, $newHeight);
        $optimalWidth = $dimensionsArray['optimalWidth'];
        $optimalHeight = $dimensionsArray['optimalHeight'];
                break;
        }

      return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
  }

## --------------------------------------------------------

    private function getSizeByFixedHeight($newWidth, $newHeight)
    {
        // *** If forcing is off...
    if (!$this->forceStretch) {

      // *** ...check if actual height is less than target height
      if ($this->height < $newHeight) {
          return array('optimalWidth' => $this->width, 'optimalHeight' => $this->height);
      }
    }

        $ratio = $this->width / $this->height;

        $newWidth = $newHeight * $ratio;

        //return $newWidth;
    return array('optimalWidth' => $newWidth, 'optimalHeight' => $newHeight);
    }

## --------------------------------------------------------

    private function getSizeByFixedWidth($newWidth, $newHeight)
    {
        // *** If forcing is off...
    if (!$this->forceStretch) {

      // *** ...check if actual width is less than target width
      if ($this->width < $newWidth) {
          return array('optimalWidth' => $this->width, 'optimalHeight' => $this->height);
      }
    }

        $ratio = $this->height / $this->width;

        $newHeight = $newWidth * $ratio;

        //return $newHeight;
    return array('optimalWidth' => $newWidth, 'optimalHeight' => $newHeight);
    }

## --------------------------------------------------------

    private function getSizeByAuto($newWidth, $newHeight)
    # Author:     Jarrod Oberto
    # Date:       19-08-08
    # Purpose:    Depending on the height, choose to resize by 0, 1, or 2
    # Param in:   The new height and new width
    # Notes:
    #
    {
        // *** If forcing is off...
    if (!$this->forceStretch) {

      // *** ...check if actual size is less than target size
      if ($this->width < $newWidth && $this->height < $newHeight) {
          return array('optimalWidth' => $this->width, 'optimalHeight' => $this->height);
      }
    }

        if ($this->height < $this->width) {
            // *** Image to be resized is wider (landscape)

            //$optimalWidth = $newWidth;
            //$optimalHeight= $this->getSizeByFixedWidth($newWidth);

            $dimensionsArray = $this->getSizeByFixedWidth($newWidth, $newHeight);
            $optimalWidth = $dimensionsArray['optimalWidth'];
            $optimalHeight = $dimensionsArray['optimalHeight'];
        } elseif ($this->height > $this->width) {
            // *** Image to be resized is taller (portrait)

            //$optimalWidth = $this->getSizeByFixedHeight($newHeight);
            //$optimalHeight= $newHeight;

            $dimensionsArray = $this->getSizeByFixedHeight($newWidth, $newHeight);
            $optimalWidth = $dimensionsArray['optimalWidth'];
            $optimalHeight = $dimensionsArray['optimalHeight'];
        } else {
            // *** Image to be resizerd is a square


      if ($newHeight < $newWidth) {
          //$optimalWidth = $newWidth;
        //$optimalHeight= $this->getSizeByFixedWidth($newWidth);
                $dimensionsArray = $this->getSizeByFixedWidth($newWidth, $newHeight);
          $optimalWidth = $dimensionsArray['optimalWidth'];
          $optimalHeight = $dimensionsArray['optimalHeight'];
      } elseif ($newHeight > $newWidth) {
          //$optimalWidth = $this->getSizeByFixedHeight($newHeight);
            //$optimalHeight= $newHeight;
                $dimensionsArray = $this->getSizeByFixedHeight($newWidth, $newHeight);
          $optimalWidth = $dimensionsArray['optimalWidth'];
          $optimalHeight = $dimensionsArray['optimalHeight'];
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
  # Author:     Jarrod Oberto
    # Date:       17-11-09
    # Purpose:    Get optimal crop dimensions
    # Param in:   width and height as requested by user (fig 3)
    # Param out:  Array of optimal width and height (fig 2)
    # Reference:
    # Notes:      The optimal width and height return are not the same as the
  #       same as the width and height passed in. For example:
  #
  #
  #   |-----------------|     |------------|       |-------|
  #   |             |   =>  |**|      |**|   =>  |       |
  #   |             |     |**|      |**|       |       |
    #   |           |       |------------|       |-------|
  #   |-----------------|
  #        original                optimal             crop
  #              size                   size               size
  #  Fig          1                      2                  3
  #
  #       300 x 250           150 x 125          150 x 100
  #
  #    The optimal size is the smallest size (that is closest to the crop size)
  #    while retaining proportion/ratio.
  #
  #  The crop size is the optimal size that has been cropped on one axis to
  #  make the image the exact size specified by the user.
  #
  #               * represent cropped area
  #
    {

    // *** If forcing is off...
    if (!$this->forceStretch) {

      // *** ...check if actual size is less than target size
      if ($this->width < $newWidth && $this->height < $newHeight) {
          return array('optimalWidth' => $this->width, 'optimalHeight' => $this->height);
      }
    }

        $heightRatio = $this->height / $newHeight;
        $widthRatio  = $this->width /  $newWidth;

        if ($heightRatio < $widthRatio) {
            $optimalRatio = $heightRatio;
        } else {
            $optimalRatio = $widthRatio;
        }

        $optimalHeight = round($this->height / $optimalRatio);
        $optimalWidth  = round($this->width  / $optimalRatio);

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }

## --------------------------------------------------------

  private function sharpen()
    # Author:     Jarrod Oberto
    # Date:       08 04 2011
    # Purpose:    Sharpen image
    # Param in:   n/a
    # Param out:  n/a
    # Reference:
    # Notes:
    # Credit:   Incorporates Joe Lencioni (August 6, 2008) code
  {
      if (version_compare(PHP_VERSION, '5.1.0') >= 0) {

      // ***
      if ($this->aggresiveSharpening) { # A more aggressive sharpening solution

        $sharpenMatrix = array( array( -1, -1, -1 ),
                         array( -1, 16, -1 ),
                         array( -1, -1, -1 ) );
          $divisor = 8;
          $offset = 0;

          imageconvolution($this->imageResized, $sharpenMatrix, $divisor, $offset);
      } else {
          # More subtle and personally more desirable

        $sharpness  = $this->findSharp($this->widthOriginal, $this->width);

          $sharpenMatrix  = array(
          array(-1, -2, -1),
          array(-2, $sharpness + 12, -2), //Lessen the effect of a filter by increasing the value in the center cell
          array(-1, -2, -1)
        );
          $divisor    = $sharpness; // adjusts brightness
        $offset     = 0;
          imageconvolution($this->imageResized, $sharpenMatrix, $divisor, $offset);
      }
      } else {
          if ($this->debug) {
              throw new Exception('Sharpening required PHP 5.1.0 or greater.');
          }
      }
  }

  ## --------------------------------------------------------

  private function sharpen2($level)
  {
      $sharpenMatrix  = array(
        array($level, $level, $level),
        array($level, (8*$level)+1, $level), //Lessen the effect of a filter by increasing the value in the center cell
        array($level, $level, $level)
      );
  }

## --------------------------------------------------------

  private function findSharp($orig, $final)
    # Author:     Ryan Rud (http://adryrun.com)
    # Purpose:    Find optimal sharpness
    # Param in:   n/a
    # Param out:  n/a
    # Reference:
    # Notes:
    #
  {
      $final  = $final * (750.0 / $orig);
      $a    = 52;
      $b    = -0.27810650887573124;
      $c    = .00047337278106508946;

      $result = $a + $b * $final + $c * $final * $final;

      return max(round($result), 0);
  }

## --------------------------------------------------------

  private function prepOption($option)
    # Author:     Jarrod Oberto
    # Purpose:    Prep option like change the passed in option to lowercase
    # Param in:   (str/int) $option: eg. 'exact', 'crop'. 0, 4
    # Param out:  lowercase string
    # Reference:
    # Notes:
    #
  {
      if (is_array($option)) {
          if (fix_strtolower($option[0]) == 'crop' && count($option) == 2) {
              return 'crop';
          } else {
              throw new Exception('Crop resize option array is badly formatted.');
          }
      } elseif (strpos($option, 'crop') !== false) {
          return 'crop';
      }

      if (is_string($option)) {
          return fix_strtolower($option);
      }

      return $option;
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Presets
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

#
# Preset are pre-defined templates you can apply to your image.
#
# These are inteded to be applied to thumbnail images.
#


  public function borderPreset($preset)
  {
      switch ($preset) {

      case 'simple':
        $this->addBorder(7, '#fff');
        $this->addBorder(6, '#f2f1f0');
        $this->addBorder(2, '#fff');
        $this->addBorder(1, '#ccc');
        break;
      default:
        break;
    }
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Draw border
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function addBorder($thickness = 1, $rgbArray = array(255, 255, 255))
    # Author:     Jarrod Oberto
    # Date:       05-05-11
    # Purpose:    Add a border to the image
    # Param in:
    # Param out:
    # Reference:
    # Notes:    This border is added to the INSIDE of the image
    #
  {
      if ($this->imageResized) {
          $rgbArray = $this->formatColor($rgbArray);
          $r = $rgbArray['r'];
          $g = $rgbArray['g'];
          $b = $rgbArray['b'];


          $x1 = 0;
          $y1 = 0;
          $x2 = ImageSX($this->imageResized) - 1;
          $y2 = ImageSY($this->imageResized) - 1;

          $rgbArray = ImageColorAllocate($this->imageResized, $r, $g, $b);


          for ($i = 0; $i < $thickness; $i++) {
              ImageRectangle($this->imageResized, $x1++, $y1++, $x2--, $y2--, $rgbArray);
          }
      }
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Gray Scale
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function greyScale()
    # Author:     Jarrod Oberto
    # Date:       07-05-2011
    # Purpose:    Make image greyscale
    # Param in:   n/a
    # Param out:
    # Reference:
    # Notes:
    #
  {
      if ($this->imageResized) {
          imagefilter($this->imageResized, IMG_FILTER_GRAYSCALE);
      }
  }

  ## --------------------------------------------------------

  public function greyScaleEnhanced()
    # Author:     Jarrod Oberto
    # Date:       07-05-2011
    # Purpose:    Make image greyscale
    # Param in:   n/a
    # Param out:
    # Reference:
    # Notes:
    #
  {
      if ($this->imageResized) {
          imagefilter($this->imageResized, IMG_FILTER_GRAYSCALE);
          imagefilter($this->imageResized, IMG_FILTER_CONTRAST, -15);
          imagefilter($this->imageResized, IMG_FILTER_BRIGHTNESS, 2);
          $this->sharpen($this->width);
      }
  }

  ## --------------------------------------------------------

  public function greyScaleDramatic()
  # Alias of gd_filter_monopin
  {
      $this->gd_filter_monopin();
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Black 'n White
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function blackAndWhite()
    # Author:     Jarrod Oberto
    # Date:       07-05-2011
    # Purpose:    Make image black and white
    # Param in:   n/a
    # Param out:
    # Reference:
    # Notes:
    #
  {
      if ($this->imageResized) {
          imagefilter($this->imageResized, IMG_FILTER_GRAYSCALE);
          imagefilter($this->imageResized, IMG_FILTER_CONTRAST, -1000);
      }
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Negative
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function negative()
    # Author:     Jarrod Oberto
    # Date:       07-05-2011
    # Purpose:    Make image negative
    # Param in:   n/a
    # Param out:
    # Reference:
    # Notes:
    #
  {
      if ($this->imageResized) {
          imagefilter($this->imageResized, IMG_FILTER_NEGATE);
      }
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Sepia
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function sepia()
    # Author:     Jarrod Oberto
    # Date:       07-05-2011
    # Purpose:    Make image sepia
    # Param in:   n/a
    # Param out:
    # Reference:
    # Notes:
    #
  {
      if ($this->imageResized) {
          imagefilter($this->imageResized, IMG_FILTER_GRAYSCALE);
          imagefilter($this->imageResized, IMG_FILTER_BRIGHTNESS, -10);
          imagefilter($this->imageResized, IMG_FILTER_CONTRAST, -20);
          imagefilter($this->imageResized, IMG_FILTER_COLORIZE, 60, 30, -15);
      }
  }

  ## --------------------------------------------------------

  public function sepia2()
  {
      if ($this->imageResized) {
          $total = imagecolorstotal($this->imageResized);
          for ($i = 0; $i < $total; $i++) {
              $index = imagecolorsforindex($this->imageResized, $i);
              $red = ($index["red"] * 0.393 + $index["green"] * 0.769 + $index["blue"] * 0.189) / 1.351;
              $green = ($index["red"] * 0.349 + $index["green"] * 0.686 + $index["blue"] * 0.168) / 1.203;
              $blue = ($index["red"] * 0.272 + $index["green"] * 0.534 + $index["blue"] * 0.131) / 2.140;
              imagecolorset($this->imageResized, $i, $red, $green, $blue);
          }
      }
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Vintage
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function vintage()
  # Alias of gd_filter_monopin
  {
      $this->gd_filter_vintage();
  }

/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Presets By Marc Hibbins
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/


  /** Apply 'Monopin' preset */
  public function gd_filter_monopin()
  {
      if ($this->imageResized) {
          imagefilter($this->imageResized, IMG_FILTER_GRAYSCALE);
          imagefilter($this->imageResized, IMG_FILTER_BRIGHTNESS, -15);
          imagefilter($this->imageResized, IMG_FILTER_CONTRAST, -15);
          $this->imageResized = $this->gd_apply_overlay($this->imageResized, 'vignette', 100);
      }
  }

  ## --------------------------------------------------------

  public function gd_filter_vintage()
  {
      if ($this->imageResized) {
          $this->imageResized = $this->gd_apply_overlay($this->imageResized, 'vignette', 45);
          imagefilter($this->imageResized, IMG_FILTER_BRIGHTNESS, 20);
          imagefilter($this->imageResized, IMG_FILTER_CONTRAST, -35);
          imagefilter($this->imageResized, IMG_FILTER_COLORIZE, 60, -10, 35);
          imagefilter($this->imageResized, IMG_FILTER_SMOOTH, 7);
          $this->imageResized = $this->gd_apply_overlay($this->imageResized, 'scratch', 10);
      }
  }

  ## --------------------------------------------------------

  /** Apply a PNG overlay */
  private function gd_apply_overlay($im, $type, $amount)
  #
  # Original Author:    Marc Hibbins
  # License:  Attribution-ShareAlike 3.0
  # Purpose:
  # Params in:
  # Params out:
  # Notes:
  #
  {
      $width = imagesx($im);
      $height = imagesy($im);
      $filter = imagecreatetruecolor($width, $height);

      imagealphablending($filter, false);
      imagesavealpha($filter, true);

      $transparent = imagecolorallocatealpha($filter, 255, 255, 255, 127);
      imagefilledrectangle($filter, 0, 0, $width, $height, $transparent);

    // *** Resize overlay
    $overlay = $this->filterOverlayPath . '/' . $type . '.png';
      $png = imagecreatefrompng($overlay);
      imagecopyresampled($filter, $png, 0, 0, 0, 0, $width, $height, imagesx($png), imagesy($png));

      $comp = imagecreatetruecolor($width, $height);
      imagecopy($comp, $im, 0, 0, 0, 0, $width, $height);
      imagecopy($comp, $filter, 0, 0, 0, 0, $width, $height);
      imagecopymerge($im, $comp, 0, 0, 0, 0, $width, $height, $amount);

      imagedestroy($comp);
      return $im;
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Colorise
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function image_colorize($rgb)
  {
      imageTrueColorToPalette($this->imageResized, true, 256);
      $numColors = imageColorsTotal($this->imageResized);

      for ($x = 0; $x < $numColors; $x++) {
          list($r, $g, $b) = array_values(imageColorsForIndex($this->imageResized, $x));

    // calculate grayscale in percent
    $grayscale = ($r + $g + $b) / 3 / 0xff;

          imageColorSet($this->imageResized, $x,
      $grayscale * $rgb[0],
      $grayscale * $rgb[1],
      $grayscale * $rgb[2]
    );
      }

      return true;
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Reflection
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function addReflection($reflectionHeight = 50, $startingTransparency = 30, $inside = false, $bgColor = '#fff', $stretch=false, $divider = 0)
  {

    // *** Convert color
    $rgbArray = $this->formatColor($bgColor);
      $r = $rgbArray['r'];
      $g = $rgbArray['g'];
      $b = $rgbArray['b'];

      $im = $this->imageResized;
      $li = imagecreatetruecolor($this->width, 1);

      $bgc = imagecolorallocate($li, $r, $g, $b);
      imagefilledrectangle($li, 0, 0, $this->width, 1, $bgc);

      $bg = imagecreatetruecolor($this->width, $reflectionHeight);
      $wh = imagecolorallocate($im, 255, 255, 255);

      $im = imagerotate($im, -180, $wh);
      imagecopyresampled($bg, $im, 0, 0, 0, 0, $this->width, $this->height, $this->width, $this->height);

      $im = $bg;

      $bg = imagecreatetruecolor($this->width, $reflectionHeight);

      for ($x = 0; $x < $this->width; $x++) {
          imagecopy($bg, $im, $x, 0, $this->width-$x -1, 0, 1, $reflectionHeight);
      }
      $im = $bg;

      $transparencyAmount = $this->invertTransparency($startingTransparency, 100);


    // *** Fade
    if ($stretch) {
        $step = 100/($reflectionHeight + $startingTransparency);
    } else {
        $step = 100/$reflectionHeight;
    }
      for ($i=0; $i<=$reflectionHeight; $i++) {
          if ($startingTransparency>100) {
              $startingTransparency = 100;
          }
          if ($startingTransparency< 1) {
              $startingTransparency = 1;
          }
          imagecopymerge($bg, $li, 0, $i, 0, 0, $this->width, 1, $startingTransparency);
          $startingTransparency+=$step;
      }

    // *** Apply fade
    imagecopymerge($im, $li, 0, 0, 0, 0, $this->width, $divider, 100); // Divider


    // *** width, height of reflection.
    $x = imagesx($im);
      $y = imagesy($im);


    // *** Determines if the reflection should be displayed inside or outside the image
    if ($inside) {

      // Create new blank image with sizes.
      $final = imagecreatetruecolor($this->width, $this->height);

        imagecopymerge($final, $this->imageResized, 0, 0, 0, $reflectionHeight, $this->width, $this->height - $reflectionHeight, 100);
        imagecopymerge($final, $im, 0, $this->height - $reflectionHeight, 0, 0, $x, $y, 100);
    } else {

      // Create new blank image with sizes.
      $final = imagecreatetruecolor($this->width, $this->height + $y);

        imagecopymerge($final, $this->imageResized, 0, 0, 0, 0, $this->width, $this->height, 100);
        imagecopymerge($final, $im, 0, $this->height, 0, 0, $x, $y, 100);
    }

      $this->imageResized = $final;

      imagedestroy($li);
      imagedestroy($im);
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Rotate
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function rotate($value = 90, $bgColor = 'transparent')
    # Author:     Jarrod Oberto
    # Date:       07-05-2011
    # Purpose:    Rotate image
    # Param in:   (mixed) $degrees: (int) number of degress to rotate image
  #               (str) param "left": rotate left
  #               (str) param "right": rotate right
  #               (str) param "upside": upside-down image
    # Param out:
    # Reference:
    # Notes:    The default direction of imageRotate() is counter clockwise.
    #
  {
      if ($this->imageResized) {
          if (is_integer($value)) {
              $degrees = $value;
          }

      // *** Convert color
      $rgbArray = $this->formatColor($bgColor);
          $r = $rgbArray['r'];
          $g = $rgbArray['g'];
          $b = $rgbArray['b'];
          if (isset($rgbArray['a'])) {
              $a = $rgbArray['a'];
          }

          if (is_string($value)) {
              $value = fix_strtolower($value);

              switch ($value) {
          case 'left':
            $degrees = 90;
            break;
          case 'right':
            $degrees = 270;
            break;
          case 'upside':
            $degrees = 180;
            break;
          default:
            break;
        }
          }

      // *** The default direction of imageRotate() is counter clockwise
      //   * This makes it clockwise
      $degrees = 360 - $degrees;

      // *** Create background color
      $bg = ImageColorAllocateAlpha($this->imageResized, $r, $g, $b, $a);

      // *** Fill with background
      ImageFill($this->imageResized, 0, 0, $bg);

      // *** Rotate
      $this->imageResized = imagerotate($this->imageResized, $degrees, $bg); // Rotate 45 degrees and allocated the transparent colour as the one to make transparent (obviously)

      // Ensure alpha transparency
      ImageSaveAlpha($this->imageResized, true);
      }
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Round corners
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function roundCorners($radius = 5,  $bgColor = 'transparent')
    # Author:     Jarrod Oberto
    # Date:       19-05-2011
    # Purpose:    Create rounded corners on your image
    # Param in:   (int) radius = the amount of curvature
  #       (mixed) $bgColor = the corner background color
    # Param out:  n/a
    # Reference:
    # Notes:
    #
  {

    // *** Check if the user wants transparency
    $isTransparent = false;
      if (!is_array($bgColor)) {
          if (fix_strtolower($bgColor) == 'transparent') {
              $isTransparent = true;
          }
      }


    // *** If we use transparency, we need to color our curved mask with a unique color
    if ($isTransparent) {
        $bgColor = $this->findUnusedGreen();
    }

    // *** Convert color
    $rgbArray = $this->formatColor($bgColor);
      $r = $rgbArray['r'];
      $g = $rgbArray['g'];
      $b = $rgbArray['b'];
      if (isset($rgbArray['a'])) {
          $a = $rgbArray['a'];
      }



    // *** Create top-left corner mask (square)
    $cornerImg = imagecreatetruecolor($radius, $radius);
    //$cornerImg = imagecreate($radius, $radius);

      //imagealphablending($cornerImg, true);
      //imagesavealpha($cornerImg, true);

      //imagealphablending($this->imageResized, false);
      //imagesavealpha($this->imageResized, true);

    // *** Give it a color
    $maskColor = imagecolorallocate($cornerImg, 0, 0, 0);



    // *** Replace the mask color (black) to transparent
    imagecolortransparent($cornerImg, $maskColor);



    // *** Create the image background color
    $imagebgColor = imagecolorallocate($cornerImg, $r, $g, $b);



    // *** Fill the corner area to the user defined color
    imagefill($cornerImg, 0, 0, $imagebgColor);


      imagefilledellipse($cornerImg, $radius, $radius, $radius * 2, $radius * 2, $maskColor);


    // *** Map to top left corner
    imagecopymerge($this->imageResized, $cornerImg, 0, 0, 0, 0, $radius, $radius, 100); #tl

    // *** Map rounded corner to other corners by rotating and applying the mask
    $cornerImg = imagerotate($cornerImg, 90, 0);
      imagecopymerge($this->imageResized, $cornerImg, 0, $this->height - $radius, 0, 0, $radius, $radius, 100); #bl

    $cornerImg = imagerotate($cornerImg, 90, 0);
      imagecopymerge($this->imageResized, $cornerImg, $this->width - $radius, $this->height - $radius, 0, 0, $radius, $radius, 100); #br

    $cornerImg = imagerotate($cornerImg, 90, 0);
      imagecopymerge($this->imageResized, $cornerImg, $this->width - $radius, 0, 0, 0, $radius, $radius, 100); #tr


    // *** If corners are to be transparent, we fill our chromakey color as transparent.
    if ($isTransparent) {
        //imagecolortransparent($this->imageResized, $imagebgColor);
      $this->imageResized = $this->transparentImage($this->imageResized);
        imagesavealpha($this->imageResized, true);
    }
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Shadow
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function addShadow($shadowAngle=45, $blur=15, $bgColor='transparent')
  #
  # Author:   Jarrod Oberto (Adapted from Pascal Naidon)
  # Ref:    http://www.les-stooges.org/pascal/webdesign/vignettes/index.php?la=en
  # Purpose:  Add a drop shadow to your image
  # Params in:  (int) $angle: the angle of the shadow
  #       (int) $blur: the blur distance
  #       (mixed) $bgColor: the color of the background
  # Params out:
  # Notes:
  #
  {
      // *** A higher number results in a smoother shadow
    define('STEPS', $blur*2);

    // *** Set the shadow distance
    $shadowDistance = $blur*0.25;

    // *** Set blur width and height
    $blurWidth = $blurHeight = $blur;


      if ($shadowAngle == 0) {
          $distWidth = 0;
          $distHeight = 0;
      } else {
          $distWidth = $shadowDistance * cos(deg2rad($shadowAngle));
          $distHeight = $shadowDistance * sin(deg2rad($shadowAngle));
      }


    // *** Convert color
    if (fix_strtolower($bgColor) != 'transparent') {
        $rgbArray = $this->formatColor($bgColor);
        $r0 = $rgbArray['r'];
        $g0 = $rgbArray['g'];
        $b0 = $rgbArray['b'];
    }


      $image = $this->imageResized;
      $width = $this->width;
      $height = $this->height;


      $newImage = imagecreatetruecolor($width, $height);
      imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $width, $height);


    // *** RGB
    $rgb = imagecreatetruecolor($width+$blurWidth, $height+$blurHeight);
      $colour = imagecolorallocate($rgb, 0, 0, 0);
      imagefilledrectangle($rgb, 0, 0, $width+$blurWidth, $height+$blurHeight, $colour);
      $colour = imagecolorallocate($rgb, 255, 255, 255);
    //imagefilledrectangle($rgb, $blurWidth*0.5-$distWidth, $blurHeight*0.5-$distHeight, $width+$blurWidth*0.5-$distWidth, $height+$blurWidth*0.5-$distHeight, $colour);
    imagefilledrectangle($rgb, $blurWidth*0.5-$distWidth, $blurHeight*0.5-$distHeight, $width+$blurWidth*0.5-$distWidth, $height+$blurWidth*0.5-$distHeight, $colour);
    //imagecopymerge($rgb, $newImage, 1+$blurWidth*0.5-$distWidth, 1+$blurHeight*0.5-$distHeight, 0,0, $width, $height, 100);
    imagecopymerge($rgb, $newImage, $blurWidth*0.5-$distWidth, $blurHeight*0.5-$distHeight, 0, 0, $width+$blurWidth, $height+$blurHeight, 100);


    // *** Shadow (alpha)
    $shadow = imagecreatetruecolor($width+$blurWidth, $height+$blurHeight);
      imagealphablending($shadow, false);
      $colour = imagecolorallocate($shadow, 0, 0, 0);
      imagefilledrectangle($shadow, 0, 0, $width+$blurWidth, $height+$blurHeight, $colour);


      for ($i=0;$i<=STEPS;$i++) {
          $t = ((1.0*$i)/STEPS);
          $intensity = 255*$t*$t;

          $colour = imagecolorallocate($shadow, $intensity, $intensity, $intensity);
          $points = array(
        $blurWidth*$t,        $blurHeight,     // Point 1 (x, y)
        $blurWidth,         $blurHeight*$t,  // Point 2 (x, y)
        $width,           $blurHeight*$t,  // Point 3 (x, y)
        $width+$blurWidth*(1-$t), $blurHeight,     // Point 4 (x, y)
        $width+$blurWidth*(1-$t), $height,     // Point 5 (x, y)
        $width,           $height+$blurHeight*(1-$t),  // Point 6 (x, y)
        $blurWidth,         $height+$blurHeight*(1-$t),  // Point 7 (x, y)
        $blurWidth*$t,        $height      // Point 8 (x, y)
      );
          imagepolygon($shadow, $points, 8, $colour);
      }

      for ($i=0;$i<=STEPS;$i++) {
          $t = ((1.0*$i)/STEPS);
          $intensity = 255*$t*$t;

          $colour = imagecolorallocate($shadow, $intensity, $intensity, $intensity);
          imagefilledarc($shadow, $blurWidth-1, $blurHeight-1, 2*(1-$t)*$blurWidth, 2*(1-$t)*$blurHeight, 180, 268, $colour, IMG_ARC_PIE);
          imagefilledarc($shadow, $width, $blurHeight-1, 2*(1-$t)*$blurWidth, 2*(1-$t)*$blurHeight, 270, 358, $colour, IMG_ARC_PIE);
          imagefilledarc($shadow, $width, $height, 2*(1-$t)*$blurWidth, 2*(1-$t)*$blurHeight, 0, 90, $colour, IMG_ARC_PIE);
          imagefilledarc($shadow, $blurWidth-1, $height, 2*(1-$t)*$blurWidth, 2*(1-$t)*$blurHeight, 90, 180, $colour, IMG_ARC_PIE);
      }


      $colour = imagecolorallocate($shadow, 255, 255, 255);
      imagefilledrectangle($shadow, $blurWidth, $blurHeight, $width, $height, $colour);
      imagefilledrectangle($shadow, $blurWidth*0.5-$distWidth, $blurHeight*0.5-$distHeight, $width+$blurWidth*0.5-1-$distWidth, $height+$blurHeight*0.5-1-$distHeight, $colour);


    // *** The magic
        imagealphablending($rgb, false);

      for ($theX=0;$theX<imagesx($rgb);$theX++) {
          for ($theY=0;$theY<imagesy($rgb);$theY++) {

        // *** Get the RGB values for every pixel of the RGB image
        $colArray = imagecolorat($rgb, $theX, $theY);
              $r = ($colArray >> 16) & 0xFF;
              $g = ($colArray >> 8) & 0xFF;
              $b = $colArray & 0xFF;

        // *** Get the alpha value for every pixel of the shadow image
        $colArray = imagecolorat($shadow, $theX, $theY);
              $a = $colArray & 0xFF;
              $a = 127-floor($a/2);
              $t = $a/128.0;

        // *** Create color
        if (fix_strtolower($bgColor) == 'transparent') {
            $myColour = imagecolorallocatealpha($rgb, $r, $g, $b, $a);
        } else {
            $myColour = imagecolorallocate($rgb, $r*(1.0-$t)+$r0*$t, $g*(1.0-$t)+$g0*$t, $b*(1.0-$t)+$b0*$t);
        }

        // *** Add color to new rgb image
        imagesetpixel($rgb, $theX, $theY, $myColour);
          }
      }

      imagealphablending($rgb, true);
      imagesavealpha($rgb, true);

      $this->imageResized = $rgb;

      imagedestroy($image);
      imagedestroy($newImage);
      imagedestroy($shadow);
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Add Caption Box
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function addCaptionBox($side='b', $thickness=50, $padding=0, $bgColor='#000', $transparencyAmount=30)
  #
  # Author:   Jarrod Oberto
  # Date:   26 May 2011
  # Purpose:  Add a caption box
  # Params in:  (str) $side: the side to add the caption box (t, r, b, or l).
  #       (int) $thickness: how thick you want the caption box to be.
  #       (mixed) $bgColor: The color of the caption box.
  #       (int) $transparencyAmount: The amount of transparency to be
  #       applied.
  # Params out: n/a
  # Notes:
  #
  {
      $side = fix_strtolower($side);

      // *** Convert color
      $rgbArray = $this->formatColor($bgColor);
      $r = $rgbArray['r'];
      $g = $rgbArray['g'];
      $b = $rgbArray['b'];

      $positionArray = $this->calculateCaptionBoxPosition($side, $thickness, $padding);

      // *** Store incase we want to use method addTextToCaptionBox()
      $this->captionBoxPositionArray = $positionArray;


      $transparencyAmount = $this->invertTransparency($transparencyAmount, 127, false);
      $transparent = imagecolorallocatealpha($this->imageResized, $r, $g, $b, $transparencyAmount);
      imagefilledrectangle($this->imageResized, $positionArray['x1'], $positionArray['y1'], $positionArray['x2'], $positionArray['y2'], $transparent);
  }

  ## --------------------------------------------------------

  public function addTextToCaptionBox($text, $fontColor='#fff', $fontSize = 12, $angle = 0, $font = null)
  #
  # Author:   Jarrod Oberto
  # Date:   03 Aug 11
  # Purpose:  Simplify adding text to a caption box by automatically
  #       locating the center of the caption box
  # Params in:  The usually text paams (less a couple)
  # Params out: n/a
  # Notes:
  #
  {

    // *** Get the caption box measurements
    if (count($this->captionBoxPositionArray) == 4) {
        $x1 = $this->captionBoxPositionArray['x1'];
        $x2 = $this->captionBoxPositionArray['x2'];
        $y1 = $this->captionBoxPositionArray['y1'];
        $y2 = $this->captionBoxPositionArray['y2'];
    } else {
        if ($this->debug) {
            throw new Exception('No caption box found.');
        } else {
            return false;
        }
    }


    // *** Get text font
    $font = $this->getTextFont($font);

    // *** Get text size
    $textSizeArray = $this->getTextSize($fontSize, $angle, $font, $text);
      $textWidth = $textSizeArray['width'];
      $textHeight = $textSizeArray['height'];

    // *** Find the width/height middle points
    $boxXMiddle = (($x2 - $x1) / 2);
      $boxYMiddle = (($y2 - $y1) / 2);

    // *** Box middle - half the text width/height
    $xPos = ($x1 + $boxXMiddle) - ($textWidth/2);
      $yPos = ($y1 + $boxYMiddle) - ($textHeight/2);

      $pos = $xPos . 'x' . $yPos;

      $this->addText($text, $pos, $padding = 0, $fontColor, $fontSize, $angle, $font);
  }

  ## --------------------------------------------------------

  private function calculateCaptionBoxPosition($side, $thickness, $padding)
  {
      $positionArray = array();

      switch ($side) {
      case 't':
        $positionArray['x1'] = 0;
        $positionArray['y1'] = $padding;
        $positionArray['x2'] = $this->width;
        $positionArray['y2'] = $thickness + $padding;
        break;
      case 'r':
        $positionArray['x1'] = $this->width - $thickness - $padding;
        $positionArray['y1'] = 0;
        $positionArray['x2'] = $this->width - $padding;
        $positionArray['y2'] = $this->height;
        break;
      case 'b':
        $positionArray['x1'] = 0;
        $positionArray['y1'] = $this->height - $thickness - $padding;
        $positionArray['x2'] = $this->width;
        $positionArray['y2'] = $this->height - $padding;
        break;
      case 'l':
        $positionArray['x1'] = $padding;
        $positionArray['y1'] = 0;
        $positionArray['x2'] = $thickness + $padding;
        $positionArray['y2'] = $this->height;
        break;

      default:
        break;
    }

      return $positionArray;
  }

/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Get EXIF Data
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function getExif($debug=false)
    # Author:     Jarrod Oberto
    # Date:       07-05-2011
    # Purpose:    Get image EXIF data
    # Param in:   n/a
    # Param out:  An associate array of EXIF data
    # Reference:
    # Notes:
    # 23 May 13 : added orientation flag -jco
    #
  {
      if (!$this->debug || !$debug) {
          $debug = false;
      }

    // *** Check all is good - check the EXIF library exists and the file exists, too.
    if (!$this->testEXIFInstalled()) {
        if ($debug) {
            throw new Exception('The EXIF Library is not installed.');
        } else {
            return array();
        }
    };
      if (!file_exists($this->fileName)) {
          if ($debug) {
              throw new Exception('Image not found.');
          } else {
              return array();
          }
      };
      if ($this->fileExtension != '.jpg') {
          if ($debug) {
              throw new Exception('Metadata not supported for this image type.');
          } else {
              return array();
          }
      };
      $exifData = exif_read_data($this->fileName, 'IFD0');

    // *** Format the apperture value
    $ev = $exifData['ApertureValue'];
      $apPeicesArray = explode('/', $ev);
      if (count($apPeicesArray) == 2) {
          $apertureValue = round($apPeicesArray[0] / $apPeicesArray[1], 2, PHP_ROUND_HALF_DOWN) . ' EV';
      } else {
          $apertureValue = '';
      }

    // *** Format the focal length
    $focalLength = $exifData['FocalLength'];
      $flPeicesArray = explode('/', $focalLength);
      if (count($flPeicesArray) == 2) {
          $focalLength = $flPeicesArray[0] / $flPeicesArray[1] . '.0 mm';
      } else {
          $focalLength = '';
      }

    // *** Format fNumber
    $fNumber = $exifData['FNumber'];
      $fnPeicesArray = explode('/', $fNumber);
      if (count($fnPeicesArray) == 2) {
          $fNumber = $fnPeicesArray[0] / $fnPeicesArray[1];
      } else {
          $fNumber = '';
      }

    // *** Resolve ExposureProgram
    if (isset($exifData['ExposureProgram'])) {
        $ep =  $exifData['ExposureProgram'];
    }
      if (isset($ep)) {
          $ep = $this->resolveExposureProgram($ep);
      }


    // *** Resolve MeteringMode
    $mm = $exifData['MeteringMode'];
      $mm = $this->resolveMeteringMode($mm);

    // *** Resolve Flash
    $flash = $exifData['Flash'];
      $flash = $this->resolveFlash($flash);


      if (isset($exifData['Make'])) {
          $exifDataArray['make'] = $exifData['Make'];
      } else {
          $exifDataArray['make'] = '';
      }

      if (isset($exifData['Model'])) {
          $exifDataArray['model'] = $exifData['Model'];
      } else {
          $exifDataArray['model'] = '';
      }

      if (isset($exifData['DateTime'])) {
          $exifDataArray['date'] = $exifData['DateTime'];
      } else {
          $exifDataArray['date'] = '';
      }

      if (isset($exifData['ExposureTime'])) {
          $exifDataArray['exposure time'] = $exifData['ExposureTime'] . ' sec.';
      } else {
          $exifDataArray['exposure time'] = '';
      }

      if ($apertureValue != '') {
          $exifDataArray['aperture value'] = $apertureValue;
      } else {
          $exifDataArray['aperture value'] = '';
      }

      if (isset($exifData['COMPUTED']['ApertureFNumber'])) {
          $exifDataArray['f-stop'] = $exifData['COMPUTED']['ApertureFNumber'];
      } else {
          $exifDataArray['f-stop'] = '';
      }

      if (isset($exifData['FNumber'])) {
          $exifDataArray['fnumber'] = $exifData['FNumber'];
      } else {
          $exifDataArray['fnumber'] = '';
      }

      if ($fNumber != '') {
          $exifDataArray['fnumber value'] = $fNumber;
      } else {
          $exifDataArray['fnumber value'] = '';
      }

      if (isset($exifData['ISOSpeedRatings'])) {
          $exifDataArray['iso'] = $exifData['ISOSpeedRatings'];
      } else {
          $exifDataArray['iso'] = '';
      }

      if ($focalLength != '') {
          $exifDataArray['focal length'] = $focalLength;
      } else {
          $exifDataArray['focal length'] = '';
      }

      if (isset($ep)) {
          $exifDataArray['exposure program'] = $ep;
      } else {
          $exifDataArray['exposure program'] = '';
      }

      if ($mm != '') {
          $exifDataArray['metering mode'] = $mm;
      } else {
          $exifDataArray['metering mode'] = '';
      }

      if ($flash != '') {
          $exifDataArray['flash status'] = $flash;
      } else {
          $exifDataArray['flash status'] = '';
      }

      if (isset($exifData['Artist'])) {
          $exifDataArray['creator'] = $exifData['Artist'] ;
      } else {
          $exifDataArray['creator'] = '';
      }

      if (isset($exifData['Copyright'])) {
          $exifDataArray['copyright'] = $exifData['Copyright'];
      } else {
          $exifDataArray['copyright'] = '';
      }

    // *** Orientation
    if (isset($exifData['Orientation'])) {
        $exifDataArray['orientation'] = $exifData['Orientation'];
    } else {
        $exifDataArray['orientation'] = '';
    }

      return $exifDataArray;
  }

  ## --------------------------------------------------------

  private function resolveExposureProgram($ep)
  {
      switch ($ep) {
      case 0:
        $ep = '';
        break;
      case 1:
        $ep = 'manual';
        break;
      case 2:
        $ep = 'normal program';
        break;
      case 3:
        $ep = 'aperture priority';
        break;
      case 4:
        $ep = 'shutter priority';
        break;
      case 5:
        $ep = 'creative program';
        break;
      case 6:
        $ep = 'action program';
        break;
      case 7:
        $ep = 'portrait mode';
        break;
      case 8:
        $ep = 'landscape mode';
        break;

      default:
        break;
    }

      return $ep;
  }

  ## --------------------------------------------------------

  private function resolveMeteringMode($mm)
  {
      switch ($mm) {
      case 0:
        $mm = 'unknown';
        break;
      case 1:
        $mm = 'average';
        break;
      case 2:
        $mm = 'center weighted average';
        break;
      case 3:
        $mm = 'spot';
        break;
      case 4:
        $mm = 'multi spot';
        break;
      case 5:
        $mm = 'pattern';
        break;
      case 6:
        $mm = 'partial';
        break;
      case 255:
        $mm = 'other';
        break;

      default:
        break;
    }

      return $mm;
  }

  ## --------------------------------------------------------

  private function resolveFlash($flash)
  {
      switch ($flash) {
      case 0:
        $flash = 'flash did not fire';
        break;
      case 1:
        $flash = 'flash fired';
        break;
      case 5:
        $flash = 'strobe return light not detected';
        break;
      case 7:
        $flash = 'strobe return light detected';
        break;
      case 9:
        $flash = 'flash fired, compulsory flash mode';
        break;
      case 13:
        $flash = 'flash fired, compulsory flash mode, return light not detected';
        break;
      case 15:
        $flash = 'flash fired, compulsory flash mode, return light detected';
        break;
      case 16:
        $flash = 'flash did not fire, compulsory flash mode';
        break;
      case 24:
        $flash = 'flash did not fire, auto mode';
        break;
      case 25:
        $flash = 'flash fired, auto mode';
        break;
      case 29:
        $flash = 'flash fired, auto mode, return light not detected';
        break;
      case 31:
        $flash = 'flash fired, auto mode, return light detected';
        break;
      case 32:
        $flash = 'no flash function';
        break;
      case 65:
        $flash = 'flash fired, red-eye reduction mode';
        break;
      case 69:
        $flash = 'flash fired, red-eye reduction mode, return light not detected';
        break;
      case 71:
        $flash = 'flash fired, red-eye reduction mode, return light detected';
        break;
      case 73:
        $flash = 'flash fired, compulsory flash mode, red-eye reduction mode';
        break;
      case 77:
        $flash = 'flash fired, compulsory flash mode, red-eye reduction mode, return light not detected';
        break;
      case 79:
        $flash = 'flash fired, compulsory flash mode, red-eye reduction mode, return light detected';
        break;
      case 89:
        $flash = 'flash fired, auto mode, red-eye reduction mode';
        break;
      case 93:
        $flash = 'flash fired, auto mode, return light not detected, red-eye reduction mode';
        break;
      case 95:
        $flash = 'flash fired, auto mode, return light detected, red-eye reduction mode';
        break;

      default:
        break;
    }

      return $flash;
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Get IPTC Data
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Write IPTC Data
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function writeIPTCcaption($value)
  # Caption
  {
      $this->writeIPTC(120, $value);
  }

  ## --------------------------------------------------------

  public function writeIPTCwriter($value)
  {
      //$this->writeIPTC(65, $value);
  }

  ## --------------------------------------------------------

  private function writeIPTC($dat, $value)
  {

    # LIMIT TO JPG

    $caption_block = $this->iptc_maketag(2, $dat, $value);
      $image_string = iptcembed($caption_block, $this->fileName);
      file_put_contents('iptc.jpg', $image_string);
  }

## --------------------------------------------------------

  private function iptc_maketag($rec, $dat, $val)
  # Author:   Thies C. Arntzen
  # Purpose:    Function to format the new IPTC text
  # Param in:   $rec: Application record. (We’re working with #2)
  #       $dat: Index. (120 for caption, 118 for contact. See the IPTC IIM
  #         specification:
  #         http://www.iptc.org/std/IIM/4.1/specification/IIMV4.1.pdf
  #       $val: Value/data/text. Make sure this is within the length
  #         constraints of the IPTC IIM specification
  # Ref:      http://blog.peterhaza.no/working-with-image-meta-data-in-exif-and-iptc-headers-from-php/
  #       http://php.net/manual/en/function.iptcembed.php
  #
  {
      $len = strlen($val);
      if ($len < 0x8000) {
          return chr(0x1c).chr($rec).chr($dat).
      chr($len >> 8).
      chr($len & 0xff).
      $val;
      } else {
          return chr(0x1c).chr($rec).chr($dat).
      chr(0x80).chr(0x04).
      chr(($len >> 24) & 0xff).
      chr(($len >> 16) & 0xff).
      chr(($len >> 8) & 0xff).
      chr(($len) & 0xff).
      $val;
      }
  }



/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Write XMP Data
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  //http://xmpphptoolkit.sourceforge.net/


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Add Text
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function addText($text, $pos = '20x20', $padding = 0, $fontColor='#fff', $fontSize = 12, $angle = 0, $font = null)
    # Author:     Jarrod Oberto
  # Date:       18-11-09
    # Purpose:    Add text to an image
    # Param in:
    # Param out:
    # Reference:  http://php.net/manual/en/function.imagettftext.php
    # Notes:      Make sure you supply the font.
    #
  {

    // *** Convert color
    $rgbArray = $this->formatColor($fontColor);
      $r = $rgbArray['r'];
      $g = $rgbArray['g'];
      $b = $rgbArray['b'];

    // *** Get text font
    $font = $this->getTextFont($font);

    // *** Get text size
    $textSizeArray = $this->getTextSize($fontSize, $angle, $font, $text);
      $textWidth = $textSizeArray['width'];
      $textHeight = $textSizeArray['height'];

    // *** Find co-ords to place text
    $posArray = $this->calculatePosition($pos, $padding, $textWidth, $textHeight, false);
      $x = $posArray['width'];
      $y = $posArray['height'];

      $fontColor = imagecolorallocate($this->imageResized, $r, $g, $b);

    // *** Add text
    imagettftext($this->imageResized, $fontSize, $angle, $x, $y, $fontColor, $font, $text);
  }

  ## --------------------------------------------------------

  private function getTextFont($font)
  {
      // *** Font path (shou
    $fontPath =  dirname(__FILE__) . '/' . $this->fontDir;


    // *** The below is/may be needed depending on your version (see ref)
    putenv('GDFONTPATH=' . realpath('.'));

    // *** Check if the passed in font exsits...
    if ($font == null || !file_exists($font)) {

      // *** ...If not, default to this font.
      $font = $fontPath . '/arimo.ttf';

      // *** Check our default font exists...
      if (!file_exists($font)) {

        // *** If not, return false
        if ($this->debug) {
            throw new Exception('Font not found');
        } else {
            return false;
        }
      }
    }

      return $font;
  }

  ## --------------------------------------------------------

  private function getTextSize($fontSize, $angle, $font, $text)
  {

    // *** Define box (so we can get the width)
    $box = @imageTTFBbox($fontSize, $angle, $font, $text);

    // ***  Get width of text from dimensions
    $textWidth = abs($box[4] - $box[0]);

    // ***  Get height of text from dimensions (should also be same as $fontSize)
    $textHeight = abs($box[5] - $box[1]);

      return array('height' => $textHeight, 'width' => $textWidth);
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  Add Watermark
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  public function addWatermark($watermarkImage, $pos, $padding = 0, $opacity = 0)
    # Author:     Jarrod Oberto
    # Date:       18-11-09
    # Purpose:    Add watermark image
    # Param in:   (str) $watermark: The watermark image
  #       (str) $pos: Could be a pre-determined position such as:
  #           tl = top left,
  #           t  = top (middle),
  #           tr = top right,
  #           l  = left,
  #           m  = middle,
  #           r  = right,
  #           bl = bottom left,
  #           b  = bottom (middle),
  #           br = bottom right
  #         Or, it could be a co-ordinate position such as: 50x100
  #
  #       (int) $padding: If using a pre-determined position you can
  #         adjust the padding from the edges by passing an amount
  #         in pixels. If using co-ordinates, this value is ignored.
    # Param out:
    # Reference:  http://www.php.net/manual/en/image.examples-watermark.php
    # Notes:      Based on example in reference.
  #
    #
  {

    // Load the stamp and the photo to apply the watermark to
    $stamp = $this->openImage($watermarkImage);    # stamp
    $im = $this->imageResized;            # photo

    // *** Get stamps width and height
    $sx = imagesx($stamp);
      $sy = imagesy($stamp);

    // *** Find co-ords to place image
    $posArray = $this->calculatePosition($pos, $padding, $sx, $sy);
      $x = $posArray['width'];
      $y = $posArray['height'];

    // *** Set watermark opacity
    if (fix_strtolower(strrchr($watermarkImage, '.')) == '.png') {
        $opacity = $this->invertTransparency($opacity, 100);
        $this->filterOpacity($stamp, $opacity);
    }

    // Copy the watermark image onto our photo
    imagecopy($im, $stamp, $x, $y, 0, 0, imagesx($stamp), imagesy($stamp));
  }

  ## --------------------------------------------------------

  private function calculatePosition($pos, $padding, $assetWidth, $assetHeight, $upperLeft = true)
  #
  # Author:   Jarrod Oberto
  # Date:   08-05-11
  # Purpose:  Calculate the x, y pixel cordinates of the asset to place
  # Params in:  (str) $pos: Either something like: "tl", "l", "br" or an
  #         exact position like: "100x50"
  #       (int) $padding: The amount of padding from the edge. Only
  #         used for the predefined $pos.
  #       (int) $assetWidth: The width of the asset to add to the image
  #       (int) $assetHeight: The height of the asset to add to the image
  #       (bol) $upperLeft: if true, the asset will be positioned based
  #         on the upper left x, y coords. If false, it means you're
  #         using the lower left as the basepoint and this will
  #         convert it to the upper left position
  # Params out:
  # NOTE: this is done from the UPPER left corner!! But will convert lower
  #   left basepoints to upper left if $upperleft is set to false
  #
  #
  {
      $pos = fix_strtolower($pos);

    // *** If co-ords have been entered
    if (strstr($pos, 'x')) {
        $pos = str_replace(' ', '', $pos);

        $xyArray = explode('x', $pos);
        list($width, $height) = $xyArray;
    } else {
        switch ($pos) {
        case 'tl':
          $width = 0 + $padding;
          $height = 0 + $padding;
          break;

        case 't':
          $width = ($this->width / 2) - ($assetWidth / 2);
          $height = 0 + $padding;
          break;

        case 'tr':
          $width = $this->width - $assetWidth - $padding;
          $height = 0 + $padding;;
          break;

        case 'l':
          $width = 0 + $padding;
          $height = ($this->height / 2) - ($assetHeight / 2);
          break;

        case 'm':
          $width = ($this->width / 2) - ($assetWidth / 2);
          $height = ($this->height / 2) - ($assetHeight / 2);
          break;

        case 'r':
          $width = $this->width - $assetWidth - $padding;
          $height = ($this->height / 2) - ($assetHeight / 2);
          break;

        case 'bl':
          $width = 0 + $padding;
          $height = $this->height - $assetHeight - $padding;
          break;

        case 'b':
          $width = ($this->width / 2) - ($assetWidth / 2);
          $height = $this->height - $assetHeight - $padding;
          break;

        case 'br':
          $width = $this->width - $assetWidth - $padding;
          $height = $this->height - $assetHeight - $padding;
          break;

        default:
          $width = 0;
          $height = 0;
          break;
      }
    }

      if (!$upperLeft) {
          $height = $height + $assetHeight;
      }

      return array('width' => $width, 'height' => $height);
  }


  ## --------------------------------------------------------

  private function filterOpacity(&$img, $opacity = 75)
  #
  # Author:     aiden dot mail at freemail dot hu
  # Author date:  29-03-08 08:16
  # Date added:   08-05-11
  # Purpose:    Change opacity of image
  # Params in:    $img: Image resource id
  #         (int) $opacity: the opacity amount: 0-100, 100 being not opaque.
  # Params out:   (bool) true on success, else false
  # Ref:      http://www.php.net/manual/en/function.imagefilter.php#82162
  # Notes:      png only
  #
  {
      if (!isset($opacity)) {
          return false;
      }

      if ($opacity == 100) {
          return true;
      }

      $opacity /= 100;

    //get image width and height
    $w = imagesx($img);
      $h = imagesy($img);

    //turn alpha blending off
    imagealphablending($img, false);

    //find the most opaque pixel in the image (the one with the smallest alpha value)
    $minalpha = 127;
      for ($x = 0; $x < $w; $x++) {
          for ($y = 0; $y < $h; $y++) {
              $alpha = (imagecolorat($img, $x, $y) >> 24) & 0xFF;
              if ($alpha < $minalpha) {
                  $minalpha = $alpha;
              }
          }
      }

    //loop through image pixels and modify alpha for each
    for ($x = 0; $x < $w; $x++) {
        for ($y = 0; $y < $h; $y++) {
            //get current alpha value (represents the TANSPARENCY!)
        $colorxy = imagecolorat($img, $x, $y);
            $alpha = ($colorxy >> 24) & 0xFF;
        //calculate new alpha
        if ($minalpha !== 127) {
            $alpha = 127 + 127 * $opacity * ($alpha - 127) / (127 - $minalpha);
        } else {
            $alpha += 127 * $opacity;
        }
        //get the color index with new alpha
        $alphacolorxy = imagecolorallocatealpha($img, ($colorxy >> 16) & 0xFF, ($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha);
        //set pixel with the new color + opacity
        if (!imagesetpixel($img, $x, $y, $alphacolorxy)) {
            return false;
        }
        }
    }

      return true;
  }

## --------------------------------------------------------

    private function openImage($file)
    # Author:     Jarrod Oberto
    # Date:       27-02-08
    # Purpose:
    # Param in:
    # Param out:  n/a
    # Reference:
    # Notes:
    #
    {
        if (!file_exists($file) && !$this->checkStringStartsWith('http://', $file)) {
            if ($this->debug) {
                throw new Exception('Image not found.');
            } else {
                throw new Exception();
            }
        };

        // *** Get extension
        $extension = strrchr($file, '.');
        $extension = fix_strtolower($extension);
        switch ($extension) {
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
            case '.bmp':
                $img = @$this->imagecreatefrombmp($file);
                break;
            case '.psd':
                $img = @$this->imagecreatefrompsd($file);
                break;


            // ... etc

            default:
                $img = false;
                break;
        }

        return $img;
    }

## --------------------------------------------------------

  public function reset()
  #
  # Author:   Jarrod Oberto
  # Date:   30-08-11
  # Purpose:  Reset the resource (allow further editing)
  # Params in:
  # Params out:
  # Notes:
  #
  {
      $this->__construct($this->fileName);
  }

## --------------------------------------------------------

    public function saveImage($savePath, $imageQuality="100")
    # Author:     Jarrod Oberto
    # Date:       27-02-08
    # Purpose:    Saves the image
    # Param in:   $savePath: Where to save the image including filename:
    #             $imageQuality: image quality you want the image saved at 0-100
    # Param out:  n/a
    # Reference:
    # Notes:    * gif doesn't have a quality parameter
  #       * jpg has a quality setting 0-100 (100 being the best)
    #       * png has a quality setting 0-9 (0 being the best)
  #
  #             * bmp files have no native support for bmp files. We use a
  #       third party class to save as bmp.
    {

    // *** Perform a check or two.
    if (!is_resource($this->imageResized)) {
        if ($this->debug) {
            throw new Exception('saveImage: This is not a resource.');
        } else {
            throw new Exception();
        }
    }
        $fileInfoArray = pathInfo($savePath);
        clearstatcache();
        if (!is_writable($fileInfoArray['dirname'])) {
            if ($this->debug) {
                throw new Exception('The path is not writable. Please check your permissions.');
            } else {
                throw new Exception();
            }
        }

    // *** Get extension
        $extension = strrchr($savePath, '.');
        $extension = fix_strtolower($extension);

        $error = '';

        switch ($extension) {
            case '.jpg':
            case '.jpeg':
        $this->checkInterlaceImage($this->isInterlace);
        if (imagetypes() & IMG_JPG) {
            imagejpeg($this->imageResized, $savePath, $imageQuality);
        } else {
            $error = 'jpg';
        }
                break;

            case '.gif':
        $this->checkInterlaceImage($this->isInterlace);
        if (imagetypes() & IMG_GIF) {
            imagegif($this->imageResized, $savePath);
        } else {
            $error = 'gif';
        }
                break;

            case '.png':
        // *** Scale quality from 0-100 to 0-9
        $scaleQuality = round(($imageQuality/100) * 9);

        // *** Invert qualit setting as 0 is best, not 9
        $invertScaleQuality = 9 - $scaleQuality;

        $this->checkInterlaceImage($this->isInterlace);
        if (imagetypes() & IMG_PNG) {
            imagepng($this->imageResized, $savePath, $invertScaleQuality);
        } else {
            $error = 'png';
        }
                break;

            case '.bmp':
        file_put_contents($savePath, $this->GD2BMPstring($this->imageResized));
          break;


            // ... etc

            default:
        // *** No extension - No save.
        $this->errorArray[] = 'This file type (' . $extension . ') is not supported. File not saved.';
                break;
        }

    //imagedestroy($this->imageResized);

    // *** Display error if a file type is not supported.
    if ($error != '') {
        $this->errorArray[] = $error . ' support is NOT enabled. File not saved.';
    }
    }

## --------------------------------------------------------

  public function displayImage($fileType = 'jpg', $imageQuality="100")
    # Author:     Jarrod Oberto
    # Date:       18-11-09
    # Purpose:    Display images directly to the browser
    # Param in:   The image type you want to display
    # Param out:
    # Reference:
    # Notes:
    #
  {
      if (!is_resource($this->imageResized)) {
          if ($this->debug) {
              throw new Exception('saveImage: This is not a resource.');
          } else {
              throw new Exception();
          }
      }

      switch ($fileType) {
            case 'jpg':
            case 'jpeg':
        header('Content-type: image/jpeg');
        imagejpeg($this->imageResized, '', $imageQuality);
                break;
            case 'gif':
        header('Content-type: image/gif');
        imagegif($this->imageResized);
                break;
            case 'png':
        header('Content-type: image/png');

        // *** Scale quality from 0-100 to 0-9
        $scaleQuality = round(($imageQuality/100) * 9);

        // *** Invert qualit setting as 0 is best, not 9
        $invertScaleQuality = 9 - $scaleQuality;

        imagepng($this->imageResized, '', $invertScaleQuality);
        break;
      case 'bmp':
        echo 'bmp file format is not supported.';
        break;

            // ... etc

            default:
        // *** No extension - No save.
                break;
        }


    //imagedestroy($this->imageResized);
  }

## --------------------------------------------------------

  public function setTransparency($bool)
  # Sep 2011
  {
      $this->keepTransparency = $bool;
  }

## --------------------------------------------------------

  public function setFillColor($value)
  # Sep 2011
    # Param in:   (mixed) $value: (array) Could be an array of RGB
  #               (str) Could be hex #ffffff or #fff, fff, ffffff
  #
  # If the keepTransparency is set to false, then no transparency is to be used.
  # This is ideal when you want to save as jpg.
  #
  # this method allows you to set the background color to use instead of
  # transparency.
  #
  {
      $colorArray = $this->formatColor($value);
      $this->fillColorArray = $colorArray;
  }

## --------------------------------------------------------

  public function setCropFromTop($value)
  # Sep 2011
  {
      $this->cropFromTopPercent = $value;
  }

## --------------------------------------------------------

    public function testGDInstalled()
    # Author:     Jarrod Oberto
    # Date:       27-02-08
    # Purpose:    Test to see if GD is installed
    # Param in:   n/a
    # Param out:  (bool) True is gd extension loaded otherwise false
    # Reference:
    # Notes:
    #
    {
        if (extension_loaded('gd') && function_exists('gd_info')) {
            $gdInstalled = true;
        } else {
            $gdInstalled = false;
        }

        return $gdInstalled;
    }

## --------------------------------------------------------

    public function testEXIFInstalled()
    # Author:     Jarrod Oberto
    # Date:       08-05-11
    # Purpose:    Test to see if EXIF is installed
    # Param in:   n/a
    # Param out:  (bool) True is exif extension loaded otherwise false
    # Reference:
    # Notes:
    #
    {
        if (extension_loaded('exif')) {
            $exifInstalled = true;
        } else {
            $exifInstalled = false;
        }

        return $exifInstalled;
    }

## --------------------------------------------------------

    public function testIsImage($image)
    # Author:     Jarrod Oberto
    # Date:       27-02-08
    # Purpose:    Test if file is an image
    # Param in:   n/a
    # Param out:  n/a
    # Reference:
    # Notes:
    #
    {
        if ($image) {
            $fileIsImage = true;
        } else {
            $fileIsImage = false;
        }

        return $fileIsImage;
    }

## --------------------------------------------------------

    public function testFunct()
    # Author:     Jarrod Oberto
    # Date:       27-02-08
    # Purpose:    Test Function
    # Param in:   n/a
    # Param out:  n/a
    # Reference:
    # Notes:
    #
    {
        echo $this->height;
    }

## --------------------------------------------------------

    public function setForceStretch($value)
    # Author:     Jarrod Oberto
    # Date:       23-12-10
    # Purpose:
    # Param in:   (bool) $value
    # Param out:  n/a
    # Reference:
    # Notes:
    #
    {
        $this->forceStretch = $value;
    }

## --------------------------------------------------------

    public function setFile($fileName)
    # Author:     Jarrod Oberto
    # Date:       28-02-08
    # Purpose:
    # Param in:   n/a
    # Param out:  n/a
    # Reference:
    # Notes:
    #
    {
        self::__construct($fileName);
    }

## --------------------------------------------------------

  public function getFileName()
    # Author:     Jarrod Oberto
    # Date:       10-09-08
    # Purpose:
    # Param in:   n/a
    # Param out:  n/a
    # Reference:
    # Notes:
    #
  {
      return $this->fileName;
  }

## --------------------------------------------------------

  public function getHeight()
  {
      return $this->height;
  }

## --------------------------------------------------------

  public function getWidth()
  {
      return $this->width;
  }

## --------------------------------------------------------

  public function getOriginalHeight()
  {
      return $this->heightOriginal;
  }

## --------------------------------------------------------

  public function getOriginalWidth()
  {
      return $this->widthOriginal;
  }

## --------------------------------------------------------

  public function getErrors()
    # Author:     Jarrod Oberto
    # Date:       19-11-09
    # Purpose:    Returns the error array
    # Param in:   n/a
    # Param out:  Array of errors
    # Reference:
    # Notes:
    #
  {
      return $this->errorArray;
  }

## --------------------------------------------------------

  private function checkInterlaceImage($isEnabled)
  # jpg will use progressive (they don't use interace)
  {
      if ($isEnabled) {
          imageinterlace($this->imageResized, $isEnabled);
      }
  }

## --------------------------------------------------------

  protected function formatColor($value)
    # Author:     Jarrod Oberto
    # Date:       09-05-11
    # Purpose:    Determine color method passed in and return color as RGB
    # Param in:   (mixed) $value: (array) Could be an array of RGB
  #               (str) Could be hex #ffffff or #fff, fff, ffffff
    # Param out:
    # Reference:
    # Notes:
    #
  {
      $rgbArray = array();

    // *** If it's an array it should be R, G, B
    if (is_array($value)) {
        if (key($value) == 0 && count($value) == 3) {
            $rgbArray['r'] = $value[0];
            $rgbArray['g'] = $value[1];
            $rgbArray['b'] = $value[2];
        } else {
            $rgbArray = $value;
        }
    } elseif (fix_strtolower($value) == 'transparent') {
        $rgbArray = array(
        'r' => 255,
        'g' => 255,
        'b' => 255,
        'a' => 127
      );
    } else {

      // *** ...Else it should be hex. Let's make it RGB
      $rgbArray = $this -> hex2dec($value);
    }

      return $rgbArray;
  }

  ## --------------------------------------------------------

  function hex2dec($hex)
  # Purpose:  Convert #hex color to RGB
  {
      $color = str_replace('#', '', $hex);

      if (strlen($color) == 3) {
          $color = $color . $color;
      }

      $rgb = array(
      'r' => hexdec(substr($color, 0, 2)),
      'g' => hexdec(substr($color, 2, 2)),
      'b' => hexdec(substr($color, 4, 2)),
      'a' => 0
    );
      return $rgb;
  }

  ## --------------------------------------------------------

  private function createImageColor($colorArray)
  {
      $r = $colorArray['r'];
      $g = $colorArray['g'];
      $b = $colorArray['b'];

      return imagecolorallocate($this->imageResized, $r, $g, $b);
  }

  ## --------------------------------------------------------

  private function testColorExists($colorArray)
  {
      $r = $colorArray['r'];
      $g = $colorArray['g'];
      $b = $colorArray['b'];

      if (imagecolorexact($this->imageResized, $r, $g, $b) == -1) {
          return false;
      } else {
          return true;
      }
  }

  ## --------------------------------------------------------

  private function findUnusedGreen()
  # Purpose:  We find a green color suitable to use like green-screen effect.
  #     Therefore, the color must not exist in the image.
  {
      $green = 255;

      do {
          $greenChroma = array(0, $green, 0);
          $colorArray = $this->formatColor($greenChroma);
          $match = $this->testColorExists($colorArray);
          $green--;
      } while ($match == false && $green > 0);

    // *** If no match, just bite the bullet and use green value of 255
    if (!$match) {
        $greenChroma = array(0, $green, 0);
    }

      return $greenChroma;
  }

  ## --------------------------------------------------------

  private function findUnusedBlue()
  # Purpose:  We find a green color suitable to use like green-screen effect.
  #     Therefore, the color must not exist in the image.
  {
      $blue = 255;

      do {
          $blueChroma = array(0, 0, $blue);
          $colorArray = $this->formatColor($blueChroma);
          $match = $this->testColorExists($colorArray);
          $blue--;
      } while ($match == false && $blue > 0);

    // *** If no match, just bite the bullet and use blue value of 255
    if (!$match) {
        $blueChroma = array(0, 0, $blue);
    }

      return $blueChroma;
  }

  ## --------------------------------------------------------

  private function invertTransparency($value, $originalMax, $invert=true)
  # Purpose:  This does two things:
  #       1) Convert the range from 0-127 to 0-100
  #       2) Inverts value to 100 is not transparent while 0 is fully
  #          transparent (like Photoshop)
  {
      // *** Test max range
    if ($value > $originalMax) {
        $value = $originalMax;
    }

    // *** Test min range
    if ($value < 0) {
        $value = 0;
    }

      if ($invert) {
          return $originalMax - (($value/100) * $originalMax);
      } else {
          return ($value/100) * $originalMax;
      }
  }

  ## --------------------------------------------------------

  private function transparentImage($src)
  {
      // *** making images with white bg transparent
    $r1 = 0;
      $g1 = 255;
      $b1 = 0;
      for ($x = 0; $x < imagesx($src); ++$x) {
          for ($y = 0; $y < imagesy($src); ++$y) {
              $color = imagecolorat($src, $x, $y);
              $r = ($color >> 16) & 0xFF;
              $g = ($color >> 8) & 0xFF;
              $b = $color & 0xFF;
              for ($i = 0; $i < 270; $i++) {
                  //if ($r . $g . $b == ($r1 + $i) . ($g1 + $i) . ($b1 + $i)) {
          if ($r == 0 && $g == 255 && $b == 0) {
              //if ($g == 255) {
            $trans_colour = imagecolorallocatealpha($src, 0, 0, 0, 127);
              imagefill($src, $x, $y, $trans_colour);
          }
              }
          }
      }

      return $src;
  }

  ## --------------------------------------------------------

  function checkStringStartsWith($needle, $haystack)
  # Check if a string starts with a specific pattern
  {
      return (substr($haystack, 0, strlen($needle))==$needle);
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  BMP SUPPORT (SAVING) - James Heinrich
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  private function GD2BMPstring(&$gd_image)
    # Author:     James Heinrich
    # Purpose:    Save file as type bmp
    # Param in:   The image canvas (passed as ref)
    # Param out:
    # Reference:
    # Notes:    This code was stripped out of two external files
  #       (phpthumb.bmp.php,phpthumb.functions.php) and added below to
  #       avoid dependancies.
    #
  {
      $imageX = ImageSX($gd_image);
      $imageY = ImageSY($gd_image);

      $BMP = '';
      for ($y = ($imageY - 1); $y >= 0; $y--) {
          $thisline = '';
          for ($x = 0; $x < $imageX; $x++) {
              $argb = $this->GetPixelColor($gd_image, $x, $y);
              $thisline .= chr($argb['blue']).chr($argb['green']).chr($argb['red']);
          }
          while (strlen($thisline) % 4) {
              $thisline .= "\x00";
          }
          $BMP .= $thisline;
      }

      $bmpSize = strlen($BMP) + 14 + 40;
    // BITMAPFILEHEADER [14 bytes] - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_62uq.asp
    $BITMAPFILEHEADER  = 'BM';                                    // WORD    bfType;
    $BITMAPFILEHEADER .= $this->LittleEndian2String($bmpSize, 4); // DWORD   bfSize;
    $BITMAPFILEHEADER .= $this->LittleEndian2String(0, 2); // WORD    bfReserved1;
    $BITMAPFILEHEADER .= $this->LittleEndian2String(0, 2); // WORD    bfReserved2;
    $BITMAPFILEHEADER .= $this->LittleEndian2String(54, 4); // DWORD   bfOffBits;

    // BITMAPINFOHEADER - [40 bytes] http://msdn.microsoft.com/library/en-us/gdi/bitmaps_1rw2.asp
    $BITMAPINFOHEADER  = $this->LittleEndian2String(40, 4); // DWORD  biSize;
    $BITMAPINFOHEADER .= $this->LittleEndian2String($imageX, 4); // LONG   biWidth;
    $BITMAPINFOHEADER .= $this->LittleEndian2String($imageY, 4); // LONG   biHeight;
    $BITMAPINFOHEADER .= $this->LittleEndian2String(1, 2); // WORD   biPlanes;
    $BITMAPINFOHEADER .= $this->LittleEndian2String(24, 2); // WORD   biBitCount;
    $BITMAPINFOHEADER .= $this->LittleEndian2String(0, 4); // DWORD  biCompression;
    $BITMAPINFOHEADER .= $this->LittleEndian2String(0, 4); // DWORD  biSizeImage;
    $BITMAPINFOHEADER .= $this->LittleEndian2String(2835, 4); // LONG   biXPelsPerMeter;
    $BITMAPINFOHEADER .= $this->LittleEndian2String(2835, 4); // LONG   biYPelsPerMeter;
    $BITMAPINFOHEADER .= $this->LittleEndian2String(0, 4); // DWORD  biClrUsed;
    $BITMAPINFOHEADER .= $this->LittleEndian2String(0, 4); // DWORD  biClrImportant;

    return $BITMAPFILEHEADER.$BITMAPINFOHEADER.$BMP;
  }

## --------------------------------------------------------

  private function GetPixelColor(&$img, $x, $y)
    # Author:     James Heinrich
    # Purpose:
    # Param in:
    # Param out:
    # Reference:
    # Notes:
    #
  {
      if (!is_resource($img)) {
          return false;
      }
      return @ImageColorsForIndex($img, @ImageColorAt($img, $x, $y));
  }

## --------------------------------------------------------

  private function LittleEndian2String($number, $minbytes=1)
    # Author:     James Heinrich
    # Purpose:    BMP SUPPORT (SAVING)
    # Param in:
    # Param out:
    # Reference:
    # Notes:
    #
  {
      $intstring = '';
      while ($number > 0) {
          $intstring = $intstring.chr($number & 255);
          $number >>= 8;
      }
      return str_pad($intstring, $minbytes, "\x00", STR_PAD_RIGHT);
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  BMP SUPPORT (READING)
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  private function ImageCreateFromBMP($filename)
    # Author:     DHKold
    # Date:     The 15th of June 2005
  # Version:    2.0B
    # Purpose:    To create an image from a BMP file.
    # Param in:   BMP file to open.
    # Param out:  Return a resource like the other ImageCreateFrom functions
    # Reference:  http://us3.php.net/manual/en/function.imagecreate.php#53879
  # Bug fix:    Author:   domelca at terra dot es
  #       Date:   06 March 2008
  #       Fix:    Correct 16bit BMP support
    # Notes:
  #
  {

    //Ouverture du fichier en mode binaire
    if (! $f1 = fopen($filename, "rb")) {
        return false;
    }

    //1 : Chargement des ent�tes FICHIER
    $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
      if ($FILE['file_type'] != 19778) {
          return false;
      }

    //2 : Chargement des ent�tes BMP
    $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
           '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
           '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
      $BMP['colors'] = pow(2, $BMP['bits_per_pixel']);

      if ($BMP['size_bitmap'] == 0) {
          $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
      }

      $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
      $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
      $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
      $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
      $BMP['decal'] = 4-(4*$BMP['decal']);

      if ($BMP['decal'] == 4) {
          $BMP['decal'] = 0;
      }

    //3 : Chargement des couleurs de la palette
    $PALETTE = array();
      if ($BMP['colors'] < 16777216) {
          $PALETTE = unpack('V'.$BMP['colors'], fread($f1, $BMP['colors']*4));
      }

    //4 : Cr�ation de l'image
    $IMG = fread($f1, $BMP['size_bitmap']);
      $VIDE = chr(0);

      $res = imagecreatetruecolor($BMP['width'], $BMP['height']);
      $P = 0;
      $Y = $BMP['height']-1;
      while ($Y >= 0) {
          $X=0;
          while ($X < $BMP['width']) {
              if ($BMP['bits_per_pixel'] == 24) {
                  $COLOR = unpack("V", substr($IMG, $P, 3).$VIDE);
              } elseif ($BMP['bits_per_pixel'] == 16) {

          /*
           * BMP 16bit fix
           * =================
           *
           * Ref: http://us3.php.net/manual/en/function.imagecreate.php#81604
           *
           * Notes:
           * "don't work with bmp 16 bits_per_pixel. change pixel
           * generator for this."
           *
           */

          // *** Original code (don't work)
          //$COLOR = unpack("n",substr($IMG,$P,2));
          //$COLOR[1] = $PALETTE[$COLOR[1]+1];

          $COLOR = unpack("v", substr($IMG, $P, 2));
                  $blue = ($COLOR[1] & 0x001f) << 3;
                  $green = ($COLOR[1] & 0x07e0) >> 3;
                  $red = ($COLOR[1] & 0xf800) >> 8;
                  $COLOR[1] = $red * 65536 + $green * 256 + $blue;
              } elseif ($BMP['bits_per_pixel'] == 8) {
                  $COLOR = unpack("n", $VIDE.substr($IMG, $P, 1));
                  $COLOR[1] = $PALETTE[$COLOR[1]+1];
              } elseif ($BMP['bits_per_pixel'] == 4) {
                  $COLOR = unpack("n", $VIDE.substr($IMG, floor($P), 1));
                  if (($P*2)%2 == 0) {
                      $COLOR[1] = ($COLOR[1] >> 4) ;
                  } else {
                      $COLOR[1] = ($COLOR[1] & 0x0F);
                  }
                  $COLOR[1] = $PALETTE[$COLOR[1]+1];
              } elseif ($BMP['bits_per_pixel'] == 1) {
                  $COLOR = unpack("n", $VIDE.substr($IMG, floor($P), 1));
                  if (($P*8)%8 == 0) {
                      $COLOR[1] =  $COLOR[1]        >>7;
                  } elseif (($P*8)%8 == 1) {
                      $COLOR[1] = ($COLOR[1] & 0x40)>>6;
                  } elseif (($P*8)%8 == 2) {
                      $COLOR[1] = ($COLOR[1] & 0x20)>>5;
                  } elseif (($P*8)%8 == 3) {
                      $COLOR[1] = ($COLOR[1] & 0x10)>>4;
                  } elseif (($P*8)%8 == 4) {
                      $COLOR[1] = ($COLOR[1] & 0x8)>>3;
                  } elseif (($P*8)%8 == 5) {
                      $COLOR[1] = ($COLOR[1] & 0x4)>>2;
                  } elseif (($P*8)%8 == 6) {
                      $COLOR[1] = ($COLOR[1] & 0x2)>>1;
                  } elseif (($P*8)%8 == 7) {
                      $COLOR[1] = ($COLOR[1] & 0x1);
                  }
                  $COLOR[1] = $PALETTE[$COLOR[1]+1];
              } else {
                  return false;
              }

              imagesetpixel($res, $X, $Y, $COLOR[1]);
              $X++;
              $P += $BMP['bytes_per_pixel'];
          }

          $Y--;
          $P+=$BMP['decal'];
      }
    //Fermeture du fichier
    fclose($f1);

      return $res;
  }


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-
  PSD SUPPORT (READING)
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/

  private function imagecreatefrompsd($fileName)
  # Author:     Tim de Koning
  # Version:    1.3
  # Purpose:    To create an image from a PSD file.
  # Param in:   PSD file to open.
  # Param out:  Return a resource like the other ImageCreateFrom functions
  # Reference:  http://www.kingsquare.nl/phppsdreader
  # Notes:
  #
  {
      if (file_exists($this->psdReaderPath)) {
          include_once($this->psdReaderPath);

          $psdReader = new PhpPsdReader($fileName);

          if (isset($psdReader->infoArray['error'])) {
              return '';
          } else {
              return $psdReader->getImage();
          }
      } else {
          return false;
      }
  }

## --------------------------------------------------------

    public function __destruct()
    {
        if (is_resource($this->imageResized)) {
            imagedestroy($this->imageResized);
        }
    }

## --------------------------------------------------------
}




/*
 *    Example with some API calls (outdated):
 *
 *
 *      ===============================
 *      Compulsary
 *      ===============================
 *
 *      include("classes/resize_class.php");
 *
 *      // *** Initialise object
 *      $magicianObj = new resize('images/cars/large/a.jpg');
 *
 *      // *** Turn off stretching (optional)
 *      $magicianObj -> setForceStretch(false);
 *
 *      // *** Resize object
 *      $magicianObj -> resizeImage(150, 100, 0);
 *
 *      ===============================
 *      Image options - can run none, one, or all.
 *      ===============================
 *
 *      //  *** Add watermark
 *        $magicianObj -> addWatermark('stamp.png');
 *
 *          // *** Add text
 *      $magicianObj -> addText('testing...');
 *
 *      ===============================
 *      Output options - can run one, or the other, or both.
 *      ===============================
 *
 *      // *** Save image to disk
 *      $magicianObj -> saveImage('images/cars/large/b.jpg', 100);
 *
 *          // *** Or output to screen (params in can be jpg, gif, png)
 *      $magicianObj -> displayImage('png');
 *
 *      ===============================
 *      Return options - return errors. nice for debuggin.
 *      ===============================
 *
 *      // *** Return error array
 *      $errorArray = $magicianObj -> getErrors();
 *
 *
 *      ===============================
 *      Cleanup options - not really neccessary, but good practice
 *      ===============================
 *
 *      // *** Free used memory
 *      $magicianObj -> __destruct();
 */
