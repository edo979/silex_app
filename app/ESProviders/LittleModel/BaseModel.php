<?php

namespace ESProviders\LittleModel;

use \Doctrine\DBAL\Connection;

abstract class BaseModel
{

  /**
   * @var type string
   */
  protected $_tableName = '';

  /**
   * @var type string
   */
  protected $_primaryKey = 'id';

  /**
   * Used to filter value to intval for security
   * @var type string
   */
  protected $_primaryFilter = 'intval';

  /**
   * @var type string
   */
  protected $_orderBy = '';

  /**
   * @var type bool
   */
  protected $_timestamps = FALSE;

  /**
   * Doctrine database connection
   * @var object
   */
  protected $conn;

  public function __construct(Connection $conn)
  {
    $this->conn = $conn;
  }

  public function get($id = NULL)
  {
    if ($id != NULL)
    {
      $filter = $this->_primaryFilter;
      $id = $filter($id);

      $sql = "SELECT * FROM {$this->_tableName} WHERE {$this->_primaryKey} = ?";
      $result = $this->conn->fetchAssoc($sql, array($id));
    }
    else
    {
      $sql = "SELECT * FROM {$this->_tableName}";
      $statement = $this->conn->prepare($sql);
      $statement->execute();
      $result = $statement->fetchAll();
    }

    return $result;
  }

  public function get_by($where, $single = FALSE)
  {
    
  }
  
  public function get_last_id()
  {
    // fetch last id from database
    $sql = "SELECT id FROM {$this->_tableName} ORDER BY id DESC LIMIT 1";
    $statement = $this->conn->prepare($sql);
    $statement->execute();
    $result = $statement->fetch();

    return (string) $result['id'];
  }

  public function save($data, $id = NULL)
  {
    // Set timestamps
    if ($this->_timestamps == TRUE)
    {
      $now = date('Y-m-d H:i:s');
      $id || $data['created'] = $now;
      $data['modified'] = $now;
    }

    if ($id != NULL)
    {
      // Update
      $filter = $this->_primaryFilter;
      $id = $filter($id);

      return $this->conn
          ->update($this->_tableName, $data, array($this->_primaryKey => $id));
    }
    else
    {
      // Save
      $this->conn
          ->insert($this->_tableName, $data);
      
      return $this->conn->lastInsertId();
    }
  }

  public function delete($id)
  {
    $filter = $this->_primaryFilter;
    $id = $filter($id);

    return $this->conn
        ->delete($this->_tableName, array($this->_primaryKey => $id));
    // DELETE FROM user WHERE id = ? (1)
  }

}