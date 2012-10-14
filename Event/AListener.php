<?php

/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
abstract class Incube_Event_AListener implements Incube_Event_IListener {

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
