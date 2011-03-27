<?php
/** @author incubatio 
  * @depandancies Incube_Db_Adapter_Abstract, Incube_Db_Driver_CouchDb
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_Db_Adapter_CouchDb extends Incube_Db_Adapter_Abstract {


	/** @param array $options */
	public function __construct($options = array()) {
		$this->init($options);
	}

	/** @param array $options */
	public function init($options) {
		if (empty($this->_driver)) {
			$this->_driver = new Incube_Db_Driver_CouchDb();
		}
		$this->_driver->init($options);
	}

	/** @param mixed $data
		@param string $id
		return Incube_Db_Adapter_CouchDb */
	 public function create($data, $id = null) {
		 if(empty($id)) {
			$this->_driver->post($data);
		 } else {
			 $this->_driver->put($id, $data);
		 }
		 
		 return $this;
	 }
	 
	/** @param mixed $items
		return Incube_Db_Adapter_CouchDb */
	 public function retreive($items) {
		$this->selection = $items;
		$this->_driver->get();
		return $this;
	 }

	/** @param mixed $data
		@param string $id
		return Incube_Db_Adapter_CouchDb */
	 public function update($data) {
		 $this->_driver->put(null, $data);
		 return $this;
	 }

	/** @param string $item
		return Incube_Db_Adapter_CouchDb */
	 public function delete($item) {
		$this->_driver->delete($item);
		return $this;
	 }

	/** @param string $dataType
		return Incube_Db_Adapter_CouchDb */
	 public function from($dataType) {
		$this->_driver->in($dataType);
		return $this;
	 }


	/** @param array $where
		return Incube_Db_Adapter_CouchDb */
	 public function where($where = array()) {
		 $this->_where = $where;
		 if(array_key_exists("id", $where)) {
			 $this->_driver->setId($where["id"]);
		 }
		 return $this;
	 }

	/** return ???? */
	 public function execute() {
		$id = $this->_driver->getId();
		if( $id == "*" OR !isset($id)) {
			$this->_driver->setId("_all_docs");
		}
		return $this->_driver->execute();
	 }

	/** return array */
	 public function fetchArray() {
		 $response = $this->execute();
		 $res = array();
		 if(!empty($this->selection)) {
			foreach(explode(',', $this->selection) as $key) {
				foreach($response["rows"] as $num => $row) {
					$res[$num][$key] = array_key_exists($key, $row) ? $row[$key] : "unexistant field:$key";
				}
			}
		 } else {
			 $res = $response["rows"];
		 }
		 return $res;
	 }

	/** return array */
	 public function fetchOne() {
		 $response = $this->execute();
		 Incube_debug::dump($response);die;
		 return $response;
	 }

	 //getAll()->from($support)->make();
	 //get("id")->from($support)->where(array("id",'1'))->make();


	}
