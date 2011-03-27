<?php
/** @author incubatio
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
interface Incube_Pattern_IURI {

	/** @param string $key */
    public function getParam($key);

	public function getMainParams();

	public function getContentType();
}
