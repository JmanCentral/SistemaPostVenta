<?php
class ZeroBounceService {
    private $apiKey;
    
    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }
    
    public function validateEmail($email, $ipAddress = '') {
        $url = "https://api.zerobounce.net/v2/validate";
        $params = [
            'api_key' => $this->apiKey,
            'email' => $email,
            'ip_address' => $ipAddress
        ];
        
        $queryString = http_build_query($params);
        $fullUrl = $url . '?' . $queryString;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}
?>