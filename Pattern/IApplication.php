<?php
namespace Incube\Pattern;
/** @author incubatio
  * This model contain is a base for web application
  * @depandancies Incube_Pattern_IURI, Incube_Pattern_IChecker, Incube_Pattern_IFilter 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
interface IApplication {

    /** @param array $resources 
      * @return IApplication
      */
    public function set_resources(array $resources);

    /** @return array $resources */
    public function get_resources();

    /** @return array $resources */
    public function get_resource($key);

	public function start();


    // Part below is replaced by the injection of an event manager

	/** @param Incube_Pattern_Checker checker */
	//public function addChecker(Incube_Pattern_IChecker $checker);

	/** @param Incube_Pattern_IFilter */
	//public function addFilter(Incube_Pattern_IFilter $object);

}
