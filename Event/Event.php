<?php
namespace Incube\Event;
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */

use ArrayAccess;

class Event implements IEvent
{
    /** @var string */
    protected $_name;

    /** @var array */
    protected $_params;

    /** @var object */
    protected $_target;

    /** @param  string $name
      * @param  object $target
      * @param  array $params
      */
    public function __construct($name = null, $target = null, array $params = array())
    {
        $this->_name    = $name;
        $this->_target  = $target;
        $this->_params  = $params;
    }

    /** @return string */
    public function getName()
    {
        return $this->_name;
    }

    /** @param  string $name 
      * @return Event
      */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /** @param  array
      * @return Event
      */
    public function setParams(array $params)
    {
        $this->_params = $params;
        return $this;
    }

    /** @return array */
    public function getParams()
    {
        return $this->_params;
    }

    /** @param  string
      * @return mixed
      */
    public function getParam($name)
    {
        return array_key_exists($name, $this->_params) ? $this->_params[$name] : false;
    }

    /** @param  string $name 
      * @param  mixed $value 
      * @return Event
      */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    /** @return object */
    public function getTarget()
    {
        return $this->_target;
    }

    /** set the context, the object from which the event is triggered
      * @return Event
      */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

}

