<?php

namespace ESProviders;

use Silex\Application;
use Silex\ServiceProviderInterface;

class LittlePhotoServiceProvider implements ServiceProviderInterface
{
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
  private $_model;

  public function boot(Application $app)
  {
    
  }

  public function register(Application $app)
  {
    // Set model to store images
    $this->_model = $app['model.photo'];
    
    $app['photoHandler'] = $app->protect(function($file) use($app)
      {
        if ($this->validate_file($file))
        {
          $this->save_file($file);
        }
        
        return FALSE;
      });

    $app['photoHandler.errors'] = $this->errors;
  }

  // Validate file
  private function validate_file($file)
  {
    $upload_path = 'upload/';
    $max_size = 5000;             // maximum file size, in KiloBytes
    $alwidth = 900;               // maximum allowed width, in pixels
    $alheight = 800;              // maximum allowed height, in pixels
    // Perform error checking on the form parameters
    if (!$file || empty($file) || !is_array($file))
    {
      // error: nothing uploaded or wrong argument usage
      $this->errors[] = "No file was uploaded.";
      return FALSE;
    }
    elseif ($file['error'] != 0)
    {
      // error: report what PHP says went wrong
      $this->errors[] = $this->upload_errors[$file['error']];
      return FALSE;
    }

    // check for uploaded file size
    if ($file['size'] > $max_size)
    {
      return FALSE;
    }

    //check if its image file
    if (!getimagesize($file['tmp_name']))
    {
      return FALSE;
    }

    // restrict width and height if its image or photo file
    list($width, $height) = getimagesize($file['tmp_name']);

    if ($width > $alwidth || $height > $alheight)
    {
      return FALSE;
    }

    return TRUE;
  }

  // Save file to db
  private function save_file($file)
  {
    // Validate file
    if (!$this->validate_file($file))
    {
      return FALSE;
    }

    // Set image attribute from upload file.
    $data = array(
        'filename'  => basename($file['name']),
        'temp_path' => $file['tmp_name']
    );
    
    // Ready to save
    $this->_model->save($data);
  }

}