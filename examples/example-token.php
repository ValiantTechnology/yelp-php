<?php
/**
 * This is an example script to obtain a token from the Yelp Fusion API
 */
use TVW\Yelp;
require '../vendor/autoload.php';

/**
 * You must specify a Yelp App ID and Secret for this example
 * see: https://www.yelp.com/developers
 */
$id         =  "";
$secret     =  "";

try {
    $result = Yelp::bearerRequest($id, $secret);
    var_dump($result);
} catch (Exception $e) {
    print $e->getMessage();
}
