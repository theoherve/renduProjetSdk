<?php

class SpotifyProvider{
	
	public static function Spotify(): Provider
	{
		return new Provider(SPOTIFY_CLIENT_ID, SPOTIFY_CLIENT_SECRET, "http://localhost:8081/spotify_callback","user-read-email", "POST", "https://accounts.spotify.com/api/token", "https://api.spotify.com/v1/me");
	}
	
}