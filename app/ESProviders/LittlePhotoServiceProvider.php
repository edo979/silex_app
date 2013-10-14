<?php

namespace ESProviders;

use Silex\Application;
use Silex\ServiceProviderInterface;

class LittlePhotoServiceProvider implements ServiceProviderInterface
{

  private $temp_path;
  private $filename;
  private $type;
  private $size;
  private $errors = array();
  private $upload_errors = array(
// http://www.php.net/manual/en/features.file-upload.errors.php
      UPLOAD_ERR_OK         => "No errors.",
      UPLOAD_ERR_INI_SIZE   => "Larger than upload_max_filesize.",
      UPLOAD_ERR_FORM_SIZE  => "Larger than form MAX_FILE_SIZE.",
      UPLOAD_ERR_PARTIAL    => "Partial upload.",
      UPLOAD_ERR_NO_FILE    => "No file.",
      UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
      UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
      UPLOAD_ERR_EXTENSION  => "File upload stopped by extension."
  );

  public function boot(Application $app)
  {
    
  }

  public function register(Application $app)
  {
    $app['photoHandler'] = $app->protect(function() use($app)
      {
        
      });

    $app['photoHandler.errors'] = $this->errors;
  }

  // Get file from form
  private function attach_file($file)
  {
    // Perform error checking on the form parameters
    if (!$file || empty($file) || !is_array($file))
    {
      // error: nothing uploaded or wrong argument usage
      $this->errors[] = "No file was uploaded.";
      return false;
    }
    elseif ($file['error'] != 0)
    {
      // error: report what PHP says went wrong
      $this->errors[] = $this->upload_errors[$file['error']];
      return false;
    }
    else
    {
      // Set object attributes to the form parameters.
      $this->temp_path = $file['tmp_name'];
      $this->filename = basename($file['name']);
      $this->type = $file['type'];
      $this->size = $file['size'];

      return true;
    }
  }

  // Validate file
  private function validate_file()
  {
    $max_size = 5000;             // maximum file size, in KiloBytes
    $alwidth = 900;               // maximum allowed width, in pixels
    $alheight = 800;              // maximum allowed height, in pixels
    // check for uploaded file size
    if ($this->size > $max_size)
    {
      return FALSE;
    }

    //check if its image file
    if (!getimagesize($this->temp_path))
    {
      return FALSE;
    }

    // restrict width and height if its image or photo file
    list($width, $height) = getimagesize($this->temp_path);

    if ($width > $alwidth || $height > $alheight)
    {
      return FALSE;
    }

    return TRUE;
  }

// Save file to db
}