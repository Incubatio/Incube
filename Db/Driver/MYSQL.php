<?php
namespace Incube\Db\Driver;
/** @author incubatio 
  * @depandancy Incube_SQL_Query
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  *
  * TODO: Implements Mysql particularities
  * TOTHINK: Put Connection into a separate Object e.g. Mysql/Connection
  */

use \Incube\Db\PdoConnection,
    Pdo;

class MYSQL extends \Incube\SQL\Query {

    const DELETE    = "DELETE FROM <tables> <where>";
    const DROP      = "DROP <type> <name>";
    const INSERT    = "INSERT <delayed> <ignore> INTO <tables> VALUES <data>";
    const SELECT    = "SELECT <delayed> <cols> FROM <tables> <where> <groupby> <having> <orderby> <limit>";
    const UPDATE    = "UPDATE <tables> SET <data> <where>";
    const SHOW      = "SHOW <cols> FROM <tables>";
    const DESCRIBE  = "DESCRIBE <name>";


    /** @var string
     * Default security is addslashes just in case */
    protected $_secure_function = "PDO::quote";

    /** @var connection */
    protected $_connection;

    /** @param array options */
    public function __construct(PdoConnection $connection, $options = array()) {
        parent::__construct();
        $this->_connection = $connection;
        //$this->init($options);
    }

	/** @param string $db */
    public function set_db($db) {
        $this->_db = $db;
        return $this;
    }

	/** @param string $connection */
    public function get_connection() {
        return  $this->_connection;
    }

	/** @param string $connection */
    public function set_connection($connection) {
        $this->_connection = $connection;
        return $this;
    }

    /** @param array $options */
    public function init(array $options) {
        foreach($options as $key => $option) {
            $this->{"_$key"} = $option;
        }
    }

    /** Execute the mysql query
	  * @param string $query
      * @return mysql_query result */
    public function execute($query = null) {
      //TODO: generate $this->$QUERYPARAMS from $query parse  ...
      //if(!empty($this->_query) && isset($query)) {
      // $query          = $this->generateQuery($query);
      //}

      $this->_host .= empty($this->_port) ? "" : ":$this->_port" ;

      //TODO: Should I put the content below inside a function ?
      $is_affect_row = false;
      if(isset($this->_query->action)) {
        if(in_array($this->_query->action, array("insert", "update", "delete"))) {
          $is_affect_row = true;
        }
        if($this->_query->action == "insert") {
          $_query         = clone $this->_query;
          $this->_query   = new \StdClass();
          $cols = $this->get_column_names($_query->tables);
          //                        $cols = array_fill_keys($cols, '');
          $cols = array_combine($cols,array_fill(0,count($cols), ''));
          foreach($_query->data as $key => $data) {
            //Remove unamed and bad named fields
            $data = array_intersect_key($data, $cols);
            //Merge keys wit value, allow disorder in data
            $_query->data[$key] = array_merge($cols, $data);
          }
          $this->_query = $_query;
        }
        $query = $this->getQuery();
      }


      $db = $this->_connection->get_instance();
      try {
          $result = $db->query($query);
      } catch(PDOException $e) {
          trigger_error('An error occured' . $e->getMessage(), E_USER_WARNING);
      }

        return $is_affect_row ? $ $db->rowCount() > 0 : $result;
    }


    /** Return the result of a request under an associative array
      * @param Mysql_Result $result
      * @return Array */
    public function fetch_array($result = null) {
        if(empty($result)) $result = $this->execute();
        $rows = array();
        if($result) {
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        }
        return $rows;
    }

    /** Return the result of a request under an associative array
      * @param string $key
	  * @param Mysql_result $result
      * @return Array*/
    public function fetch_array_by($key, $result = null) {
        if(empty($result)) {
            $result = $this->execute();
            die;
        }
        $rows = array();
        if($result) {
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $k = $row[$key];
                unset($row[$key]);
                $rows[$k] = $row;
            }
        }
        return $rows;
    }


    /** Return only one column from the query
      * @param <int||string> $col
      * @return array */
    public function fetch_column($col = 0, $result = null) {
        if(empty($result)) $result = $this->execute();
        if($result) {
            $rows = $result->fetchAll(PDO::FETCH_COLUMN, $col); 
        }

        /* if(is_int($col)) {
           $row = array_values($row);
           }*/
        return $rows;
    }

    /** Return only one column from the query
      * @param <int||string> $key
      * @return array */
    public function fetch_column_by($key, $result = null) {
        $rows = $this->fetch_array_by($key);
        foreach($rows as $key => $row) {
            $rows[$key] = $row[key($row)];
        }
        return $rows;
    }

    /** @return array */
    public function fetch_one($result = null) {
        if(empty($result)) $result = $this->execute();
        return $result ? $result->fetch(PDO::FETCH_ASSOC) : $result;
    }


   /** Ancient version of insert, keeped to code next insert with mysql specificities
     * TODO:delete this function ASAP
     */
    public function insert2($table, $values, array $options = array()) {
        foreach($values as $key => $value) {
            if (is_string($value)){
                $values[$key] = '"' . $value . '"';
            }
        }
        $query  = "INSERT ";
        $query .= "DELAYED ";
        if(in_array("ignore", $options)) {
            $query .= "IGNORE ";
        }
        $query .= "INTO $table VALUES(" . implode(',', $values) . ")";

        if(in_array("onDuplicateUpdate", $options)) {
            $query .= " ON DUPLICATE KEY UPDATE ";
            $values=array_values($values);
            $cols = $this->getColumnNames($table);
            $updates = array();
            foreach($cols as $key => $col) {
                if(array_key_exists($key, $values)) {
                    $updates[$col] = "$col = ". $values[$key];
                }
            }
            unset($updates['id']);
            $query .= implode(',',$updates);
        }
        $this->_query = $query;
        if(!$this->execute()) {
            throw new Incube_Db_Exception("Insertion Failed");
        }
    }

   /** @param string $table
     * @return array */
    public function getColumnNames($table) {
        /* DONT WORK ON old PHP
        //$this->_query = "select * from information_schema.tables where table_name='$table'";
        //$this->_query = "SELECT * FROM information_schema";
        $this->getInformationsFromInformationSchema("COLUMN_NAME", "columns")->where("table_name", "postits");
        $results = $this->fetch_array();
        foreach($results as $key => $result) {
        $columns[$key] = $result["COLUMN_NAME"];
        } */
        //$query = new Incube_SQL_Query();
        $this->show('COLUMNS')->from($table);
        //$query = $this->getQuery();
        //$result = $this->execute($query);
        $results = $this->fetch_array();
        foreach($results as $key => $column) {
            $columns[$key] = $column["Field"];
        }
        return $columns;
    }

   /** @param string $selection
     * @param string $table
     * @return array */
    public function getInformationsFromColumns($selection, $table) {
        //$this->select($selection, "information_schema.columns")
        //$this->_query = "select * from information_schema.columns where table_name='$table'";
        $this->getInformationsFromInformationSchema("*")->from("tables")->where(array("table_name", "postits"));
        var_dump($this->_query);die;
        return $this->fetch_array();
    }

   /** @param string $selection
     * @param string $table */
    protected function getInformationsFromInformationSchema($selection, $object) {
        //$this->select($selection, "information_schema.columns")
        $this->select($selection)->from("information_schema.$object");
        return $this;
    }
}
