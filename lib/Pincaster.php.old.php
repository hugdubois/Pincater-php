<?php
/**
 * Instanciante the client
 *   require_once 'Pincaster.php'
 *   $client = new PincasterClient('diz', 4269);
 *   
 *   - Register a new layer called "restaurants"
 *       $layer = $client->layer('restaurants'); //curl -dx http://diz:4269/api/1.0/layers/restaurants.json
 *             object(stdClass)#4 (2) {
 *               ["tid"]=>
 *               int(1)
 *               ["status"]=>
 *               string(8) "existing"
 *             }
 * 
 *   - Check the list of active layers:
 *       $client->layers(); //curl http://diz:4269/api/1.0/layers/index.json
 *             object(stdClass)#3 (2) {
 *               ["tid"]=>
 *               int(2)
 *               ["layers"]=>
 *               array(1) {
 *                 [0]=>
 *                 object(stdClass)#4 (7) {
 *                   ["name"]=>
 *                   string(11) "restaurants"
 *                   ["nodes"]=>
 *                   int(1)
 *                   ["type"]=>
 *                   string(7) "geoidal"
 *                   ["distance_accuracy"]=>
 *                   string(4) "fast"
 *                   ["latitude_accuracy"]=>
 *                   float(0.0001)
 *                   ["longitude_accuracy"]=>
 *                   float(0.0001)
 *                   ["bounds"]=>
 *                   array(4) {
 *                     [0]=>
 *                     int(-180)
 *                     [1]=>
 *                     int(-180)
 *                     [2]=>
 *                     int(180)
 *                     [3]=>
 *                     int(180)
 *                   }
 *                 }
 *               }
 *             }
 * 
 *   - Now let's add a hash record called "info". Just a set of key/values with no geographic data:
 *       $layer->set('info', array('secret_key' => 'supersecret', 'last_update' => '2001/07/08')); //curl -XPUT -d'secret_key=supersecret&last_update=2001/07/08' http://diz:4269/api/1.0/records/restaurants/info.json
 *       or $layer->set('info', 'secret_key=supersecret&last_update=2001/07/08');
 *             object(stdClass)#4 (2) {
 *               ["tid"]=>
 *               int(3)
 *               ["status"]=>
 *               string(6) "stored"
 *             }
 * 
 *   - What does the record look like?
 *       $layer->get('info'); //curl http://diz:4269/api/1.0/records/restaurants/info.json
 *             object(stdClass)#3 (4) {
 *               ["tid"]=>
 *               int(4)
 *               ["key"]=>
 *               string(4) "info"
 *               ["type"]=>
 *               string(4) "hash"
 *               ["properties"]=>
 *               object(stdClass)#4 (2) {
 *                 ["secret_key"]=>
 *                 string(11) "supersecret"
 *                 ["last_update"]=>
 *                 string(10) "2001/07/08"
 *               }
 *             }
 * 
 *   - Let's add a McDonald's, just with geographic data:
 *       $layer->set('abcd', array('_loc' => '48.512,2.243')); //curl -XPUT -d'_loc=48.512,2.243' http://diz:4269/api/1.0/records/restaurants/abcd.json
 *       or $layer->set('abcd', '_loc=48.512,2.243'));
 *             object(stdClass)#4 (2) {
 *               ["tid"]=>
 *               int(5)
 *               ["status"]=>
 *               string(6) "stored"
 *             }
 * 
 *   - What does the "abcd" record of the "restaurants" layer look like?
 *       $layer->get('abcd'); //curl http://diz:4269/api/1.0/records/restaurants/abcd.json
 *             object(stdClass)#3 (6) {
 *               ["tid"]=>
 *               int(6)
 *               ["key"]=>
 *               string(4) "abcd"
 *               ["type"]=>
 *               string(10) "point+hash"
 *               ["latitude"]=>
 *               float(48.512)
 *               ["longitude"]=>
 *               float(2.243)
 *               ["properties"]=>
 *               object(stdClass)#4 (4) {
 *                 ["name"]=>
 *                 string(10) "MacDonalds"
 *                 ["closed"]=>
 *                 string(1) "1"
 *                 ["address"]=>
 *                 string(6) "blabla"
 *                 ["visits"]=>
 *                 string(6) "100127"
 *               }
 *             }
 * 
 *   - Let's add some properties to this record, like a name, the fact that it's currently closed, an address and an initial number of visits:
 *       $layer->set('abcd', array('_loc' => '48.512,2.243')); //curl -XPUT -d'name=MacDonalds&closed=1&address=blabla&visits=100000' http://diz:4269/api/1.0/records/restaurants/abcd.json
 *       or $layer->set('abcd', 'name=MacDonalds&closed=1&address=blabla&visits=100000'));
 *             object(stdClass)#4 (2) {
 *               ["tid"]=>
 *               int(7)
 *               ["status"]=>
 *               string(6) "stored"
 *             }
 * 
 *   - Let's check it:
 *       $layer->get('abcd'); //curl http://diz:4269/api/1.0/records/restaurants/abcd.json
 *             object(stdClass)#3 (6) {
 *               ["tid"]=>
 *               int(8)
 *               ["key"]=>
 *               string(4) "abcd"
 *               ["type"]=>
 *               string(10) "point+hash"
 *               ["latitude"]=>
 *               float(48.512)
 *               ["longitude"]=>
 *               float(2.243)
 *               ["properties"]=>
 *               object(stdClass)#4 (4) {
 *                 ["name"]=>
 *                 string(10) "MacDonalds"
 *                 ["closed"]=>
 *                 string(1) "1"
 *                 ["address"]=>
 *                 string(6) "blabla"
 *                 ["visits"]=>
 *                 string(6) "100000"
 *               }
 *             }
 * 
 *   - Atomically delete the "closed" property from this record and add 127 visits:
 *       $layer->set('abcd', array('_delete:closed' => 1, 'closed' => '1', '_add_int:visits' => 127));//curl -XPUT -d'_delete:closed=1&_add_int:visits=127' http://diz:4269/api/1.0/records/restaurants/abcd.json
 *       or $layer->set('abcd', '_delete:closed=1&_add_int:visits=127'));
 *             object(stdClass)#4 (2) {
 *               ["tid"]=>
 *               int(9)
 *               ["status"]=>
 *               string(6) "stored"
 *             }
 * 
 *   - Now let's look for records whose location is near N 48.510 E 2.240, within a 7 kilometers radius:
 *       $layer->searchNear('48.510,2.240', 7000);//curl http://diz:4269/api/1.0/search/restaurants/nearby/48.510,2.240.json?radius=7000
 *       with limit 100 $layer->searchNear('48.510,2.240', 7000, 100);
 *       without properties $layer->searchNear('48.510,2.240', 7000, NULL, FALSE);
 *             object(stdClass)#3 (2) {
 *               ["tid"]=>
 *               int(340)
 *               ["matches"]=>
 *               array(1) {
 *                 [0]=>
 *                 object(stdClass)#4 (6) {
 *                   ["distance"]=>
 *                   float(2199.7)
 *                   ["key"]=>
 *                   string(4) "abcd"
 *                   ["type"]=>
 *                   string(10) "point+hash"
 *                   ["latitude"]=>
 *                   float(48.512)
 *                   ["longitude"]=>
 *                   float(2.243)
 *                   ["properties"]=>
 *                   object(stdClass)#5 (4) {
 *                     ["name"]=>
 *                     string(10) "MacDonalds"
 *                     ["closed"]=>
 *                     string(1) "1"
 *                     ["address"]=>
 *                     string(6) "blabla"
 *                     ["visits"]=>
 *                     string(6) "100127"
 *                   }
 *                 }
 *               }
 *             }
 * 
 *   - And what's in a rectangle, without properties?
 *       $layer->searchIn('48.000,2.000,49.000,3.000', NULL, FALSE);//curl http://diz:4269/api/1.0/search/restaurants/in_rect/48.000,2.000,49.000,3.000.json?properties=0
 *             object(stdClass)#4 (2) {
 *               ["tid"]=>
 *               int(341)
 *               ["matches"]=>
 *               array(1) {
 *                 [0]=>
 *                 object(stdClass)#3 (5) {
 *                   ["distance"]=>
 *                   float(20254.9)
 *                   ["key"]=>
 *                   string(4) "abcd"
 *                   ["type"]=>
 *                   string(10) "point+hash"
 *                   ["latitude"]=>
 *                   float(48.512)
 *                   ["longitude"]=>
 *                   float(2.243)
 *                 }
 *               }
 *             }
 * 
 * - ping
 *      $client->ping();//curl http://diz:4269/api/1.0/system/ping.json
 *      object(stdClass)#2 (2) {
 *        ["tid"]=>
 *        int(343)
 *        ["pong"]=>
 *        string(4) "pong"
 *      }
 * 
 * - rewrite
 *     $client->rewrite();//curl -XPOST http://$HOST:4269/api/1.0/system/rewrite.json
 *     bool(TRUE)
 * 
 * - shutdown
 *     $client->shutdown();//curl -XPOST http://diz:4269/api/1.0/system/shutdown.json
 *     bool(TRUE)
 */

