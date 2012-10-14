<?php
/** @author incubatio 
  * @depandancy Incube_HTML
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_View_Component_Grid {
	
	/** @var array */
    protected $_data = array();

	/** @var array */
    protected $_columns = array();

    public function __construct() {

    }

    /** @param array $data */
    public function setData(array $data) {
        $this->_data = $data;
    }

    /** @param array $columns */
    public function setColumns(array $columns) {
        $this->_columns = $columns;
    }

    /** @param string $actionUrl
      * @param string $actionLabel
      * @param array $params
      * @param string $columnTitle */
    public function addRowAction($actionUrl, $actionLabel, array $params = array(), $columnTitle = "actions") {
        $this->_columns[] = $columnTitle;
        foreach($this->_data as $key => $data) {
            $temp = array_key_exists($columnTitle, $data) ?  $this->_data[$key][$columnTitle] : "";
            $paramsUrl="";
            foreach($params as $param) {
                $paramsUrl[] = $param . DS . $data[$param];
            }
            $this->_data[$key][$columnTitle] = Incube_Encoder_HTML::createTag("a", array("href" => $temp . $actionUrl . DS . implode(DS,$paramsUrl)), $actionLabel);
        }
    }

    /** @param string $mode
      * @return string */
    public function render($mode = "xhtml") {
		$firstCol = key($this->_columns);
		switch($mode) {
			case "xhtml":
				foreach($this->_data as $key => $value) {
					$firstKey = key($value);
					$htmlObject = Incube_HTML_Element::factory($firstKey, $value[$firstKey], array('type' => "checkbox"));
					//Incube_Debug::dump($htmlObject->render());die;

					$this->_data[$key][$firstKey] = $htmlObject->render();
				}
				unset($this->_columns[$firstCol]);
				return $this->HTMLGrid($this->_columns, $this->_data);
			//case "text":
				//return implode(array_merge($this->_columns, $this->_data));
			case "json":
				return json_encode($this->_data);
			default:
				return "unexistent render mode";

		}
    }


    /** @param array $ths
      * @param array $tds */
	public function HTMLGrid($ths, $tds) {
		$out = "<table class=\"tgrid\">
			<thead>
			<tr>
			<td/>\n"; //ths
			foreach($ths as $th) {
				$out .= "<th>" . ucfirst($th) . "</th>";
			}   
		$out .="   </tr>
			</thead>
			<tbody>\n";
		foreach($tds as $key => $td) {

			$fkey = key($td);
			$out .= "<td>$td[$fkey]</td>\n";
			unset($td[$fkey]);
			foreach($td as $test) {
				$out .= "<td>$test</td>\n";
			}   
			$out .= "</tr>\n";
		}   

		$out .="    </tr>
			<tbody>
			</table>";
		return $out;
	}
}
?>
