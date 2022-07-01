<?php

class Provider{
	private $clientID = null;
	private $clientSecret = null;
	private $scope = null;
	private $method = null;
	private $tokenUrl = null;
	private $urlMe = null;
	
	public function __construct($clientID, $clientSecret, $scope, $method, $tokenUrl, $urlMe)
	{
		$this->clientID = $clientID;
		$this->clientSecret = $clientSecret;
		$this->scope = $scope;
		$this->method = $method;
		$this->tokenUrl = $tokenUrl;
		$this->urlMe = $urlMe;
	}
	
	/**
	 * @param null $clientID
	 */
	public function setClientID($clientID): void
	{
		$this->clientID = $clientID;
	}
	
	/**
	 * @return null
	 */
	public function getClientID()
	{
		return $this->clientID;
	}
	
	/**
	 * @param null $clientSecret
	 */
	public function setClientSecret($clientSecret): void
	{
		$this->clientSecret = $clientSecret;
	}
	
	/**
	 * @return null
	 */
	public function getClientSecret()
	{
		return $this->clientSecret;
	}
	
	/**
	 * @return null
	 */
	public function getScope()
	{
		return $this->scope;
	}
	
	/**
	 * @param null $scope
	 */
	public function setScope($scope): void
	{
		$this->scope = $scope;
	}
	
	/**
	 * @return null
	 */
	public function getMethod()
	{
		return $this->method;
	}
	
	/**
	 * @param null $method
	 */
	public function setMethod($method): void
	{
		$this->method = $method;
	}
	
	/**
	 * @return null
	 */
	public function getTokenUrl()
	{
		return $this->tokenUrl;
	}
	
	/**
	 * @param null $urlToken
	 */
	public function setTokenUrl($urlToken): void
	{
		$this->tokenUrl = $$urlToken;
	}
	
	/**
	 * @return null
	 */
	public function getUrlMe()
	{
		return $this->urlMe;
	}
	
	public function buildQuery(){
		return http_build_query([
            'client_id' => $this->getClientID(),
            'redirect_uri' => "http://localhost:8081/callback",
            'response_type' => 'code',
            'scope' => $this->getScope(),
            "state" => bin2hex(random_bytes(16))
        ]);
	}
	
	public function getToken($method, $urlToken){
		["code" => $code, "state" => $state] = $_GET;
		
		$specifParams = [
			'code' => $code,
			'grant_type' => 'authorization_code',
		];
		
		$queryParams = http_build_query(array_merge([
            'client_id' => $this->getClientID(),
            'client_secret' => $this->getClientSecret(),
            'redirect_uri' => 'http://localhost:8081/callback',
        ], $specifParams));
		
		if($method === "POST"){
			$context_options = array (
				'http' => array (
					'method' => 'POST',
					'header'=> "Content-type: application/x-www-form-urlencoded\r\n"
						. "Content-Length: " . strlen($queryParams) . "\r\n",
					'content' => $queryParams
				)
			);
			
			$response = file_get_contents($urlToken, false, stream_context_create($context_options));
		}else if($method === "GET"){
			$response = file_get_contents($urlToken . $queryParams);
		}else
			echo "wrong method";
		
		return json_decode($response, true);
	}
	
	public function getMe($token, $urlMe) {
		$context = stream_context_create([
             'http' => [
                 'header' => "Authorization: Bearer {$token['access_token']}"
             ]
         ]);
		$response = file_get_contents( $urlMe, false, $context);
		return new User(json_decode($response, true));
	}
	
	public function callback(): User
	{
		$token = $this->getToken($this->getMethod(), $this->getTokenUrl());
		return $this->getMe($token, $this->getUrlMe());
	}
	
}