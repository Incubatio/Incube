<?php 
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  *
  * Internationalisation can contain thousand of translation
  * For obvious reasons of maintenance and structuring data, 
  * please split translation in multiple files and then when
  * your application pass into production mode, merge this files
  * via cache or via a serialized array for example.
  *
  * TODO: Check dependancy about SERVER
  * TODO: add localisation to i18n
  */
class Incube_I18n {

	/** @var string */
	protected $_lang;

	/** @var array */
	protected $_langs = array();

	/** @var string */
	protected $_folderName;

	/** @var string */
	protected $_defaultLang = "en";

	/** @var array */
	//TODO: when initOption, in devmode check availabitilie (strict) of variable inclass definition.
	protected $_data = array();

	/** @param string $lang 
	  * @param array $options */
	public function __construct($lang = null, array $options = array()) {
		//list langs
		// $langs = listdir("/config/langs")
		$this->initOptions($options);
		$lang = $lang ? $lang : $this->getNavLang() ? $this->getNavLang() : $this->_defaultLang; 
		$this->setLang($lang);
	}

	/** @param array $options */
    public function initOptions(array $options) {
        foreach ($options as $key => $option) {
            $this->{"_$key"} = $option;
        }
    }

	/** @return string */
	public function getNavLang() {
		return !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : trigger_error("No HTTP_ACCEPT_LANGUAGE http header in \$_server", E_USER_WARNING);
	}

	/** @return array */
	public function getLangs() {
		return $this->_langs;
	}

	/** @param array $langs */
	public function setLangs(array $langs) {
		$this->_langs = $langs;
	}

	/** @param string $lang 
	  * TODO: Change setLang to setCurrentLang */
	public function setLang($lang) {
		$_SESSION['lang'] = $this->_lang = $lang;
	}

	/** @return string */ 
	public function getLang() {
		return $this->_lang;
	}

	/** @param string $key
	  * @return string */ 
	public function trans($key) {
		if(array_key_exists($key, $this->_data)) return $this->_data[$key];
		else { 
			//trigger_error("No Traduction for $key");
			return $key;
		}
	}

	/** @param array $data */
	public function setData(array $data) {
		$this->_data = $data;
	}
}
