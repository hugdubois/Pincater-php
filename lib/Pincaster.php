<?php
/**
 *   - Instanciante the client
 *       require_once 'Pincaster.php'
 *       $client = new PincasterClient('diz', 4269);
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
 *               int(10)
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
 *               int(11)
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
 *        int(12)
 *        ["pong"]=>
 *        string(4) "pong"
 *      }
 * 
 * - rewrite
 *     $client->rewrite();//curl -XPOST http://$HOST:4269/api/1.0/system/rewrite.json
 *     object(stdClass)#3 (2) {
 *       ["tid"]=>
 *       int(82)
 *       ["rewrite"]=>
 *       string(7) "started"
 *     }
 * 
 * - shutdown
 *     $client->shutdown();//curl -XPOST http://diz:4269/api/1.0/system/shutdown.json
 *     bool(TRUE)
 */
/**
* 
*/
abstract class Pincaster {
	protected $_request_format         = NULL;
	protected $_response_format        = NULL;
	protected $_internal_object_format = NULL;
	
	protected function setFormat($response_format = 'stdclass', $internal_object_format = 'json', $request_format = 'json') {
		$this->_request_format         = $request_format;
		$this->_response_format        = $response_format;
		$this->_internal_object_format = $internal_object_format;
	}
	
	protected function encodeInternalObject($obj) {
		$ret = '';
		switch ($this->_internal_object_format) {
			case 'json':
				$ret = json_encode($obj);
				break;
			default:
				$ret = serialize($obj);
				break;
		}
		return $ret;
	}
	
	public function requestFormatSuffix() {
		$ret = (empty($this->_request_format)) ? '' : '.'.$this->_request_format;
		return $ret;
	}
	