class PincasterException extends Exception {}

/**
 * Private class used to accumulate a CURL response.
 * @package PincasterStringIO
 */
class PincasterStringIO {
	public function __construct() {
		$this->contents = '';
	}

	public function write($ch, $data) {
		$this->contents .= $data;
		return strlen($data);
	}

	public function contents() {
		return $this->contents;
	}
}

/**
 * The PincasterClient object holds information necessary to connect to
 * Pincaster. The Pincaster API uses HTTP, so there is no persistent
 * connection, and the PincasterClient object is extremely lightweight.
 * @package PincasterClient
 */
class PincasterClient {
	
	protected $_protocol           = 'http';
	protected $_prefix             = 'api';
	protected $_host               = NULL;
	protected $_port               = NULL;
	protected $_api_version        = NULL;
	protected $_request_format     = NULL;
	protected $_response_format    = NULL;
	
	
	public function __construct($host='127.0.0.1', $port=4269, $response_format = 'stdclass', $api='1.0', $request_format = 'json') {
		$this->_host            = $host;
		$this->_port            = $port;
		$this->_api_version     = $api;
		$this->_request_format  = $request_format;
		$this->_response_format = $response_format;
	}
	
	/**
	 * Return Pincaster server base url
	 * @return string
	 */
	public function getBaseUrl() {
		return $this->_protocol.'://'.implode(array($this->_host.':'.$this->_port, $this->_prefix, $this->_api_version), '/');
	}
	
