<?php
/** @author incubatio
  * This model contain is a base for web application
  * @depandancies Incube_Pattern_IURI, Incube_Pattern_IChecker, Incube_Pattern_IFilter 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
interface Incube_Pattern_IApplication {
    
	/** @param string $appName
	  * @param Incube_Pattern_IURI $URI
	  * @param array $options */
	public function __construct($appName, Incube_Pattern_IURI $URI, array $options = array());

	public function start();

	/** @param Incube_Pattern_Checker checker */
	public function addChecker(Incube_Pattern_IChecker $checker);

	/** @param Incube_Pattern_IFilter */
	public function addFilter(Incube_Pattern_IFilter $object);

}
