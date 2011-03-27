<?php
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_Db_Driver_Abstract {

        /** @var Object */
        protected $_query;

        /** @var Mysql ID */
        protected $_connection;

        /** @var string */
        protected $_db;

        /** @var string */
        protected $_host;

        /** @var string */
        protected $_port;

        /** @var string */
        protected $_login;
        
        /** @var string */
        protected $_password;

        public function __construct() {
        }

        /** @param array $host */
        public function init(array $options) {
            foreach($options as $key => $option) {
                $key = "_" . $key;
                $this->$key = $option;
            }
        }

    }
