<?php
namespace Incube;
/** @author incubatio
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html 
  */
class FileExplorer {

	/** @param string $path
	  * @return array */
	public static function list_dirs($path) {
        $files = scandir($path);    
        $list = array();
        foreach($files as $file) if(is_dir($path . DIRECTORY_SEPARATOR . $file) && !in_array($file, array(".", ".."))) $list[] = $file;
        return $list;
	}

	/** @param string $path
	  * @return array */
	public static function list_files($path) {
        $files = scandir($path);    
        $list = array();
        foreach($files as $file) if(!is_dir($path . DIRECTORY_SEPARATOR . $file) && !in_array($file, array(".", ".."))) $list[] = $file;
        return $list;
	}

	/** @param string $path
	  * @return string */
	public static function read($path) {
		$fp = fopen($path, 'r');
		$data = fread($fp, filesize($path));
		fclose($fp);
		return $data;
	}

	/** @param string $path
	  * @param string $data
	  * @return string */
	public static function write($path, $data) {
		$fp = fopen($path, 'w');
		fwrite($fp, $data);
		fclose($fp);
	}

	/** @param string $path
	  * @return string */
	public static function exists($path) {
		return file_exists($path);
	}

	// Adding to much check will make the framework heavier for nothing, errors a reported by php, maybe put exception for the user to let him deal with error including framework lines.
	//Security check on the file and on filename => if (preg_match('#[^\w-\.\/\\:]#', $filename)) {

}
