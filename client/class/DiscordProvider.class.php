<?php

class DiscordProvider{

    public static function Discord(){
        return new Provider(DISCORD_CLIENT_ID, DISCORD_CLIENT_SECRET, "http://localhost:8081/discord_callback", "identify email", "POST", "https://discord.com/api/oauth2/token", "https://discord.com/api/oauth2/@me");
    }

}