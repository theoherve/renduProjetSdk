<?php

class OauthProvider{
	
	public static function Oauth(): Provider
	{
		return new Provider(OAUTH_CLIENT_ID, OAUTH_CLIENT_SECRET, "http://localhost:8081/callback","basic", "GET", "http://server:8080/token?", "http://server:8080/me");
	}
	
}