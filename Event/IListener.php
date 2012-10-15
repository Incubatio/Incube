<?php
namespace Incube\Event;
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */

interface IListener {
	/** @return array $value */
    public static function getEvents();

	/** @param array $value */
    public static function setEvents(array $events);
}
