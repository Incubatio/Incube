<?php
/** @author incubatio
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_SQL_Query {

	const DELETE    = "DELETE FROM <tables> <where>";
	const DROP      = "DROP <type> <name>";
	const INSERT    = "INSERT INTO <tables> VALUES <data>";
	const SELECT    = "SELECT <cols> FROM <tables> <where> <groupby> <having> <orderby> <limit>";
	const UPDATE    = "UPDATE <tables> SET <data> <where>";
	const SHOW      = "SHOW <cols> FROM <tables>";
	const DESCRIBE  = "DESCRIBE <name>";
	const CREATE	= "CREATE TABLE <tables> VALUES(<data>)<engine>";

	/** @var StdClass */
	protected $_query;


	/** @var array */
	protected $_schema = array(
			"delete"    => self::DELETE,
			"drop"      => self::DROP,
			"insert"    => self::INSERT,
			"select"    => self::SELECT,
			"update"    => self::UPDATE,
			"show"      => self::SHOW,
			"describe"  => self::DESCRIBE,
			);

	/** @var array */
	protected $_attrs = array(
			"where"     => "WHERE",
			"groupby"   => "GROUP BY",
			"having"    => "HAVING",
			"orderby"   => "ORDER BY",
			"limit"     => "LIMIT",
			);

	/** @var string
	  * Default security is addslashes just in case */
	protected $_secureFunction = "addslashes";

	/** Contains the list of the attributes which need to be escaped to avoid SQL injection, 
	  * Example of escaped attribute: $this->_query->where should store the params for secureFunction
	  * @var array */
	protected $_nonSecureAttrList = array("where", "having");

	public function __construct() {
		$this->_query = new StdClass;
	}

	/** @return Incube_SQL_Query */
	public function delete() {
		$this->_query->action = "delete";
		return $this;
	}

	/** @param string $type
	  * @param string $name
	  * @return Incube_SQL_Query */
	public function drop($type, $name) {
		//$type= TABLE/columns/VIEW/INDEX/PK
		$this->_query->action = 'drop';
		$this->_query->type = $type;
		$this->_query->name = $name;
		return $this;
	}

	/** @param string $type
	  * @param string $name
	  * @return array */
	public function describe($type, $name) {
		//$this->_query = "SHOW COLUMNS FROM `$table`";
		$this->_query->action = 'describe';
		$this->_query->type = $type;
		$this->_query->name = $name;
		return $this->fetchArray();
	}

	/** @param array $data
	  * @param array $options 
	  * @return Incube_SQL_Query */
	public function insert(array $data, array $options = array()) {
		$this->_query->action = 'insert';
		$this->_query->data   = $data;
		if(!is_array($this->_query->data[key($this->_query->data)])) {
			$this->_query->data = array($this->_query->data); 
		}
		return $this;
	}

	/** @param mixed $selection 
	  * @return Incube_SQL_Query */
	public function select($selection) {
		$this->_query->action = "select";
		if(is_array($selection)) {
			$this->_query->cols = implode(',', $selection);
		} else {
			$this->_query->cols = $selection;
		}
		return $this;
	}

	/** @param mixed $items 
	  * @return Incube_SQL_Query */
	public function show($items) {
		$this->_query->action = "show";
		if(is_array($items)) {
			$this->_query->cols = implode(',', $items);
		} else {
			$this->_query->cols = $items;
		}
		return $this;
	}


	/** @param array $data 
	  * @return Incube_SQL_Query */
	public function update(array $data) {
		$this->_query->action = "update";
		$this->_query->data = $data;
		if(!is_array($this->_query->data[key($this->_query->data)])) {
			$this->_query->data = array($this->_query->data); 
		}
		return $this;
	}

	/** @param mixed $tables 
	  * @return Incube_SQL_Query */
	public function from($tables) {
		if(is_array($tables)) {
			$this->_query->tables = implode(', ', $tables);
		} else {
			$this->_query->tables = $tables;
		}
		return $this;
	}

	/** @param int $pos
	  * @param int $number 
	  * @return Incube_SQL_Query */
	public function limit($pos, $number) {
		$this->_query->limit = "$pos, $number";
		return $this;
	}

	/** @param string $item
	  * @param int $order 
	  * @return Incube_SQL_Query */
	public function orderBy($item, $order ="") {
		$this->_query->orderby = "$item $order";
		return $this;
	}

	/** @return Incube_SQL_Query */
	public function count() {
		$this->select('count(*)');
		return $this;
	}



	/** select need <cols>
	  * insert need <data>
	  * update need <data>
	  * drop need <type>
	  * describe need
	  * show need <cols> 
	  * @return mixed */
	public function getQuery() {

		// Format data on an insert/updata
		if(empty($this->_query->action)) throw new Incube_Exception("Action not initialized");

		if(!empty($this->_query->data)) $this->_prepareData();

		$schema = $this->_schema[$this->_query->action];
		preg_match_all("/[a-z]+/", $schema, $params);
		foreach($params[0] as $param) {
			if(!empty($this->_query->$param)) {
				// Check if the param is an attribute with inputable value

				if (array_key_exists($param, $this->_attrs)) {
					// Check if we need to secure the param before using it
					if(in_array($param, $this->_nonSecureAttrList)) {
						$this->_query->$param = call_user_func_array(array($this, "_secure"), $this->_query->$param);
					}
					$this->_query->$param = $this->_attrs[$param]." ".$this->_query->$param;
				} else {
					//trigger_error("$param is not part of the " . $this->_query->action . " template query and has been skipped", E_USER_WARNING);
				}
				$schema = str_replace("<$param>", $this->_query->$param, $schema);
			} else {
				$schema = str_replace("<$param>", "", $schema);
			}

		}
		$this->_lastQuery = $schema;
		$this->_query = new StdClass;

		return trim($schema);
	}


	protected function _prepareData() {
		foreach($this->_query->data as $i => $data) {
			if($this->_query->action == "insert") {
				foreach($data as $key => $value) {
					$arguments[$key] = '"' . $value . '"';
				}
				$this->_query->data[$i] = "(" . implode(', ', $arguments) . ")";
			} else {
				foreach($data as $key => $value) {
					$arguments[] = "$key = \"$value\"";
				}
				$this->_query->data[$i] = implode(', ', $arguments);
			}
		}
		$this->_query->data = implode(', ', $this->_query->data);
	}


	/** @param string $where
	  * @param mixed $values
	  * where("id != :value", $value) 
	  * TODO: add $safe boolean to allow nested query ? */
	public function where($where, $values) {
		//$this->_query->where = $this->_secure($where, $values);
		$this->_query->where = array($where, $values);
		return $this;
	}

	/** @param string $string
	  * @param mixed $params 
	  * @return string */
	protected function _secure($string, $params) {
		$secure = $this->_secureFunction;
		if(is_array($params)) {
			foreach($params as $key => $param) {
				$param = $secure($param);
				$string = preg_replace("#:" . $key . "#", '"' . $param . '"', $string);
			}
		} else {
			$params = $secure($params);
			$string = preg_replace("#:[\w]+#", '"' . $params . '"', $string);
		}
		return $string;

	}

	/** @param mixed $cols 
	  * @return Incube_SQL_Query */
	public function groupby($cols) {
		if(!is_array($cols)){
			$cols=array($cols);
		}
		$this->_query->groupby = implode(",", $cols);
		return $this;
	}

	/** @param string $having
	  * @param mixed $values
	  * having("id != :value", $value) 
	  * @return Incube_SQL_Query */
	public function having($having, $params) {
		/*if(empty($this->groupby)) {
		  throw new Incube_Db_Exception("Having needs a groupby");
		  }*/
		//foreach($havings as $column => $value) {
		//$having[] = "$column = \"$value\"";
		//}
		//$this->_query->having = implode(" AND ", $having);

		//$this->_query->having = $this->_secure($having, $params);
		$this->_query->having = array($having, $params);
		return $this;
	}
	/*
	   Pour les join on va faire avec des where on verra apres sinon
	   public function join($joins){
	   foreach($joins as $table1 => $table2) {
	   $table1_name = explode(".", $table1)[0];
	   $table2_name = explode(".", $table2)[0];
	   $join[] = "JOIN ";
	   }
	   }*/

}

?>
