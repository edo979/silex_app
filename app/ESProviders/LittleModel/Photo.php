<?php

namespace ESProviders\LittleModel;

class Photo extends BaseModel
{

  /**
   * @var type string
   */
  protected $_tableName = 'photos';
  
//  public function save($file)
//  {
//    // get id from database
//    // make new name for photo using id
//    // move photo to destination folder and rename
//    // save path and name to database
//  }
  
  private function get_last_id()
  {
    // fetch last photo id from database
  }
  
  private function move_photo($file)
  {
    // move file and rename
  }

}