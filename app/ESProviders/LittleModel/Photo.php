<?php

namespace ESProviders\LittleModel;

class Photo extends BaseModel
{

  /**
   * @var type string
   */
  protected $_tableName = 'photos';

  public function getLastAdd($limit = 3)
  {
    $sql = "SELECT id FROM {$this->_tableName} ORDER BY id DESC LIMIT {$limit}";
    $statement = $this->conn->prepare($sql);
    $statement->execute();
    $result = $statement->fetchAll();
    
    return $result;
  }

}
