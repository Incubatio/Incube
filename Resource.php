<?php
namespace Incube;
/** @author incubatio
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */

class Resource {

    /** @var array */
    protected $_resources = array();

    /**@param array $resources */
    public function __construct(array $resources = array()) {
        $this->_resources = $resources;
    }

    /** @param string $resource_name 
      * @return object
      */
    protected function _load($resource_name) {
        if(!array_key_exists($resource_name, $this->_resources)) {
            $method = 'init_' . $resource_name;
            if(method_exists($this, $method)) {
                $this->_resources[strtolower($resource_name)] = $this->$method();
            } else throw new \Exception('init_' . $resource_name . ' does not exist');
        }
        return $this->_resources[$resource_name];
    }

    /** list all methods begining by "init_", load it into $this->_resources and then return $this->_resources
      * 
      * @return array 
      */
    public function load() {
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if(preg_match('/^init_/', $method)) {
                $resource_name = preg_replace('/init_/', '', $method);
                $this->_load($resource_name);
            }
        }
        return $this->_resources;
    }
}
