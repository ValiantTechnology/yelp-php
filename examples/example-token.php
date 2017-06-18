<?php
/**
 * This is an example script to obtain a token from the Yelp Fusion API
 */
use TVW\Yelp;
require '../vendor/autoload.php';

$id         =  "903XGskK28EgQ-CoMyXDYw";
$secret     =  "9YiZkTsFvXkUPRaVBQMqQpHMvky7V4szBSBBS2WSKkmZgxmq8gnBwFcUiMGwseI8";

try {
    $result = Yelp::bearerRequest($id, $secret);
    var_dump($result);
} catch (Exception $e) {
    print $e->getMessage();
}
