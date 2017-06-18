<?php
class YelpTest extends PHPUnit_Framework_TestCase
{
    /**
     * Yelp App ID and Secret must be placed in phpunit.xml for tests to work locally.
     */
    protected $client_id;
    protected $client_secret;

    protected function setUp() {
        $this->client_id = $_ENV["client_id"];
        $this->client_secret = $_ENV["client_secret"];

        var_dump($_ENV);
    }

    /**
     * Asserts returned object has 'access_token', 'token_type', and 'expires_in' attributes
     */
    public function testAuth(){
        $stack = [];
        $result = \TVW\Yelp::bearerRequest($this->client_id, $this->client_secret);
        $this->assertObjectHasAttribute('access_token', $result);
        $this->assertObjectHasAttribute('token_type', $result);
        $this->assertObjectHasAttribute('expires_in', $result);

        $stack["client_token"] =  $result->access_token;
        return $stack;
    }

    /**
     * Exception generated by missing id and secret parameters
     */
    public function testAuthException(){
        $this->expectException(Exception::Class);
        $result = \TVW\Yelp::bearerRequest("", "");
    }

    /**
     * Asserts returned object has a 'apiToken' attribute
     * @depends testAuth
     * @param array $stack
     */
    public function testInit(array $stack) {
        $yelpFusion = new \TVW\Yelp($stack["client_token"]);
        $this->assertObjectHasAttribute('apiToken', $yelpFusion);
    }

    /**
     * Exception generated by missing api token parameter
     */
    public function testInitException() {
        $this->expectException(Exception::Class);
        $yelpFusion = new \TVW\Yelp("");
    }

    /**
     * Asserts returned object has a 'businesses' attribute
     * @depends testAuth
     * @param array $stack
     */
    public function testBusinessSearch(array $stack){
        $testParams = [
            "location"      => "10001",
            "radius"        => "500",
            "sort_by"       => "distance",
            "categories"    => "restaurants",
            "limit"         => 5
        ];
        $yelpFusion = new \TVW\Yelp($stack["client_token"]);
        $results = $yelpFusion->searchBusiness($testParams);
        $this->assertObjectHasAttribute('businesses', $results);
    }

    /**
     * Exception generated by limit > 50.
     * @depends testAuth
     * @param array $stack
     */
    public function testBusinessSearchException(array $stack){
        $this->expectException(Exception::Class);
        $testParams = [
            "location"      => "10001",
            "radius"        => "500",
            "sort_by"       => "distance",
            "categories"    => "restaurants",
            "limit"         => 500
        ];
        $yelpFusion = new \TVW\Yelp($stack["client_token"]);
        $results = $yelpFusion->searchBusiness($testParams);
    }

    /**
     * Asserts returned object has a 'businesses' attribute
     * @depends testAuth
     * @param array $stack
     */
    public function testPhoneSearch(array $stack){
        $yelpFusion = new \TVW\Yelp($stack["client_token"]);
        $result = $yelpFusion->searchPhone("+12127527470");
        $this->assertObjectHasAttribute('businesses', $result);
    }

    /**
     * Exception generated by improperly formatted phone number
     * @depends testAuth
     * @param array $stack
     */
    public function testPhoneSearchException(array $stack){
        $this->expectException(Exception::Class);
        $yelpFusion = new \TVW\Yelp($stack["client_token"]);
        $result = $yelpFusion->searchPhone("12127527470");
    }

    /**
     * Asserts returned object has a 'businesses' attribute
     * @depends testAuth
     * @param array $stack
     */
    public function testTransactionSearch(array $stack){
        $transactionParams = [
            "latitude"      => "40.730610",
            "longitude"     => "-73.935242"
        ];
        $yelpFusion = new \TVW\Yelp($stack["client_token"]);
        $result = $yelpFusion->searchTransaction("delivery", $transactionParams);
        $this->assertObjectHasAttribute('businesses', $result);
    }

    /**
     * Exception generated by missing parameters
     * @depends testAuth
     * @param array $stack
     */
    public function testTransactionSearchException(array $stack){
        $transactionParams = [];
        $this->expectException(Exception::Class);
        $yelpFusion = new \TVW\Yelp($stack["client_token"]);
        $result = $yelpFusion->searchTransaction("delivery", $transactionParams);
    }

    /**
     * Asserts returned object has a 'id' attribute
     * @depends testAuth
     * @param array $stack
     */
    public function testBusiness(array $stack){
        $yelpFusion = new \TVW\Yelp($stack["client_token"]);
        $result = $yelpFusion->getBusiness("blue-hill-new-york");
        $this->assertObjectHasAttribute('id', $result);
    }

    /**
     * Exception generated by invalid business id
     * @depends testAuth
     * @param array $stack
     */
    public function testBusinessException(array $stack){
        $this->expectException(Exception::Class);
        $yelpFusion = new \TVW\Yelp($stack["client_token"]);
        $result = $yelpFusion->getBusiness("invalid-business-id");
    }

    /**
     * Asserts returned object has a 'reviews' attribute
     * @depends testAuth
     * @param array $stack
     */
    public function testBusinessReviews(array $stack){
        $yelpFusion = new \TVW\Yelp($stack["client_token"]);
        $result = $yelpFusion->getReviews("blue-hill-new-york");
        $this->assertObjectHasAttribute('reviews', $result);
    }

    /**
     * Exception generated by invalid business id
     * @depends testAuth
     * @param array $stack
     */
    public function testBusinessReviewsException(array $stack){
        $this->expectException(Exception::Class);
        $yelpFusion = new \TVW\Yelp($stack["client_token"]);
        $result = $yelpFusion->getReviews("invalid-business-id");
    }

    /**
     * Asserts returned object has a 'businesses' attribute
     * @depends testAuth
     * @param array $stack
     */
    public function testAutoCompleteSearch(array $stack){
        $autoCompleteParams = [
            "text"          => "atomic",
            "latitude"      => "40.730610",
            "longitude"     => "-73.935242"
        ];
        $yelpFusion = new \TVW\Yelp($stack["client_token"]);
        $result = $yelpFusion->autoComplete($autoCompleteParams);
        $this->assertObjectHasAttribute('businesses', $result);
    }

    /**
     * Exception generated by missing parameters
     * @depends testAuth
     * @param array $stack
     */
    public function testAutoCompleteSearchException(array $stack){
        $autoCompleteParams = [];
        $this->expectException(Exception::Class);
        $yelpFusion = new \TVW\Yelp($stack["client_token"]);
        $result = $yelpFusion->autoComplete($autoCompleteParams);
    }

}
