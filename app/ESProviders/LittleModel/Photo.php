<?php

namespace LittleModel;

use \Doctrine\DBAL\Connection;

class Photo extends BaseModel
{

  /**
   * @var type string
   */
  protected $_tableName = 'photos';

  public function __construct($conn)
  {
    $this->conn = $conn;
  }

}