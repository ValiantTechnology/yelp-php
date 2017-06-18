<?php
/**
 * This is an example script to demonstrate the methods in the TVW\Yelp Class
 *
 * Uncomment the method you'd like to see a demonstration of and a var_dump
 * will be performed on the information returned.
 */
use TVW\Yelp;
require '../vendor/autoload.php';

/**
 * You must specify a Yelp Fusion API token for this demo
 * see: https://www.yelp.com/developers
*/
$apiToken = "o2PnoSFoKGIiy-wbaHhnUobjNgKRLWDuXHNSZLobNXwWP3N3xiHzW2CTIU-8jhXvZkc65aes7qt8unGRjczJh_jg8ePw-keuOXxi9BjE1kXqX1g2TPjEqhSM6TQ8WXYx";

// parameters for example API queries
$testParams = [
    "location"      => "10001",
    "radius"        => "500",
    "sort_by"       => "distance",
    "categories"    => "restaurants",
    "limit"         => 5
];

$transactionParams = [
    "latitude"      => "40.730610",
    "longitude"     => "-73.935242"
];

$autoCompleteParams = [
    "text"          => "atomic",
    "latitude"      => "40.730610",
    "longitude"     => "-73.935242"
];

// create instance of Yelp class
$yelpFusion = new Yelp($apiToken);

/**
 * searchBusiness($params)
 * Searches for businesses based on provided parameters
 */
//$result = $yelpFusion->searchBusiness($testParams);

/**
 * searchPhone($phone)
 * Searches for businesses by phone number (+10123456789)
 */
//$result = $yelpFusion->searchPhone("+12127527470");

/**
 * searchTransaction($transactionType = "delivery", $params)
 * Searches for businesses by transaction type
 */
//$result = $yelpFusion->searchTransaction("delivery", $transactionParams);

/**
 * getBusiness($id, $params = null)
 * Retrieves detailed business information
 */
//$result = $yelpFusion->getBusiness("blue-hill-new-york");

/**
 * getReviews($id, $params = null)
 * Retrieves up to 3 business reviews
 */
$result = $yelpFusion->getReviews("blue-hill-new-york");

/**
 * autoComplete($params)
 * Retrieves suggested search terms, categories, etc.
 */
//$result = $yelpFusion->autoComplete($autoCompleteParams);

var_dump($result);