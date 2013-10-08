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
  
  /**
   * Rules for validation in forms
   * @var type mixed
   */
  public $rules = array(
      'title' => 'required',
      'body'  => 'required'
  );

  public function __construct(Connection $conn)
  {
    $this->conn = $conn;
  }

}