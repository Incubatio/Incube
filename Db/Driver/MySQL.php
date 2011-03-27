<?php
/** @author incubatio 
  * @depandancy Incube_SQL_Query
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  *
  * TODO: Implements Mysql particularities
  * TOTHINK: Put Connection into a separate Object e.g. Mysql/Connection
  */
class Incube_Db_Driver_MySQL extends Incube_SQL_Query {

    const DELETE    = "DELETE FROM <tables> <where>";
    const DROP      = "DROP <type> <name>";
    const INSERT    = "INSERT <delayed> <ignore> INTO <tables> VALUES <data>";
    const SELECT    = "SELECT <delayed> <cols> FROM <tables> <where> <groupby> <having> <orderby> <limit>";
    const UPDATE    = "UPDATE <tables> SET <data> <where>";
    const SHOW      = "SHOW <cols> FROM <tables>";
    const DESCRIBE  = "DESCRIBE <name>";


    /** @var string
     * Default security is addslashes just in case */
    protected $_secureFunction = "mysql_real_escape_string";

    /** @var MysqlID */
    protected $_connection;

    /** @var string */
    protected $_host = "localhost";

    /** @var string */
    protected $_login = "root";

    /** @var string */
    protected $_password = "";

    /** @var string */
    protected $_db;

    /** @param array options */
    public function __construct($options = array()) {
        parent::__construct();
        //$this->init($options);
    }

	/** @param string $db */
    public function setDb($db) {
        $this->_db = $db;
    }

    /** @param array $options */
    public function init(array $options) {
        foreach($options as $key => $option) {
            $this->{"_$key"} = $option;
        }
    }

    /** @param string $host
      * @param string $login
      * @param string $password 
	  * @param string db */
    public function initConnection($host="localhost", $login="root", $password="", $db = null) {
        if(!$this->_connection = mysql_connect($host, $login, $password)) {
            throw new Incube_Db_Exception("connection to database at $host with $login FAILED");
        }
        if (!is_null($db)) {
            $this->selectDb($db);
        }
    }

    public function selectDb($db) {
        mysql_select_db($db);
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
		$this->initConnection($this->_host, $this->_login, $this->_password, $this->_db);

		//TODO: Should I put the content below inside a function ?
        $isAffectRow = false;
        if(isset($this->_query->action)) {
            if(in_array($this->_query->action, array("insert", "update", "delete"))) {
                $isAffectRow = true;
            }
            if($this->_query->action == "insert") {
                $_query         = clone $this->_query;
                $this->_query   = new StdClass();
                $cols = $this->getColumnNames($_query->tables);
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


		//if(isset($query))
		//Incube_Debug::dump($query);

		if(empty($this->_connection)) {
			$this->_host .= empty($this->_port) ? "" : ":$this->_port" ;
			$this->initConnection($this->_host, $this->_login, $this->_password, $this->_db);
		}
		$result = mysql_query($query, $this->_connection);

		//TODO:review the if
		//If query failed trigger error
		if(!$result && mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);

		//If the query is not getting data, it will return if the action performed affected row or not
        return $isAffectRow ? mysql_affected_rows() > 0 : $result;
    }


    /** Return the result of a request under an associative array
      * @param Mysql_Result $result
      * @return Array */
    public function fetchArray($result = null) {
        if(empty($result)) {
            $result = $this->execute();
        }
        $rows = array();
        if($result) {
            while($row = mysql_fetch_assoc($result))
                $rows[] = $row;
        }
        return $rows;
    }

    /** Return the result of a request under an associative array
      * @param string $key
	  * @param Mysql_result $result
      * @return Array*/
    public function fetchArrayBy($key, $result = null) {
        if(empty($result)) {
            $result = $this->execute();
        }
        $rows = array();
        if($result) {
            while($row = mysql_fetch_assoc($result)) {
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
    public function fetchColumn($col = 0) {
        $result = $this->execute();
        if($result) {
            while($row = mysql_fetch_row($result)) {
                $rows[] = $row[$col];
            }
        }

        /* if(is_int($col)) {
           $row = array_values($row);
           }*/
        return $rows;
    }

    public function fetchResult() {
        $result = $this->execute();
        if($result) {
            while($row = mysql_fetch_row($result)) {
                $rows[] = $row[0];
            }
        }

        /* if(is_int($col)) {
           $row = array_values($row);
           }*/
        return $rows[key($rows)];
    }
    /** Return only one column from the query
      * @param <int||string> $key
      * @return array */
    public function fetchColumnBy($key) {
        $rows = $this->fetchArrayBy($key);
        foreach($rows as $key => $row) {
            $rows[$key] = $row[key($row)];
        }
        return $rows;
    }

    /** @return array */
    public function fetchOne() {
        $result = $this->execute();
        return $result ? mysql_fetch_assoc($result) : $result;
    }

    public function close() {
        if(!empty($this->_connection)) {
            mysql_close($this->_connection);
            $this->_connect = NULL;
        }
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
                    $updates[$col] = "$col = $values[$key]";
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
        $results = $this->fetchArray();
        foreach($results as $key => $result) {
        $columns[$key] = $result["COLUMN_NAME"];
        } */
        //$query = new Incube_SQL_Query();
        $this->show('COLUMNS')->from($table);
        //$query = $this->getQuery();
        //$result = $this->execute($query);
        $results = $this->fetchArray();
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
        return $this->fetchArray();
    }

   /** @param string $selection
     * @param string $table */
    protected function getInformationsFromInformationSchema($selection, $object) {
        //$this->select($selection, "information_schema.columns")
        $this->select($selection)->from("information_schema.$object");
        return $this;
    }
}
