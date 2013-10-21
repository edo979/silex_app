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

    $app['photoHandler'] = function() use($app)
      {
        $file = $app['photoHandler.temp_file'];

        $validate = $this->validate_file($file);
        $app['photoHandler.errors'] = $this->errors;

        if ($validate)
        {
          // Set image attribute from upload file.
          $app['photoHandler.image'] = array(
              'filename'  => basename($file['name']),
              'temp_path' => $file['tmp_name']
          );

          return $this->move_image($file);
        }

        return FALSE;
      };
  }

  // Validate file
  private function validate_file($file)
  {
    $upload_path = 'upload/';
    $max_size = 3 * 1024 * 1024;             // maximum file size, in Mb
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
      $this->errors[] = 'File is to large';
      return FALSE;
    }

    // restrict width and height if its image or photo file
    list($width, $height) = getimagesize($file['tmp_name']);

    //check if its image file
    if (!isset($width))
    {
      $this->errors[] = 'Not suported format';
      return FALSE;
    }

    if ($width > $alwidth || $height > $alheight)
    {
      $this->errors[] = 'Image size not correct';
      return FALSE;
    }

    return TRUE;
  }

  private function move_image()
  {
    /**
     * upload.php
     *
     * Copyright 2013, Moxiecode Systems AB
     * Released under GPL License.
     *
     * License: http://www.plupload.com/license
     * Contributing: http://www.plupload.com/contributing
     */
    // Make sure file is not cached (as it happens for example on iOS devices)
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    /*
      // Support CORS
      header("Access-Control-Allow-Origin: *");
      // other CORS headers if any...
      if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
      exit; // finish preflight CORS requests here
      }
     */

    // 5 minutes execution time
    @set_time_limit(5 * 60);

    // Uncomment this one to fake upload time
    // usleep(5000);
    // Settings
    //$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
    $targetDir = '../app/uploads';
    $cleanupTargetDir = true; // Remove old files
    $maxFileAge = 5 * 3600; // Temp file age in seconds
    // Create target dir
    if (!file_exists($targetDir))
    {
      @mkdir($targetDir);
    }

    // Get a file name and replece with alfanum caracters
    if (isset($_REQUEST["name"]))
    {
      $fileName = preg_replace('#[^A-Za-z0-9.]#', '', $_REQUEST["name"]);
    }
    elseif (!empty($_FILES))
    {
      $fileName = preg_replace('#[^A-Za-z0-9.]#', '', $_FILES["file"]["name"]);
    }
    else
    {
      $fileName = preg_replace('#[^A-Za-z0-9-./]#', '', uniqid("file_"));
    }
    
    // get last id from db
    $last_id = $this->_model->get_last_id();
    $new_id = (int) $last_id + 1;
    $new_id = (string) $new_id;
    // get extension
    $fileName_extn = substr($fileName, strrpos($fileName, '.')+1);
    // set file name to last id from db
    $fileName = 'image' . $new_id . '.' . $fileName_extn;
    
    $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

    // Chunking might be enabled
    $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
    $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;


    // Remove old temp files	
    if ($cleanupTargetDir)
    {
      if (!is_dir($targetDir) || !$dir = opendir($targetDir))
      {
        die('{
          "jsonrpc" : "2.0",
          "error" : {"code": 100, "message": "Failed to open temp directory."}
         }');
      }

      while (($file = readdir($dir)) !== false)
      {
        $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

        // If temp file is current file proceed to the next
        if ($tmpfilePath == "{$filePath}.part")
        {
          continue;
        }

        // Remove temp file if it is older than the max age and is not the current file
        if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge))
        {
          @unlink($tmpfilePath);
        }
      }
      closedir($dir);
    }


    // Open temp file
    if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb"))
    {
      die('{
        "jsonrpc" : "2.0",
        "error" : {"code": 102, "message": "Failed to open output stream."}
       }');
    }

    if (!empty($_FILES))
    {
      if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"]))
      {
        die('{
          "jsonrpc" : "2.0",
          "error" : {"code": 103, "message": "Failed to move uploaded file."}
         }');
      }

      // Read binary input stream and append it to temp file
      if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb"))
      {
        die('{
          "jsonrpc" : "2.0",
          "error" : {"code": 101, "message": "Failed to open input stream."}
         }');
      }
    }
    else
    {
      if (!$in = @fopen("php://input", "rb"))
      {
        die('{
          "jsonrpc" : "2.0",
          "error" : {"code": 101, "message": "Failed to open input stream."}
         }');
      }
    }

    while ($buff = fread($in, 4096))
    {
      fwrite($out, $buff);
    }

    @fclose($out);
    @fclose($in);

    // Check if file has been uploaded
    if (!$chunks || $chunk == $chunks - 1)
    {
      // Strip the temp .part suffix off 
      rename("{$filePath}.part", $filePath);
    }

    // Return Success JSON-RPC response
    return $fileName;
  }

}