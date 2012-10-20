<?php
namespace Incube\Db;

/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  * TODO: Limit Driver and Adapter to the available list 
  */

class DataModel {
    protected static $_pdo_drivers = array(
            'CUBRID',
            'DBLIB',
            'FIREBIRD',
            'IBM',
            'INFORMIX',
            'MYSQL',
            'OCI',
            'ODBC',
            'PGSQL',
            'SQLITE',
            'SQLSRV',
            '4D',
            );

        /** @param string $type
		  * @return Incube\Pattern\IDriver */
        public static function driver_factory($type) {
            $type = strtoupper($type);
            if(in_array($type, self::$_pdo_drivers)) {
                $connection = new PdoConnection($type);
            }
            $driver_class_name = '\Incube\Db\Driver\\' . $type;
            return new $driver_class_name($connection);
        }

        /** @param string $type
		  * @return Incube\Pattern\IAdaptater */
        public static function adapter_factory($type) {
            $adapter_class_name = '\Incube\Db\Adapter\\' . $type;
            return new $adapter_class_name();
        }

}
