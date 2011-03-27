<?php
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
interface Incube_Pattern_IValidator {

	/** @param string $value 
	  * @return bool */
	public function isValid($value);
	
}
