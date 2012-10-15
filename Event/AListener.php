<?php
namespace Incube\Event;
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */

use Incube\Event\IListener;

abstract class AListener implements IListener {

    /** @var array **/
    protected static $_events = array();

    /** @return array **/
    public static function getEvents() {
        return static::$_events;
    }

    /**
     * @param array $event
     *
     * @return Multee\Event\Alistener;
     */
    public static function setEvents(array $events) {
        static::$_events = $events;
        return $this;
    }
}
