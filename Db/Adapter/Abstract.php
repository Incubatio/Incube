<?php
/** @author incubatio
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_Db_Adapter_Abstract {

        /** @var Mysql ID */
        protected $_connection;

        /** @var string */
        protected $_db;

        /** @var string */
        protected $host;

        /** @var string */
        protected $port;

        /** @var string */
        protected $login;
        
        /** @var string */
        protected $password;

        protected $_data = array();

        public function close() {
            
        }
    
    }