	public function reponseByFormat($response) {
		if (empty($response)) return $response;
		$ret = NULL;
		switch (strtolower($this->_request_format)) {
			case 'json':
				switch (strtolower($this->_response_format)) {
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
	
	abstract public function request($method, $url, $values);
}

class PincasterClient extends Pincaster {
	
	protected $_protocol                = 'http';
	protected $_prefix                  = 'api';
	protected $_curl_handler            = NULL;
	protected $_host                    = NULL;
	protected $_port                    = NULL;
	protected $_api_version             = NULL;
		
	public function __construct($host='localhost', $port = '4269', $api = '1.0', $response_format = 'stdclass',  $internal_object_format = 'json', $request_format = 'json', $protocol = 'http') {
		$this->_host                   = $host;
		$this->_port                   = $port;
		$this->_api_version            = $api;
		$this->setFormat($response_format,  $internal_object_format, $request_format);
		$this->curlInit();
	}
	
	/**
	 * Return Pincaster server base url
	 * @return string
	 */
	public function getBaseUrl() {
		return $this->_protocol.'://'.implode(array($this->_host.':'.$this->_port, $this->_prefix, $this->_api_version), '/');
	}

	/**
	 * initialize a curl connection for keep alive connect
	 **/
	protected function curlInit() {
		//debug
		//echo "OPEN handler";
		//end debug
		$this->_curl_handler = curl_init();
		curl_setopt($this->_curl_handler, CURLOPT_HTTPHEADER,
		           array('Connection: Keep-Alive',
		                 'Keep-Alive: 300'));
		curl_setopt($this->_curl_handler, CURLOPT_HEADER, 0);
		
		curl_setopt($this->_curl_handler, CURLOPT_FORBID_REUSE, 0);
		curl_setopt($this->_curl_handler, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->_curl_handler, CURLOPT_FOLLOWLOCATION, 1);
	}

	/**
	 * initialize a curl connection for keep alive connect
	 **/
	protected function curlClose() {
		//debug
		//echo "KILL handler";
		//end debug
		curl_close($this->_curl_handler);
	}

	/**
	 * close the curl handler
	 **/
	public function __destruct() {
		$this->curlClose();
	}

	/**
	 * Given a Method, URL, Headers, and Body, perform and HTTP request,
	 * and return an array of arity 2 containing an associative array of
	 * response headers and the response body.
	 * @param string $method         - http method (GET, POST, PUT, DELETE)
	 * @param string $url            - http url request
	 * @param string $obj            - url encoded params for the request ex: foo=bar&foo1=bar1 -- (default : '')
	 */
	public function execRequest($method, $url, $values = '') {
		$method = strtoupper($method);
		$resp = NULL;
		switch ($method) {
			case 'POST':
				curl_setopt($this->_curl_handler, CURLOPT_POST         , 1);
				curl_setopt($this->_curl_handler, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($this->_curl_handler, CURLOPT_POSTFIELDS   , $values);
				curl_setopt($this->_curl_handler, CURLOPT_URL          , $url);
				$resp_body = curl_exec($this->_curl_handler);
				break;
			case 'PUT':
				curl_setopt($this->_curl_handler, CURLOPT_POST         , 1);
				curl_setopt($this->_curl_handler, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($this->_curl_handler, CURLOPT_POSTFIELDS   , $values);
				curl_setopt($this->_curl_handler, CURLOPT_URL          , $url);
				$resp_body = curl_exec($this->_curl_handler);
				break;
			case 'DELETE':
				curl_setopt($this->_curl_handler, CURLOPT_POST         , 1);
				curl_setopt($this->_curl_handler, CURLOPT_CUSTOMREQUEST, 'DELETE');
				curl_setopt($this->_curl_handler, CURLOPT_POSTFIELDS   , $values);
				curl_setopt($this->_curl_handler, CURLOPT_URL          , $url);
				$resp_body = curl_exec($this->_curl_handler);
				break;
			default: 
				curl_setopt($this->_curl_handler, CURLOPT_POST         , 0);
				curl_setopt($this->_curl_handler, CURLOPT_CUSTOMREQUEST, 'GET');
				curl_setopt($this->_curl_handler, CURLOPT_POSTFIELDS   , $values);
				curl_setopt($this->_curl_handler, CURLOPT_URL          , $url);
				$resp_body = curl_exec($this->_curl_handler);
				break;
		}
		
		$info = curl_getinfo($this->_curl_handler);
		
		($info['http_code'] == 200) || $resp_body = NULL;
		
		//debug
		// echo $method.' :: '.$url. " \n";
		// if (!empty($values)) echo "> \n> ".$values."\n> \n";
		// echo '< '.str_replace("\n", "\n< ", $resp_body)."\n\n";
		//end debug
		return $resp_body;
	}
	
	public function request($method, $url, $values = '') {
		return $this->reponseByFormat($this->execRequest($method, $url, $values));
	}
	
	protected function getUrl($what = 'ping') {
		return $this->getBaseUrl().'/system/'.$what.$this->requestFormatSuffix();
	}
	/**
	 * Return a ping response of Pincaster server
	 * @return mixed -- standard class if ping or NULL if not
	 *    $ret->tid  : integer
	 *    $ret->pong : string
	 **/
	public function ping() {
		return $this->request('GET', $this->getUrl('ping'));
	}
	
	/**
	 * Shutdown a Pincaster server
	 */
	public function shutdown() {
		$this->request('POST', $this->getUrl('shutdown'));
		return $this->reponseByFormat('{"server_shutdown":"ok"}');
	}
	
	/**
	 * Rewrite a Pincaster server stored file
	 */
	public function rewrite() {
		return $this->request('POST', $this->getUrl('rewrite'));
	}
	
	/**
	 * Retrun list of sotred layers
	 */
	public function layers() {
		return $this->request('GET', $this->getBaseUrl().'/layers/index'.$this->requestFormatSuffix());
	}
	
	/**
	 * Get the layer by the specified name. Since buckets always exist,
	 * this will always return a PincasterLayer.
	 * @return PincasterLayer
	 */
	public function layer($name) {
		return new PincasterLayer($this, $name, $this->_response_format, $this->_internal_object_format, $this->_request_format);
	}

	
}
/**
 * The PincasterLayer object allows you to access and change information
 * about a Pincaster layer, and provides methods to create or retrieve
 * objects within the layer.
 * @package Pincaster
 */
class PincasterLayer extends Pincaster {
	protected $_client  = NULL;
	protected $_name    = NULL;
		
	public function __construct(PincasterClient $client, $name, $response_format = 'stdclass', $internal_object_format = 'json', $request_format = 'json') {
		$this->_client = $client;
		$this->_name   = $name;
		$this->setFormat($response_format,  $internal_object_format, $request_format);
	}
	
	public function request($method, $url, $values = '') {
		return $this->reponseByFormat($this->_client->execRequest($method, $url, $values));
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
			$this->_client->getBaseUrl().'/layers/'.urlencode($this->getName()).$this->requestFormatSuffix() :
			$this->_client->getBaseUrl().'/records/'.urlencode($this->getName()).'/'.urlencode($key).$this->requestFormatSuffix() ;
		return $ret;
	}
	
	/**
	 * Registering a layer in pincaster server
	 * @return mixed
	 */
	public function register() {
		return $this->request('POST', $this->getUrl(), '');
	}
	
	/**
	 * if if $key param is spécified deleted a key else delete this layer in pincaster server
	 * @param key
	 * @return mixed
	 */
	public function delete($key = NULL) {
		return $this->request('DELETE', $this->getUrl($key));
	}
	
	/**
	 * Create a new Pincaster record that will be stored as.
	 * @param  string $key - Name of the key.
	 * @param  object $data - The data to store. (default NULL)
	 * @return mixed
	 */
	public function get($key) {
		return $this->request('GET', $this->getUrl($key));
	}
	
	/**
	 * set a new key in this layer
	 *
	 * @param   string $key - Name of the key.
	 * @param   mixed array/string $data - The data to store.
	 * @return  mixed
	 */
	public function set($key, $values) {
		$_params = '';
		if (!empty($values)) {
			if (is_array($values)) {
				$_params = array();
				foreach($values as $k => $v) {
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
			} elseif (is_string($values)) {
				$_params = $values;
			}
		}
		return $this->request('PUT', $this->getUrl($key), $_params);
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
		$u = $this->_client->getBaseUrl().'/search/'.urlencode($this->getName()).'/'.$u.'/'.$loc.$this->requestFormatSuffix();
		$_params = array();
		(empty($limit))  || $_params[] = 'limit'.(int)$limit;
		$_params[] = 'properties='.(empty($properties) ? '0' : '1');
		$_params = implode($_params, '&');
		$u.='?'.$_params;
		return $u;
	}
	
	/**
	 * to be sure we have an array on matches field
	 **/
	protected function reformatSearchResponse($res) {
		(gettype($res) == 'object' && isset($res->matches)) && $res->matches = (array) $res->matches;
		return $res;
	}
	
	/**
	 * Finding records whose location is within a radius:
	 * @param $loc location point -- center point is defined as latitude,longitude.
	 * @param $radius radius, in meters
	 */
	public function searchNear($loc, $radius = NULL, $limit = NULL, $properties = TRUE) {
		$url = $this->searchUrl($loc, $limit, $properties);
		$url.= '&radius='.(empty($radius) ? 1000 : (double)$radius);
		return $this->reformatSearchResponse($this->request('GET', $url));
	}
	
	/**
	 * Finding records whose location is within a radius:
	 * @param $loc location point -- center point is defined as latitude,longitude.
	 * @param $radius radius, in meters
	 */
	public function searchIn($loc, $limit = NULL, $properties = TRUE) {
		$url = $this->searchUrl($loc, $limit, $properties, 'in');
		return $this->reformatSearchResponse($this->request('GET', $url));
	}
}
