<?php
namespace Incube\Db; 
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */

use Pdo;

class PdoConnection {
    protected $_instance;
    protected $_type;
    protected $_params;
    protected $_options;

    protected $_default_params = array(
            'host' => 'localhost',
            'user' => 'root',
            );
 
    public function __construct($type, array $params = array(), array $options = array()) {
        $this->_type    = strtolower($type);
        $this->_params  = $params;
        $this->_options = $options;
    }

    protected function _init() {

        $params = $this->_params;
        // Remap params if necessary
        $remap_keys = array(
                'host' => 'hostname',
                'user' => 'username',
                'dbname' => 'db',
                'dbname' => 'database',
                'dsn'  => 'connection_string',
                );

        foreach($remap_keys as $new_key => $old_key) { 
            if(array_key_exists($old_key, $this->_params)) $this->_params[$new_key] = $this->_params[$old_key];
        }

        if(array_key_exists('dsn', $params)) $connection_string = $params['dsn'];
        else {
            $param_keys = array('host', 'dbname', 'port');
            $connection_string_params = array();
            foreach( $param_keys as $key) {
                $value = $this->get_param($key);
                if(!is_null($value)) $connection_string_params[] = $key . '=' . $value;
            }
            $connection_string = $this->_type . ':' . implode($connection_string_params, ';');
        }

        $username = $this->get_param('user');
        $password = $this->get_param('password');

        $this->_instance = new Pdo($connection_string, $username, $password, $this->_options);

    }
 
    public function get_instance() {
        if(!isset($this->_instance)) {
            $this->_init();
        }
        return $this->_instance;
    }
    
    /**
     * @return mixed
     */
    public function get_type()
    {
        return $this->_type;
    }
    
    /**
     * @param mixed $_type 
     * @return PdoConnection
     */
    public function set_type($_type)
    {
        $this->_type = $_type;
        return $this;
    }

    /**
     * @param mixed $key 
     * @return void
     */
    public function get_default_param($key) {
        $value = null;
        if(array_key_exists($key, $this->_default_params)) {
            $value = $this->_default_params[$key];
            trigger_error('Parameter "' . $key . '" was not given to the connection, default value "' 
                    . $value . '" has been assigned', E_USER_NOTICE);
        } 
        return $value;
    }

    /**
     * @param mixed $key 
     * @return void
     */
    public function get_param($key) {
        return array_key_exists($key, $this->_params) ? $this->_params[$key] : $this->get_default_param($key); 
    }
    
    /**
     * @return mixed
     */
    public function get_params()
    {
        return $this->_params;
    }
    
    /**
     * @param mixed $_params 
     * @return PdoConnection
     */
    public function set_params(array $params)
    {
        $this->_params = $params;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function get_options()
    {
        return $this->_options;
    }
    
    /**
     * @param mixed $_options 
     * @return PdoConnection
     */
    public function set_options(array $options)
    {
        $this->_options = $options;
        return $this;
    }
}
