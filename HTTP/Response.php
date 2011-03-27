<?php
/** @author incubatio
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_HTTP_Response {

	/** @var string */
    private $rawResponse = '';

	/** @var string */
    private $headers = '';

	/** @var string */
    private $body = '';

	
	/** @param string $response */
    function __construct($response = '') {
        $this->raw_response = $response;
        list($this->headers, $this->body) = explode("\r\n\r\n", $response);
    }

	/** @return string */
    function getRawResponse() {
        return $this->rawResponse;
    }

	/** @return string */
    function getHeaders() {
        return $this->headers;
    }

	/** @var bool $decodeJSON 
	  * @return string */
    function getBody($decodeJSON = false) {
        return $decodeJSON ? json_decode($this->body, true) : $this->body;
    }
}
?>
