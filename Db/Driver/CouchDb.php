<?php
/** @author incubatio
  * @depandancy Incube_HTTP_Query
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  *
  * TODO: Finish This class
  */
class Incube_Db_Driver_CouchDb extends Incube_HTTP_Query {


    /** @var string */
    protected $_dataType = "application/json";


    /** @param string $host
      * @param string $port
      * @param string $db */
    function __construct($host = "localhost", $port="5984", $db = "") {
        $this->_host = $host;
        $this->_port = $port;
        $this->_database = $db;
    }

    /** @param array $options */
    public function init(array $options) {
        foreach($options as $key => $option) {
            $key = "_" . $key;
            $this->$key = $option;
        }
    }


    /** Return the result of a request under an associative array
     * @return Array
     */
    public function fetchArray() {
        $result = $this->execute();
    }
    
    private function connect() {
        $this->sock = @fsockopen($this->_host, $this->_port, $err_num, $err_string);
        if(!$this->sock) {
            throw new Incube_Exception('Could not open connection to '.$this->_host.':'.$this->_port.' ('.$err_string.')');
        }
    }

    /** @param string $query
	  * @return string */
    function execute($query = null) {
        if(empty($query)) {
            $query = $this->getQuery();
        }
        //TODO : Check Syntax below
        if (preg_match("/PUT/", $query)) {
            if(!array_key_exists("_rev", $this->_data)) {
                $q = new Incube_HTTP_Query();
                $q->in($this->_db)->get($this->_id);
                $temp = $this->send($q->getQuery());
                $temp  = $temp->getBody(true);
                $this->_data["_rev"] = $temp["_rev"];
                $query = $this->getQuery();
            }
        }
//        var_dump($query);die;
        $response = $this->send($query);
        $body = $response->getBody(true);
        if (array_key_exists("error", $body)){
            Incube_debug::dump($response);die;
            throw new Incube_Exception($body['error']. " : " . $body['reason']);
        }
//        Incube_debug::dump($response);die;
        
        return $body;
    }

    private function disconnect() {
        fclose($this->sock);
        $this->sock = NULL;
    }

    /** @param string $query
	  * @return ??? */
    private function send($query) {
        $this->connect();
        fwrite($this->sock, $query);
        $response = '';
        while(!feof($this->sock)) {
            $response .= fgets($this->sock);
        }
        $this->disconnect();
        $this->response = new Incube_HTTP_Response($response);
        return $this->response;
    }

	/** ???? */
    function getAllDocs() {
        return $this->get('/_all_docs')->fetchArray();
    }
}
?>
