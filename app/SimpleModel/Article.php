<?php

namespace SimpleModel;

use \Doctrine\DBAL\Connection;

class Article extends BaseModel
{

  /**
   * @var type string
   */
  protected $_tableName = 'articles';

  /**
   * @var type bool
   */
  protected $_timestamps = TRUE;

  public function __construct(Connection $conn)
  {
    $this->conn = $conn;
  }

}