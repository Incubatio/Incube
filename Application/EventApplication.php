<?php
namespace Incube\Application;
/** @author incubatio
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */

use Incube\Pattern\IApplication,
    Incube\Event\EventManager;

class EventApplication implements IApplication{

    /** @var string */
    protected $_name;

    /** @var array */
    protected $_resources;

    /** @var EventManager */
    protected $_event_manager;

    /** @var array */
    protected $_event_list =  array('pre_main', 'main', 'post_main');

    /** @param string $app_name
      * @param EventManager $event_manager
      * @param array $resources */
    public function __construct($app_name, EventManager $event_manager, array $resources = array()) {
        $this->_name            = $app_name;
        $this->_event_manager   = $event_manager;
        $this->_resources       = $resources;
    }

    /** @param array $resources 
      * @return IApplication
      */
    public function set_resources(array $resources) {
        $this->_resources = $resources;
        return $this;
    }

    /** @return array $resources */
    public function get_resources() {
        return $this->_resources;
    }

    /** @return array $resources
      * @return IApplication
      */
    public function set_resource($key, $value) {
        $this->_resources[$key] = $value;
        return $this;
    }

    /** @return array $resources */
    public function get_resource($name) {
        return $this->_resources[$name];
    }

    /** @return EventManager */
    public function get_event_manager() {
        return $this->_event_manager;
    }

    /** @return string */
    public function get_name() {
        return $this->_name;
    }

    public function start() {
        foreach($this->_event_list as $event_name) {
            $this->_event_manager->trigger($event_name, $this);
        }
    }

}
