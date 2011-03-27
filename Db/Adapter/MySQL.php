<?php
/** @author incubatio
  * @depandancies Incube_Db_Adapter_Abstract, Incube_Db_Driver_MySQL
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_Db_Adapter_MySQL extends Incube_Db_Adapter_Abstract {

        public function __construct($options = array()) {
            $this->init($options);
        }
		/** TODO: check if the method below can be used 
		  * BEGIN BLOCK */
		public function __call($method, $params) {
			$this->_lastMethod = $method;
			call_user_method_array(array($this, $method), $params);
		}

		public function or(array $params); 
		public function and(array $params); 
		/** End Block */

        public function init($options) {
            if (empty($this->_driver)) {
                $this->_driver = new Incube_Db_Driver_MySQL();
            }
            $this->_driver->init($options);
        }

         public function create($data) {
             $this->_driver->insert($data);
             return $this;
         }

         public function count() {
            $this->_driver->count();
            return $this;
         }

         public function retreive($item) {
            $this->_driver->select($item);
            return $this;
         }

         public function update($data) {
             $this->_driver->update($data);
             return $this;
         }

         public function delete() {
            $this->_driver->delete();
            return $this;
         }

         public function from($dataType) {
            $this->_driver->from($dataType);
            return $this;
         }


         public function where(array $wheres) {
	    //TODO: manage AND, OR for where clause
			foreach($wheres["equals"] as $column => $value) $where[] = "$column = \"$value\"";
			foreach($wheres["not"] as $column => $value) $where[] = "$column != \"$value\"";
			$where = implode(" AND ", $wheres);
			die("need to be finished");
             $this->_driver->where($where);
             return $this;
         }

         public function execute() {
            return $this->_driver->execute();
         }

         public function fetchArray() {
            return $this->_driver->fetchArray();
         }

         public function fetchArrayBy($key) {
            return $this->_driver->fetchArrayBy($key);
         }

         public function fetchColumn() {
            return $this->_driver->fetchColumn();
         }

         public function fetchColumnBy($key) {
            return $this->_driver->fetchColumnBy($key);
         }

         public function fetchResult() {
            return $this->_driver->fetchResult();
         }
         public function fetchOne() {
             $response = $this->fetchArray();
             return empty($response[0]) ? NULL : $response[0];
         }

         public function getQuery() {
             return $this->_driver->getQuery();
         }

         public function orderby($item, $order = "") {
             return $this->_driver->orderby($item, $order);
         }

        public function setDb($db) {
            $this->_driver->setDb($db);
            return $this;
        }

         //getAll()->from($support)->make();
         //get("id")->from($support)->where(array("id",'1'))->make();


    }
