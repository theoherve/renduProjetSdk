<?php
require(".env.php");
require("./class/Provider.class.php");
require("./class/User.class.php");
require("./class/SpotifyProvider.class.php");
require("./class/DiscordProvider.class.php");
require("./class/OauthProvider.class.php");
require("./class/FacebookProvider.class.php");

function login(): void
{
	echo "
        <form action='/callback' method='post'>
            <input type='text' name='username'/>
            <input type='password' name='password'/>
            <input type='submit' value='Login'/>
        </form>
    ";
	
	//OAuth login
	echo "<a href=\"http://localhost:8080/auth?" . OauthProvider::Oauth()->buildQuery() . "\">Login with OauthServer</a><br><br>";
	
	//Facebook login
	echo "<a href=\"https://www.facebook.com/v2.10/dialog/oauth?" . FacebookProvider::Facebook()->buildQuery() . "\">Login with Facebook</a><br><br>";
	
	//Spotify login
	echo "<a href=\"https://accounts.spotify.com/authorize?" . SpotifyProvider::Spotify()->buildQuery() . "\">Login with Spotify</a><br><br>";
  
    //Discord login
    echo "<a href=\"https://discord.com/api/oauth2/authorize?" . DiscordProvider::Discord()->buildQuery() . "\">Login with Discord</a>";
}

$route = $_SERVER["REQUEST_URI"];
switch (strtok($route, "?")) {
	case '/login':
		login();
		break;
	case '/callback':
		$user = OauthProvider::Oauth()->callback();
		print_r($user->getData());
		break;
	case '/fb_callback':
		$user = FacebookProvider::Facebook()->callback();
		print_r($user->getData());
		break;
	case '/spotify_callback':
        $user = SpotifyProvider::Spotify()->callback();
        print_r($user->getData("display_name", "email"));
		break;
  case '/discord_callback':
      $user = DiscordProvider::Discord()->callback();
      print_r($user->getData("username", "email"));
      break;
	default:
		http_response_code(404);
		break;
}