	/**
	 * Return a ping response of Pincaster server
	 * @return mixed -- standard class if ping or NULL if not
	 *    $ret->tid  : integer
	 *    $ret->pong : string
	 **/
	public function ping() {
		$url = $this->getBaseUrl().'/system/ping'.PincasterUtils::formatExtension($this->_request_format);
		$response = $this->httpRequest('GET', $url);
		$ret = (($response != NULL) && (!empty($response[1]))) ? PincasterUtils::reponseByFormat($response[1], $this->_response_format, $this->_request_format) : NULL;
		return $ret;
	}
	
	/**
	 * Get the layer by the specified name. Since buckets always exist,
	 * this will always return a PincasterLayer.
	 * @return PincasterLayer
	 */
	public function layer($name) {
		return new PincasterLayer($this, $name, $this->_response_format, $this->_request_format);
	} 
	
	public function layers($value='') {
		$url = $this->getBaseUrl().'/layers/index'.PincasterUtils::formatExtension($this->_request_format);
		$response = $this->httpRequest('GET', $url);
		return PincasterUtils::reponseByFormat($response[1], $this->_response_format, $this->_request_format);
	}
	
	/**
	 * Shutdown a Pincaster server
	 */
	public function shutdown() {
		$url = $this->getBaseUrl().'/system/shutdown'.PincasterUtils::formatExtension($this->_request_format);
		$response = $this->httpRequest('POST', $url);
		//TODO retrun a good response
		// $ret = (($response != NULL) && (!empty($response[1]))) ? PincasterUtils::reponseByFormat($response[1]) : NULL;
		$ret = TRUE;
		return $ret;
	}
	
