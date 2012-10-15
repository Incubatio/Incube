<?php
namespace Incube\Event;
/** @author incubatio
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class EventManager {

    protected $_events    = array();
    protected $_listeners = array();
    protected $_options   = array();
    protected $_separator = '.';

    /** trigger an event to list of events, call a listener method with an event as param 
      * 
      * @param string $eventName
      * @param mixed $target 
      * @param array $argv
      *
      * TODO: add callback support
      * @param mixed $callback
      *
      * @return EventManager 
      */
    public function trigger($eventName, $target = null, $argv = array(), $callback = null) {
        $names = array();
        if(is_object($target)) {
            $class = get_class($target);
            do {
                $names[] = $class . $this->_separator . $eventName;
            } while ($class = get_parent_class($class)); 
        } else $names[] = $eventName;

        foreach($names as $name) {
            if (array_key_exists($name, $this->_events)) {
                $myEvent = new Event($name, $target, $argv);
                foreach ($this->_events[$name] as $event) {
                    list($callable, $priority) = $event;
                    list($listenerName, $callableName) = $callable;
                    if(!array_key_exists($listenerName, $this->_listeners)) $this->_listeners[$listenerName] = new $listenerName();
                    $listener = $this->_listeners[$listenerName];
                    call_user_func(array($listener, $callableName), $myEvent);
                    //callback stuff ?, not sure if usefull
                }
            }
        }
    } 

    /** add an event to list of events, event represented by association of listeners and callable
      * 
      * @param string $eventName
      * @param array('objectName', 'methodName') $callable
      * @param int $priority
      *
      * @return EventManager 
      */
    public function attach($event, $callable, $priority = 1) {
        if (is_array($event)) $event = implode($this->_separator, $event);
        if (is_array(current($callable))) { 
            foreach($callable as $c) {
                $this->attach($event, $c, $priority);
            }
        }
        if (!array_key_exists($event, $this->_events)) $this->_events[$event] = array();
        $this->_events[$event][] = array($callable, $priority);
        return $this;
    }

    /** Get a list of events, event represented by association of listeners and callable
      * 
      * @return array
      */
    public function getEvents() {
        return $this->_events;
    }

    /** Set a list of events, event represented by association of listeners and callable
      * 
      * @return EventManager 
      */
    public function setEvents(array $events) {
        $this->_events = $events;
        return $this;
    }

    /** 
      * @return array 
      */
    public function getOptions() {
        return $this->_options;
    }

    /** 
      * @param array $options
      *
      * @return EventManager 
      */
    public function setOptions(array $options) {
        $this->_options = $options;
        return $this;
    }
}
