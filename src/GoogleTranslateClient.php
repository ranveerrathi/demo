<?php 

namespace App;

use App\Services\HttpClientService;

class GoogleTranslateClient{

    private static $instances = [];

    protected function __construct() { }
 
    protected function __clone() { }
 
   
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance(): GoogleTranslateClient
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }
        return self::$instances[$cls];
        
    }
    public function getTranslate($q,$target,$source){
        $url = $this->getBaseUrl();
        $dataArray = ["key"=>$this->getToken(),"q"=>$q,"target"=>$target,"source"=>$source];
        $data = http_build_query($dataArray);
        $getUrl = $url."?".$data;
        $response = HttpClientService::doGet($getUrl);
        return $response;
    }
    public function getBaseUrl():string
    {
        return $_ENV['GOOGLE_BASE_URL'];
    }
    public function getToken():string
    {
        return $_ENV['GOOGLE_TOKEN'];
    }
}
