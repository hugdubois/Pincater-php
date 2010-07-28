<?php

function test($v) {
	var_dump($v);
}
require_once 'lib/Pincaster.php';

$client = new PincasterClient('localhost', 4269);
$layer = $client->layer('restaurants');
$layer->delete();
$layer->register();
test($client->layers());
test($layer->set('info', array('secret_key' => 'supersecret', 'last_update' => '2001/07/08')));
// // or test($layer->set('info', 'secret_key=supersecret&last_update=2001/07/08'));
test($layer->get('info'));
test($layer->set('abcd', array('_loc' => '48.512,2.243')));
test($layer->get('abcd'));
test($layer->set('abcd', array('_loc' => '48.512,2.243')));
// // or test($layer->set('abcd', 'name=MacDonalds&closed=1&address=blabla&visits=100000')));
test($layer->get('abcd'));
test($layer->set('abcd', array('_delete:closed' => 1, 'closed' => '1', '_add_int:visits' => 127)));
// // or test($layer->set('abcd', '_delete:closed=1&_add_int:visits=127')));
test($layer->searchNear('48.510,2.240', 7000));
// // with limit 100 test($layer->searchNear('48.510,2.240', 7000, 100));
// // without properties test($layer->searchNear('48.510,2.240', 7000, NULL, FALSE));
test($layer->searchIn('48.000,2.000,49.000,3.000', NULL, FALSE));
test($client->ping());
// test($client->rewrite());
test($client->shutdown());
// test($client->ping());
