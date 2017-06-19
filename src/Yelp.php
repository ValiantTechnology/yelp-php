<?php
/**
 * Yelp-API: PHP Client wrapper for Yelp's Fusion API
 */
Namespace TVW;
use Httpful;
use phpDocumentor\Reflection\Types\Self_;

/**
 * Class Yelp
 * PHP Client wrapper for Yelp's Fusion API
 * https://www.yelp.com/developers/documentation/v3
 *
 * @package     TVW/Yelp-PHP
 * @category    API
 * @author      Matthew F. Fox <mfox@thevaliantway.com>
 * @copyright   2017 Valiant Technology
 * @license     MIT
 */
class Yelp{

    /**
     * Yelp API token.
     * @var null
     */
    public $apiToken          = null;

    /**
     * Yelp API Authorization bearer string.
     * @var string
     */
    public $apiBearer       = "Bearer";

    /**
     * Yelp API base URI
     * @var string
     */
    public static $apiUri   = "https://api.yelp.com";

    /**
     * Default search parameters
     * @var array
     */
    private $searchDefaults = [
        "term"          => "",
        "location"      => "",
        "latitude"      => "",
        "longitude"     => "",
        "radius"        => "",
        "categories"    => "",
        "locale"        => "",
        "limit"         => 50,
        "offset"        => "",
        "sort_by"       => "",
        "price"         => "",
        "open_now"      => "",
        "open_at"       => "",
        "attributes"    => ""
    ];

    /**
     * Default transaction search parameters
     * @var array
     */
    private $transactionDefaults = [
        "latitude"      => "",
        "longitude"     => "",
        "location"      => ""
    ];

    /**
     * Default business detail parameters
     * @var array
     */
    private $businessDefaults = [
        "locale"        => ""
    ];

    /**
     * Default business review parameters
     * @var array
     */
    private $reviewDefaults = [
        "locale"        => ""
    ];

    /**
     * Default autocomplete parameters
     * @var array
     */
    private $autoCompleteDefaults = [
        "text"          => "",
        "latitude"      => "",
        "longitude"     => "",
        "locale"        => ""
    ];

    /**
     * Yelp constructor.
     * @param $apiToken
     * @param string $apiBearer
     * @throws \Exception
     */
    function __construct($apiToken, $apiBearer = "Bearer")
    {
        if(!$apiToken)
            throw new \Exception("A token is required to continue.");
            $this->apiToken    = $apiToken;
            $this->apiBearer    = $apiBearer;
    }

    /**
     * Request token from Yelp Fusion OAuth2 service.
     * Tokens are valid for 180 days, so caching them is good idea.
     * @see https://www.yelp.com/developers/documentation/v3/authentication
     *
     * @param   string      $appId         Yelp App Client ID
     * @param   string      $appSecret     Yelp App Client Secret
     * @return  object                  Httpful response object body
     * @throws  \Exception              Contains HTTP error code returned by Httpful
     */
    public static function bearerRequest($appId, $appSecret)
    {
        $uri    = self::$apiUri . "/oauth2/token";

        $body   = self::urlEncoded([
            "grant_type"    => "client_credentials",
            "client_id"     => $appId,
            "client_secret" => $appSecret
        ], false);

        $response = self::doRequest($uri, $body, "post");

        if($response->code !== 200) throw new \Exception("API responded with a HTTP status of {$response->code}.");

        return $response->body;
    }

    /**
     * Returns up to 1000 businesses based on the provided search criteria.
     * An API call will return a maximum of 50 businesses - use the offset
     * parameter to retrieve businesses beyond the initial maximum of 50.
     * @see https://www.yelp.com/developers/documentation/v3/business_search
     *
     * @param   array       $params     Business search parameters
     * @return  object                  Httpful response object body
     * @throws  \Exception              Contains HTTP error code returned by Httpful
     */
    public function searchBusiness($params)
    {
        // merge search parameter defaults with supplied options
        $keyPairs       = $this->mergeParams($this->searchDefaults, $params);

        // build querystring containing only populated key/value pairs
        $queryString    = self::urlEncoded($keyPairs, false);

        $uri            = self::$apiUri . "/v3/businesses/search?$queryString";
        $response       = self::doRequest($uri, "", "get", $this->apiBearer, $this->apiToken);

        if($response->code !== 200) throw new \Exception("API responded with a HTTP status of {$response->code}.");

        return $response->body;
    }

    /**
     * Returns a list of businesses based on the provided phone number.
     * Accepted format: +10123456789
     * @see https://www.yelp.com/developers/documentation/v3/business_search_phone
     *
     * @param   string      $phone      Telephone number
     * @return  object                  Httpful response object body
     * @throws  \Exception              Contains HTTP error code returned by Httpful
     */
    public function searchPhone($phone)
    {
        if(!preg_match("/\+[0-9]{11}/", $phone))
            throw new \Exception("$phone is not a valid phone number.");

            $queryString    = "phone=$phone";
            $uri            = self::$apiUri . "/v3/businesses/search/phone?$queryString";
            $response       = self::doRequest($uri, "", "get", $this->apiBearer, $this->apiToken);

            if($response->code !== 200) throw new \Exception("API responded with a HTTP status of {$response->code}.");

            return $response->body;
    }

