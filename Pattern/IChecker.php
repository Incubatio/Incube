<?php
/** @author Incubatio
  *
  */

interface Incube_Pattern_IChecker {

	/** @param array $resource 
	  * @return bool */
	public function isCheckable(array $params);
}
