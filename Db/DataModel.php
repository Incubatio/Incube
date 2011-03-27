<?php
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  * TODO: Limit Driver and Adapter to the available list 
  */
class Incube_Db_DataModel {
		
        /** @param string $type
		  * @return Incube_Pattern_IDriver */
        public static function driverFactory($type) {
            $adapterClassName = 'Incube_Db_Driver_' . $type;
            return new $adapterClassName();
        }

        /** @param string $type
		  * @return Incube_Pattern_IAdaptater */
        public static function adapterFactory($type) {
            $adapterClassName = 'Incube_Db_Adapter_' . $type;
            return new $adapterClassName();
        }

}