	/**
	 * Shutdown a Pincaster server
	 */
	public function rewrite() {
		$url = $this->getBaseUrl().'/system/rewrite'.PincasterUtils::formatExtension($this->_request_format);
		$response = $this->httpRequest('POST', $url);
		$ret = TRUE;
		return $ret;
	}
	/**
	 * Check if the Pincaster server for this PincasterClient is alive.
	 * @return boolean
	 */
	public function isAlive() {
		$response = $this->ping();
		$pong = PincasterUtils::getField($response, 'pong');
		return ($response != NULL) && ($pong == 'pong');
	}
	
	/**
	 * Given a Method, URL, Headers, and Body, perform and HTTP request,
	 * and return an array of arity 2 containing an associative array of
	 * response headers and the response body.
	 * @param string $method         - http method (GET, POST, PUT, DELETE)
	 * @param string $url            - http url request
	 * @param string $obj            - url encoded params for the request ex: foo=bar&foo1=bar1 -- (default : '')
	 * @param array $request_headers - header request -- (default an empty array)
	 */
	public function httpRequest($method, $url, $obj = '', $request_headers = array()) {
		//Set up curl
		//TODO make a persitant connection
		//http keep alive
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

		if ($method == 'GET') {
			curl_setopt($ch, CURLOPT_HTTPGET, 1);
		} else if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $obj);
		} else if ($method == 'PUT') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $obj);
		} else if ($method == 'DELETE') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		}

		//Capture the response headers...
		$response_headers_io = new PincasterStringIO();
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, array(&$response_headers_io, 'write'));

		//Capture the response body...
		$response_body_io = new PincasterStringIO();
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, array(&$response_body_io, 'write'));

		try {
			//Run the request.
			curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			//TODO a rendre persitant 
			curl_close($ch);

			//Get the headers...
			$parsed_headers = PincasterUtils::parseHttpHeaders($response_headers_io->contents());
			$response_headers = array("http_code"=>$http_code);
			foreach ($parsed_headers as $key=>$value) {
				$response_headers[strtolower($key)] = $value;
			}
			
			//Get the body...
			$response_body = $response_body_io->contents();

			//TODO Return a new PincasterResponse object ???.
			return array($response_headers, $response_body);
		} catch (Exception $e) {
			curl_close($ch);
			error_log('Error: ' . $e->getMessage());
			return NULL;
		} 
	}
	
}

/**
 * The PincasterLayer object allows you to access and change information
 * about a Pincaster layer, and provides methods to create or retrieve
 * objects within the layer.
 * @package PincasterLayer
 */
class PincasterLayer {
	
	protected $_client                        = NULL;
	protected $_name                          = NULL;
	protected $_response_format               = NULL;
	protected $_request_format                = NULL;
	protected $_is_store                      = FALSE;
	protected $_encode_internal_object_format = NULL; //serialized
	
	/**
	 * Constructor
	 * @param PincasterClient $client -- a pincaster client
	 * @param String $name -- a name of layer
	 * @param String $response_format -- a response format -- (default 'json')
	 * @param String $encode_internal_object_format -- if a value in a properties is not string specifier the encoding format -- default json else serialize
	 * @param String $request_format -- a resquest format on pincaster server -- (default 'json')
	 */
	public function __construct(PincasterClient $client, $name, $response_format = 'stdclass', $encode_internal_object_format = 'json', $request_format = 'json') {
		$this->_client                        = $client;
		$this->_name                          = $name;
		$this->_request_format                = $request_format;
		$this->_response_format               = $response_format;
		$this->_encode_internal_object_format = $encode_internal_object_format;
	}
	
