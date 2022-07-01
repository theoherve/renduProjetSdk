<?php

class FacebookProvider{
	
	public static function Facebook(): Provider
	{
		return new Provider(FACEBOOK_CLIENT_ID, FACEBOOK_CLIENT_SECRET, "http://localhost:8081/fb_callback","public_profile, email", "GET", "https://graph.facebook.com/v2.10/oauth/access_token?", "https://graph.facebook.com/v2.10/me");
	}
	
}