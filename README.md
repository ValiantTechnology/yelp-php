# Yelp Fusion API Wrapper
_A PHP Client wrapper for Yelp's Fusion API_

[![Build Status](https://travis-ci.org/ValiantTechnology/yelp-php.svg?branch=master)](https://travis-ci.org/ValiantTechnology/yelp-php) [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT) [![Twitter Follow](https://img.shields.io/twitter/follow/thevaliantway.svg?style=social&label=Follow)]()

Yelp's [Fusion API](https://www.yelp.com/developers/documentation/v3) allows you to get the best local business information and user reviews of over million businesses in 32 countries. 

This PHP client wrapper for Yelp's Fusion API makes it dead simple. Query the API using key/value pairs to pass along search parameters, and recieve a stdClass object to work with.

## Installation
Require this package with composer using the following command:
 ```
 composer require thevaliantway/yelp-api
 ```
 
 ## Authentication
Yelp's Fusion API uses OAuth2 for Authentication. Use the `bearerRequest()` method to retrieve an access token.
```
$id        = YELP_ID
$secret    = YELP SECRET
$yelpCreds = TVW\Yelp::bearerRequest($id, $secret);
```
On success, the method will return an array containing the following:

|Name          |Type    |Description                                                                                                                    |
|:------------ |:-------|:------------------------------------------------------------------------------------------------------------------------------|
| access_token | string | The access token which you'll use to access Yelp Fusion API endpoints.                                                        |
| token_type   | int    | The access token type. Always returns Bearer.                                                                                 |
| expires_in   | int    | Represents the number of seconds after which this access token will expire. Right now it's always 15552000, which is 180 days.|

_Access tokens are valid for 180 days, so a caching strategy for issued tokens suggested._

Once a valid token has been issued, you'll be able to work with the methods below.

## Business Search
Returns up to 1000 businesses based on the provided search criteria. Each API call is limited to a maximum of 50 results, so use the `offset` parameter to access results beyond the initial 50 returned.

The following example will query the API for the 5 closest restuarants within 500 meters of zip code 10001.
```
// parameters for testing
$searchParams = [
    "location"      => "10001",
    "radius"        => "500",
    "sort_by"       => "distance",
    "categories"    => "restaurants",
    "limit"         => 5
];
$yelpFusion = new Yelp(API_TOKEN);
$results    = $yelpFusion->searchBusiness($testParams);
```
You can use the id returned for a business with the `getBusiness()` method to retrieve detailled information.

Additional search parameters, along with detail on the API's response, may be found at [https://www.yelp.com/developers/documentation/v3/business_search](https://www.yelp.com/developers/documentation/v3/business_search)

## Phone Search
Returns a list of businesses based on the provided phone number. It is possible for more than one businesses having the same phone number (for example, chain stores with the same +1 800 phone number).

The following example will query the API for businesses with "+12127527470" as the listed phone number.
```
$yelpFusion = new Yelp(API_TOKEN);
$results    = $yelpFusion->searchPhone("+12127527470");
```
*Note:* The phone number used for searching must start with "+" and include the country code.

Detail on the API's response may be found at [https://www.yelp.com/developers/documentation/v3/business_search_phone](https://www.yelp.com/developers/documentation/v3/business_search_phone)

## Transaction Search
Returns a list of businesses which support certain transactions.

The following example will query the API for businesses that deliver to the specified coordinates:
```
$transactionParams = [
    "latitude"      => "40.730610",
    "longitude"     => "-73.935242"
];
$yelpFusion = new Yelp(API_TOKEN);
$result     = $yelpFusion->searchTransaction("delivery", $transactionParams);
```

Detail on the API's response may be found at [https://www.yelp.com/developers/documentation/v3/transactions_search](https://www.yelp.com/developers/documentation/v3/transactions_search)

## Autocomplete
Returns autocomplete suggestions for search keywords, businesses and categories, based on the input text.

The following example queries the API for autocomplete suggestions using "atomic" for the specified coordinates:
```
$autoCompleteParams = [
    "text"          => "atomic",
    "latitude"      => "40.730610",
    "longitude"     => "-73.935242"
];
$yelpFusion = new Yelp(API_TOKEN);
$results    = $yelpFusion->autoComplete($autoCompleteParams);
```
Detail on the API's response may be found at [https://www.yelp.com/developers/documentation/v3/autocomplete](https://www.yelp.com/developers/documentation/v3/autocomplete)

## Business Information
Returns the detail information of a business. A valid business id is required, and may be obtained via the business, phone, transaction, or autocomplete searches.

The following example will query the API for information on Blue Hill:
```
$yelpFusion = new Yelp(API_TOKEN);
$result     = $yelpFusion->getBusiness("blue-hill-new-york");
```
Detail on the API's response may be found at [https://www.yelp.com/developers/documentation/v3/business](https://www.yelp.com/developers/documentation/v3/business)

## Business Reviews
Returns the up to three reviews of a business. A valid business id is required, and may be obtained via the business, phone, transaction, or autocomplete searches.

The following example will query the API for reviews on Blue Hill:
```
$yelpFusion = new Yelp(API_TOKEN);
$result     = $yelpFusion->getReviews("blue-hill-new-york");
```

## Support
Please [open an issue](https://github.com/fraction/readme-boilerplate/issues/new) for support.

## Contributing
Please contribute using [Github Flow](https://guides.github.com/introduction/flow/). Create a branch, add commits, and [open a pull request](https://github.com/valianttechnology/yelp-php/compare/).

## License
This is free software distributed under the terms of the MIT license.

## About Valiant Technology
Valiant Technology is a Managed Service Provider, focused on the creative and hospitality industries. Our customers include ad agencies, PR firms, app and web developers, TV/Film producers, fashion designers, restaurants and retail. And yes, we also provide support to non-creative firms, provided they enjoy our culture and "click" with our team. Learn more at [http://thevaliantway.com](http://thevaliantway.com).
 