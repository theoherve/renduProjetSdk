<?php

abstract class Provider{
	private $clientID = null;
	private $clientSecret = null;
	private $redirectUri = null;
	private $scope = null;
	
	public function __construct($clientID, $clientSecret, $redirectUri, $scope)
	{
		$this->clientID = $clientID;
		$this->clientSecret = $clientSecret;
		$this->redirectUri = $redirectUri;
		$this->scope = $scope;
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
	public function getRedirectUri()
	{
		return $this->redirectUri;
	}
	
	/**
	 * @param null $redirectUri
	 */
	public function setUrl($redirectUri): void
	{
		$this->redirectUri = $redirectUri;
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
	
	public function buildQuery(){
		return http_build_query([
            'client_id' => $this->getClientID(),
            'redirect_uri' => $this->getRedirectUri(),
            'response_type' => 'code',
            'scope' => $this->getScope(),
            "state" => bin2hex(random_bytes(16))
        ]);
	}
	
}