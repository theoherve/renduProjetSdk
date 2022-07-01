<?php
require(".env.php");

function login(): void
{
  //OAuth login
	$queryParams= http_build_query([
       'client_id' => OAUTH_CLIENT_ID,
       'redirect_uri' => 'http://localhost:8081/callback',
       'response_type' => 'code',
       'scope' => 'basic',
       "state" => bin2hex(random_bytes(16))
   ]);
	echo "
        <form action='/callback' method='post'>
            <input type='text' name='username'/>
            <input type='password' name='password'/>
            <input type='submit' value='Login'/>
        </form>
    ";
	echo "<a href=\"http://localhost:8080/auth?{$queryParams}\">Login with OauthServer</a><br><br>";
	
	//Facebook login
	$queryParams= http_build_query([
       'client_id' => FACEBOOK_CLIENT_ID,
       'redirect_uri' => 'http://localhost:8081/fb_callback',
       'response_type' => 'code',
       'scope' => 'public_profile, email',
       "state" => bin2hex(random_bytes(16))
   ]);
	echo "<a href=\"https://www.facebook.com/v2.10/dialog/oauth?{$queryParams}\">Login with Facebook</a><br><br>";
	
	//Spotify login
	$queryParams= http_build_query([
       'client_id' => SPOTIFY_CLIENT_ID,
       'redirect_uri' => 'http://localhost:8081/spotify_callback',
       'response_type' => 'code',
       'show_dialog' => 'true', //not necessary, but useful for debugging
       'scope' => 'user-read-email',
       "state" => bin2hex(random_bytes(16))
   ]);
	echo "<a href=\"https://accounts.spotify.com/authorize?{$queryParams}\">Login with Spotify</a><br><br>";
  
  //Discord login
  $queryParams= http_build_query([
      'client_id' => DISCORD_CLIENT_ID,
      'redirect_uri' => 'http://localhost:8081/discord_callback',
      'response_type' => 'code',
      'scope' => 'identify email',
      "state" => bin2hex(random_bytes(16))
  ]);
  echo "<a href=\"https://discord.com/api/oauth2/authorize?{$queryParams}\">Login with Discord</a>";
}

// Exchange code for token then get user info
function callback(): void
{
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		["username" => $username, "password" => $password] = $_POST;
		$specifParams = [
			'username' => $username,
			'password' => $password,
			'grant_type' => 'password',
		];
	} else {
		["code" => $code, "state" => $state] = $_GET;
		
		$specifParams = [
			'code' => $code,
			'grant_type' => 'authorization_code',
		];
	}
	
	$queryParams = http_build_query(array_merge([
        'client_id' => OAUTH_CLIENT_ID,
        'client_secret' => OAUTH_CLIENT_SECRET,
        'redirect_uri' => 'http://localhost:8081/callback',
    ], $specifParams));
	$response = file_get_contents("http://server:8080/token?{$queryParams}");
	$token = json_decode($response, true);
	
	$context = stream_context_create([
         'http' => [
             'header' => "Authorization: Bearer {$token['access_token']}"
         ]
    ]);
	$response = file_get_contents("http://server:8080/me", false, $context);
	$user = json_decode($response, true);
	echo "Hello {$user['lastname']} {$user['firstname']}";
}

//Facebook callback
function fbcallback(): void
{
	["code" => $code, "state" => $state] = $_GET;
	
	$specifParams = [
		'code' => $code,
		'grant_type' => 'authorization_code',
	];
	
	$queryParams = http_build_query(array_merge([
        'client_id' => FACEBOOK_CLIENT_ID,
        'client_secret' => FACEBOOK_CLIENT_SECRET,
        'redirect_uri' => 'http://localhost:8081/fb_callback',
    ], $specifParams));
	
	$response = file_get_contents("https://graph.facebook.com/v2.10/oauth/access_token?{$queryParams}");
	$token = json_decode($response, true);
	
	$context = stream_context_create([
         'http' => [
             'header' => "Authorization: Bearer {$token['access_token']}"
         ]
     ]);
	$response = file_get_contents("https://graph.facebook.com/v2.10/me", false, $context);
	$user = json_decode($response, true);
	echo "Hello {$user['name']}";
}

//Spotify callback
function spotifyCallback(): void
{
	["code" => $code, "state" => $state] = $_GET;
	
	$specifParams = [
		'code' => $code,
		'grant_type' => 'authorization_code',
	];
	
	$queryParams = http_build_query(array_merge([
        'client_id' => SPOTIFY_CLIENT_ID,
        'client_secret' => SPOTIFY_CLIENT_SECRET,
        'redirect_uri' => 'http://localhost:8081/spotify_callback',
    ], $specifParams));
	
	$context_options = array (
		'http' => array (
			'method' => 'POST',
			'header'=> "Content-type: application/x-www-form-urlencoded\r\n"
				. "Content-Length: " . strlen($queryParams) . "\r\n",
			'content' => $queryParams
		)
	);
	
	$response = file_get_contents("https://accounts.spotify.com/api/token", false, stream_context_create($context_options));
	$token = json_decode($response, true);
	
	$context = stream_context_create([
         'http' => [
             'header' => "Authorization: Bearer {$token['access_token']}"
         ]
     ]);
	$response = file_get_contents("https://api.spotify.com/v1/me", false, $context);
	$user = json_decode($response, true);
	echo "Hello {$user['email']} {$user['display_name']}";
	
//	return new user($user['email'], $user['id']);

}

function discord_callback()
{
    ["code" => $code, "state" => $state] = $_GET;

    $specifParams = [
        'code' => $code,
        'grant_type' => 'authorization_code'
    ];

    $queryParams = http_build_query(array_merge([
        'client_id' => DISCORD_CLIENT_ID,
        'client_secret' => DISCORD_CLIENT_SECRET,
        'redirect_uri' => 'http://localhost:8081/discord_callback',
    ], $specifParams));

    $context_options = array (
        'http' => array (
            'method' => 'POST',
            'header'=> "Content-type: application/x-www-form-urlencoded",
            'content' => $queryParams
        )
    );

    $response = file_get_contents("https://discord.com/api/oauth2/token", false, stream_context_create($context_options));
    $token = json_decode($response, true);

    $context = stream_context_create([
        'http' => [
            'header' => "Authorization: Bearer {$token['access_token']}"
        ]
    ]);
    $response = file_get_contents("https://discord.com/api/oauth2/@me", false, $context);
    $user = json_decode($response, true);
    var_dump($user['user']['username']);
}

$route = $_SERVER["REQUEST_URI"];
switch (strtok($route, "?")) {
	case '/login':
		login();
		break;
	case '/callback':
		callback();
		break;
	case '/fb_callback':
		fbcallback();
		break;
	case '/spotify_callback':
		spotifyCallback();
		break;
  case '/discord_callback':
      discord_callback();
      break;
	default:
		http_response_code(404);
		break;
}
