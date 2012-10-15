<?php 
namespace Incube\Event;
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */

interface IEvent {

    /** @return string */
    public function getName();

    /** @param string $name */
    public function setName($name);

    /** set the context, the object from which the event is triggered
      * @param string $name 
      * @return mixed
      */
    public function getParam($name);

    /** @return array */
    public function getParams();

    /** Return the context, the object from which the event is triggered
      * @return object
      */
    public function getTarget();

    /** @param object */
    public function setTarget($target);
}
