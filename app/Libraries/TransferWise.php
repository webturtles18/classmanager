<?php
namespace App\Libraries;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * @class   TransferWise
 * @desc    This class is the base class of TransferWise API Library
 */
class TransferWise {

    public $url = "";
    public $url_v3 = "";
    protected $api_token = "";
    protected $default_headers = [
                    'Accept' => 'application/json',
                ];
    protected $client;
    protected $auth_header = "";

    private $_log_dir = "";
    private $_log_mode = true;
    private $_beautify_log = false;
    
    public function __construct() {
        $this->client = new Client(['headers' => $this->default_headers]);
        
        $this->api_token = config('transferwise.token','');
        $this->url = config('transferwise.url','');
        $this->url_v3 = config('transferwise.url_v3','');
        
        $this->setLogDir();
        $this->setAuthHeader();
    }
    
    public function setLogDir($dir = ""){
        
        if(!empty($dir))
        {
            $log_dir = trim($dir,"/")."/";
        }
        else
        {
            $log_dir = __DIR__."/log/";
        }
        $year_dir = date("Y");
        $month_dir = date("F");
        $file_dir = $log_dir.$year_dir."/".$month_dir."/";
        
        if (!file_exists($file_dir)) {
            mkdir($file_dir, 0777, true);
	}
        
        $this->_log_dir = $file_dir;
    }
    
    public function setAuthHeader(){
        $this->auth_header = "Bearer {$this->api_token}";
    }
    
    public function getAuthHeader(){
        return $this->auth_header;
    }

    public function log($string,$action,$type = null) {
        
        if($this->_log_mode == false){ return false; }
        
        $action = ucfirst($action);
        $file_name = "api_log.txt";
        
        $string_tail = "\n===========================================================================================\n";
        
        $dir = $this->_log_dir;
        if (!file_exists($dir)) {
            mkdir($dir, 0777);
        }
        $datetime = date("Y-m-d H:i:s");
        
        if($this->_beautify_log == true){
            $string = print_r($string,TRUE);
        }
        else{
            $string = json_encode($string);
        }
        
        $string = "[{$datetime} {$action} {$type}]: {$string}".$string_tail;
        $file_path = $dir.DS.$file_name;
        
        if(file_exists($file_path)){
            $file_size = filesize($file_path);
            if($file_size > (1024 * 512))
            {
                $new_file_name = "api_log_". time().".txt";
                $new_file_path = $dir.DS.$new_file_name;
                rename($file_path, $new_file_path);
            }
        }
        
        $fp = fopen($file_path, "a+");
        fwrite($fp, $string);
        fclose($fp);
    }
    
    public function setLogMode(bool $log_mode){
        if(is_bool($log_mode)){
            $this->_log_mode = $log_mode;
        }
    }
    
    public function beautifyLog(){
        $this->_beautify_log = TRUE;
    }
    
    public function getRequest($action){
        
        try {
            $request_url = $this->url.$action;
            
            $this->log($request_url, $action);
            
            $options['headers'] = ['Authorization' => $this->getAuthHeader()];
            
            $response = $this->client->get($request_url, $options);
            
            $content = json_decode($response->getBody()->getContents(),TRUE);
            
            $this->log($content, $action);

            if (JSON_ERROR_NONE !== json_last_error()) {
                return [
                    'type' => "error",
                    'message' => "json_decode error: ".json_last_error_msg(),
                    'json_data' => $response->getBody()
                ];
            }
            
            return $content;
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function postRequest($action, $postdata = null){
        
        try {
            $request_url = $this->url.$action;
            
            $options['headers'] = ['Authorization' => $this->getAuthHeader()];
            if(!empty($postdata)){
                $options['json'] = $postdata;
            }
            
            $response = $this->client->post($request_url, $options);
            $content = json_decode($response->getBody()->getContents(),TRUE);

            if (JSON_ERROR_NONE !== json_last_error()) {
                return [
                    'type' => "error",
                    'message' => "json_decode error: ".json_last_error_msg(),
                    'json_data' => $response->getBody()
                ];
            }
            
            return $content;
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function payout($profile_id, $transfer_id, $postdata = null){
        
        try {
            $action = "profiles/{$profile_id}/transfers/{$transfer_id}/payments";
            $request_url = $this->url_v3.$action;
            
            $options['headers'] = ['Authorization' => $this->getAuthHeader()];
            if(!empty($postdata)){
                $options['json'] = $postdata;
            }
            
            $response = $this->client->post($request_url, $options);
            $content = json_decode($response->getBody()->getContents(),TRUE);

            if (JSON_ERROR_NONE !== json_last_error()) {
                return [
                    'type' => "error",
                    'message' => "json_decode error: ".json_last_error_msg(),
                    'json_data' => $response->getBody()
                ];
            }
            
            return $content;
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}