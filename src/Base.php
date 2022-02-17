<?php

namespace RistekUSDI\ServiceAccount;

class Base
{
    private $username;
    private $password;
    private $realm;

    public function __construct()
    {
        $this->username = isset($_ENV['SSO_CLIENT_ID']) ? $_ENV['SSO_CLIENT_ID'] : null;
        $this->password = isset($_ENV['SSO_CLIENT_SECRET']) ? $_ENV['SSO_CLIENT_SECRET'] : null;
        $this->realm = isset($_ENV['SSO_REALM']) ? $_ENV['SSO_REALM'] : null;
    }

    public function getRealm()
    {
        return $this->realm;
    }

    public function getToken()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $_ENV['SSO_BASE_URL']."/realms/{$this->realm}/protocol/openid-connect/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
            CURLOPT_USERPWD => $this->username.":".$this->password
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        curl_close($curl);

        $access_token = '';

        if ($httpcode === 200) {
            $decode_response = json_decode($response, true);
            $access_token = $decode_response['access_token'];
        }

        return $access_token;
    }

    public function isTokenActive($token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $_ENV['SSO_BASE_URL']."/realms/{$this->realm}/protocol/openid-connect/token/introspect",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "token_type_hint=requesting_party_token&token={$token}",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
            CURLOPT_USERPWD => $this->username.":".$this->password
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        curl_close($curl);

        $result = false;

        if ($httpcode === 200) {
            $decode_response = json_decode($response, true);
            if (isset($decode_response['active'])) {
                if ($decode_response['active'] == true) {
                    $result = true;
                }
            }
        }

        return $result;
    }
}