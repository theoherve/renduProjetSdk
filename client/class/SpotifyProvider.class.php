<?php
require(".env.php");

class SpotifyProvider{
	
	public static function Spotify(){
		return new Provider(SPOTIFY_CLIENT_ID, SPOTIFY_CLIENT_SECRET, "user-read-email", "POST", "https://accounts.spotify.com/api/token", "https://api.spotify.com/v1/me");
	}
	
}