    /**
     * Returns a list of businesses which support certain transactions.
     * @see https://www.yelp.com/developers/documentation/v3/transactions_search
     *
     * @param   string      $transactionType    Transaction type, defaults to "delivery"
     * @param   array       $params             Transaction search parameters
     * @return  object                          Httpful response object body
     * @throws  \Exception                      Contains HTTP error code returned by Httpful
     */
    public function searchTransaction($transactionType = "delivery", $params)
    {
        // merge search parameter defaults with supplied options
        if(!$params)
            throw new \Exception("Latitude and Longitude are required parameters.");

        $keyPairs       = $this->mergeParams($this->transactionDefaults, $params);

        // build querystring containing only populated key/value pairs
        $queryString    = self::urlEncoded($keyPairs, false);

        $uri            = self::$apiUri . "/v3/transactions/$transactionType/search?$queryString";
        $response       = self::doRequest($uri, "", "get", $this->apiBearer, $this->apiToken);

        if($response->code !== 200)
            throw new \Exception("API responded with a HTTP status of {$response->code}.");

        return $response->body;
    }

    /**
     * Returns the detail information or up to 3 reviews of a business by id.
     * Business ids may be retrieved by the business, phone and transaction searches.
     * @see https://www.yelp.com/developers/documentation/v3/business
     * @see https://www.yelp.com/developers/documentation/v3/business_reviews
     *
     * @param   string      $infoType       Determines response contents (details/review)s
     * @param   string      $businessId     Valid Yelp business id
     * @param   array       $params         Business review parameters
     * @return  object                      Httpful response object body
     * @throws  \Exception                  Contains HTTP error code returned by Httpful
     */
    public function getDetails($infoType = "details", $businessId, $params = null)
    {
        $uri = self::$apiUri . "/v3/businesses/{$businessId}";
        if($infoType == "reviews") $uri .= "/reviews";

        if(is_array($params)) {
            // merge search parameter defaults with supplied options
            $keyPairs       = $this->mergeParams($this->reviewDefaults, $params);
            $queryString    = self::urlEncoded($keyPairs, false);
            $uri .= "?$queryString";
        }

        $response = self::doRequest($uri, "", "get", $this->apiBearer, $this->apiToken);

        if($response->code !== 200)
            throw new \Exception("API responded with a HTTP status of {$response->code}.");

        return $response->body;
    }

    /**
     * Returns autocomplete suggestions for search keywords, businesses and categories, based on supplied text parameter.
     * @see https://www.yelp.com/developers/documentation/v3/autocomplete
     *
     * @param   array     $params       Autocomplete search parameters
     * @return  object                  Httpful response object body
     * @throws  \Exception              Contains HTTP error code returned by Httpful
     */
    public function autoComplete($params)
    {
        if(!isset($params["text"]) || !isset($params["latitude"]) || !isset($params["longitude"]))
            throw new \Exception("Text, Latitude and Longitude are required parameters.");

        // merge search parameter defaults with supplied options
        $keyPairs       = $this->mergeParams($this->autoCompleteDefaults, $params);

        // build querystring containing only populated key/value pairs
        $queryString    = self::urlEncoded($keyPairs, false);

        $uri            = self::$apiUri . "/v3/autocomplete?$queryString";
        $response       = self::doRequest($uri, "", "get", $this->apiBearer, $this->apiToken);

        if($response->code !== 200)
            throw new \Exception("API responded with a HTTP status of {$response->code}.");

        return $response->body;
    }

    /**
     * Merges supplied parameters with defaults and prunes parameters that are not defined in the defaults.
     *
     * @param   array   $defaults   default array
     * @param   array   $params     supplied array
     * @return  array               merged/pruned array
     */
    private function mergeParams($defaults, $params)
    {
        $keyPairs = array_replace_recursive($defaults, $params);
        $keyPairs = array_filter(array_intersect_key($keyPairs, $defaults));

        return $keyPairs;
    }

    /**
     * Sends HTTP request via HTTPful.
     * @uses nategood/httpful (http://github.com/nategood/httpful)
     *
     * @param   string    $uri        endpoint uri
     * @param   mixed     $payload    request body
     * @param   string    $method     request method (get, post, etc.)
     * @param   string    $bearer     bearer string
     * @param   string    $token        api token
     * @return  object               Httpful response object
     */
    private static function doRequest($uri, $payload = null, $method = "get", $bearer = null, $token = null)
    {
        $request = Httpful\Request::$method($uri)->contentType("form");

        // add authorization header and/or payload if necessary
        if ($bearer && $token) $request->addHeader('Authorization', "$bearer $token");
        if ($payload) $request->body($payload);

        return $request->send();
    }

    /**
     * Converts array of key-value pairs in to an application/x-www-form-urlencoded string.
     *
     * @param   array       $keypairs   key-value pairs
     * @param   boolean     $encode     set to true to urlencode() string (optional)
     * @return  string                  encoded string
     */
    private static function urlEncoded($keypairs, $encode = null)
    {
        $urlEncoded = "";

        // loop through array, creating string of key/val pairs and remove first ampersand
        foreach ($keypairs as $key => $val) $urlEncoded .= "&$key=$val";
        $urlEncoded = ltrim($urlEncoded, "&");

        return $encode ? urlencode($urlEncoded) : $urlEncoded;
    }

}