	/**
	 * Get the layer name.
	 */
	public function getName() {
		return $this->_name;
	}
	
	/**
	 * Return a base url for the layer if $key param is empty else 
	 * return a base url for the layer for a key in $key param 
	 *
	 * @param $key -- String (optionnal)
	 * @return String -- An url
	 */
	public function getUrl($key = NULL) {
		$ret = (empty($key)) ?
			$this->_client->getBaseUrl().'/layers/'.urlencode($this->getName()).PincasterUtils::formatExtension($this->_request_format) :
			$this->_client->getBaseUrl().'/records/'.urlencode($this->getName()).'/'.urlencode($key).PincasterUtils::formatExtension($this->_request_format) ;
		return $ret;
	}
	
	/**
	 * Registering a layer in pincaster server
	 * @return mixed
	 */
	public function register() {
		$response = $this->_client->httpRequest('POST', $this->getUrl());
		$ret = PincasterUtils::reponseByFormat($response[1], $this->_response_format, $this->_request_format);
		in_array(PincasterUtils::getField($ret, 'status'), array('created', 'existing')) && $this->_is_store = TRUE;
		return $ret;
	}
	
	/**
	 * if if $key param is spécified deleted a key else delete this layer in pincaster server
	 * @param key
	 * @return mixed
	 */
	public function delete($key = NULL) {
		$response = $this->_client->httpRequest('DELETE', $this->getUrl($key));
		$ret = PincasterUtils::reponseByFormat($response[1], $this->_response_format, $this->_request_format);
		in_array(PincasterUtils::getField($ret, 'status'), array('deleted')) && $this->_is_store = FALSE;
		return $ret;
	}
	
	/**
	 * Create a new Pincaster record that will be stored as.
	 * @param	 string $key - Name of the key.
	 * @param	 object $data - The data to store. (default NULL)
	 * @return RiakObject
	 */
	public function get($key) {
		$response = $this->_client->httpRequest('GET', $this->getUrl($key));
		$ret = PincasterUtils::reponseByFormat($response[1], $this->_response_format, $this->_request_format);
		return $ret;
	}
	
	/**
	 * set a new key in this layer
	 *
	 * @param	 string $key - Name of the key.
	 * @param	 mixed array/string $data - The data to store.
	 * @return pincaster respoonse
	 */
	public function set($key, $data) {
		$_params = '';
		if (!empty($data)) {
			if (is_array($data)) {
				$_params = array();
				foreach($data as $k => $v) {
					switch(gettype($v)) {
						case 'string':
						case 'integer':
						case 'double':
							$_params[] = urlencode($k).'='.urlencode($v);
							break;
						case 'object':
						case 'array':
							$_params[] = urlencode($k).'='.urlencode($this->encodeInternalObject($v));
							break;
					}
				}
				$_params = implode($_params, '&');
			} elseif (is_string($data)) {
				$_params = $data;
			}
		}

		$response = $this->_client->httpRequest('PUT', $this->getUrl($key), $_params);
		$ret = PincasterUtils::reponseByFormat($response[1], $this->_response_format, $this->_request_format);
		return $ret;
	}
	
	protected function encodeInternalObject($obj) {
		$ret = '';
		switch ($this->_encode_internal_object_format) {
			case 'json':
				$ret = json_encode($obj);
				break;
			default:
				$ret = serialize($obj);
				break;
		}
		return $ret;
	}
	
