<?php

function curlInit() {
	global $ch;
	$ch = curl_init();


	curl_setopt($ch, CURLOPT_HTTPHEADER,
	           array('Connection: Keep-Alive',
	                'Keep-Alive: 300'));
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
}

function execRequest($method, $url, $values ='') {
	global $ch;
	// curlInit();
	$method = strtoupper($method);
	$resp = NULL;
	switch ($method) {
		case'POST':
			curl_setopt($ch, CURLOPT_POST         , 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS   , $values);
			curl_setopt($ch, CURLOPT_URL          , $url);
			$resp_body = curl_exec($ch);
			break;
		case'PUT':
			curl_setopt($ch, CURLOPT_POST         , 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'PUT');
			curl_setopt($ch, CURLOPT_POSTFIELDS   , $values);
			curl_setopt($ch, CURLOPT_URL          , $url);
			$resp_body = curl_exec($ch);
			break;
		case'DELETE':
			curl_setopt($ch, CURLOPT_POST         , 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'DELETE');
			curl_setopt($ch, CURLOPT_POSTFIELDS   , $values);
			curl_setopt($ch, CURLOPT_URL          , $url);
			$resp_body = curl_exec($ch);
			break;
		default: 
			curl_setopt($ch, CURLOPT_POST         , 0);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'GET');
			curl_setopt($ch, CURLOPT_POSTFIELDS   , $values);
			curl_setopt($ch, CURLOPT_URL          , $url);
			$resp_body = curl_exec($ch);
			break;
	}
	
	$info = curl_getinfo($ch);
	
	($info['http_code'] == 200) || $resp_body = NULL;
	// curlClose();
	//debug
	// echo $method.' ::'.$url. " \n";
	// if (!empty($values)) echo "> \n> ".$values."\n> \n";
	// echo'<'.str_replace("\n", "\n< ", $resp_body)."\n\n";
	//end debug
	return $resp_body;
}

function curlClose() {
	global $ch;
	curl_close($ch);
}

curlInit();
var_dump(execRequest('DELETE' , 'http://localhost:4269/api/1.0/layers/restaurants.json'));
var_dump(execRequest('POST'   , 'http://localhost:4269/api/1.0/layers/restaurants.json'));
var_dump(execRequest('GET'    , 'http://localhost:4269/api/1.0/layers/index.json'));
var_dump(execRequest('PUT'    , 'http://localhost:4269/api/1.0/records/restaurants/info.json', 'secret_key=supersecret&last_update=2001/07/08'));
var_dump(execRequest('GET'    , 'http://localhost:4269/api/1.0/records/restaurants/info.json'));
var_dump(execRequest('PUT'    , 'http://localhost:4269/api/1.0/records/restaurants/abcd.json', '_loc=48.512,2.243'));
var_dump(execRequest('GET'    , 'http://localhost:4269/api/1.0/records/restaurants/abcd.json'));
var_dump(execRequest('PUT'    , 'http://localhost:4269/api/1.0/records/restaurants/abcd.json', 'name=MacDonalds&closed=1&address=blabla&visits=100000'));
var_dump(execRequest('GET'    , 'http://localhost:4269/api/1.0/records/restaurants/abcd.json'));
var_dump(execRequest('PUT'    , 'http://localhost:4269/api/1.0/records/restaurants/abcd.json', '_delete:closed=1&_add_int:visits=127'));
var_dump(execRequest('GET'    , 'http://localhost:4269/api/1.0/search/restaurants/nearby/48.510,2.240.json?properties=1&radius=7000'));
var_dump(execRequest('GET'    , 'http://localhost:4269/api/1.0/search/restaurants/in_rect/48.000,2.000,49.000,3.000.json?properties=0')); 
var_dump(execRequest('GET'    , 'http://localhost:4269/api/1.0/system/ping.json'));
var_dump(execRequest('POST'   , 'http://localhost:4269/api/1.0/system/shutdown.json'));
curlClose();
