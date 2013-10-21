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

  public function get_last_id()
  {
    // fetch last photo id from database
    $sql = "SELECT id FROM {$this->_tableName} ORDER BY id DESC LIMIT 1";
    $statement = $this->conn->prepare($sql);
    $statement->execute();
    $result = $statement->fetch();

    return (string) $result['id'];
  }

}