	/**
	 * Finding records whose location is within a radius:
	 * @param $loc location point -- center point is defined as latitude,longitude.
	 * @param $radius radius, in meters
	 */
	public function searchNear($loc, $radius = NULL, $limit = NULL, $properties = TRUE) {
		$url = $this->searchUrl($loc, $limit, $properties);
		$url.= '&radius='.(empty($radius) ? 1000 : (double)$radius);
		$response = $this->_client->httpRequest('GET', $url);
		$ret = PincasterUtils::reponseByFormat($response[1], $this->_response_format, $this->_request_format);
		return $this->reformatSearchResponse($ret);
	}
	
	/**
	 * Finding records whose location is within a radius:
	 * @param $loc location point -- center point is defined as latitude,longitude.
	 * @param $radius radius, in meters
	 */
	public function searchIn($loc, $limit = NULL, $properties = TRUE) {
		$url = $this->searchUrl($loc, $limit, $properties, 'in');
		$response = $this->_client->httpRequest('GET', $url);
		$ret = PincasterUtils::reponseByFormat($response[1], $this->_response_format, $this->_request_format);
		return $this->reformatSearchResponse($ret);
		
	}
	
	protected function reformatSearchResponse($res) {
		(gettype($res) == 'object' && isset($res->matches)) && $res->matches = (array) $res->matches;
		return $res;
	}
	/**
	 * Return a base url for the layer if $key param is empty else 
	 * return a base url for the layer for a key in $key param 
	 *
	 * @param $key -- String (optionnal)
	 * @return String -- An url
	 */
	protected function searchUrl($loc, $limit = NULL, $properties = TRUE, $type='near') {
		$_search_url = array(
			'near' => 'nearby',
			'in'   => 'in_rect'
		);
		$u = (!empty($_search_url[$type])) ? $_search_url[$type] : $_search_url['near'];
		$u = $this->_client->getBaseUrl().'/search/'.urlencode($this->getName()).'/'.$u.'/'.$loc.PincasterUtils::formatExtension($this->_request_format);
		$_params = array();
		(empty($limit))  || $_params[] = 'limit'.(int)$limit;
		$_params[] = 'properties='.(empty($properties) ? '0' : '1');
		$_params = implode($_params, '&');
		$u.='?'.$_params;
		return $u;
	}
	
}

/**
 * Utility functions used by Pincaster library.
 * @package PincasterUtils
 */
class PincasterUtils {
	
	/**
	 * Parse an HTTP Header string into an asssociative array of
	 * response headers.
	 */
	static function parseHttpHeaders($headers) {
		$retVal = array();
		$fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headers));
		foreach( $fields as $field ) {
			if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
				$match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
				if( isset($retVal[$match[1]]) ) {
					$retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
				} else {
					$retVal[$match[1]] = trim($match[2]);
				}
			}
		}
		return $retVal;
	}
	
	static function reponseByFormat($response, $response_format = 'array', $request_format = 'json') {
		$ret = NULL;
		switch (strtolower($request_format)) {
			case 'json':
				switch (strtolower($response_format)) {
					case 'stdclass':
						$ret = json_decode($response);
						break;
					case 'array':
						$ret = json_decode($response, TRUE);
						break;
					default:
						$ret = $reponse;
						break;
				}
				break;
			default:
				$ret = $reponse;
				break;
		}
		return $ret;
	}
	
	static function formatExtension($format) {
		$ret = (empty($format)) ? '' : '.'.$format;
		return $ret;
	}
	
	static function getField($mixed, $field) {
		$ret = NULL;
		switch (gettype($mixed)) {
			case 'object':
				(! empty($mixed->{$field})) && $ret = $mixed->{$field};
				break;
			case 'array':
				(! empty($mixed[$field])) && $ret = $mixed[$field];
				break;
		}
		return $ret;
	}
	static function unsetField(&$mixed, $field) {
		$ret = NULL;
		switch (gettype($mixed)) {
			case 'object':
				unset($mixed->{$field});
				break;
			case 'array':
				unset($mixed[$field]);
				break;
		}
		return $ret;
	}
}