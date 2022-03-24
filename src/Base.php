<?php

namespace RistekUSDI\ServiceAccount;

class Base
{
    private $admin_url;
    private $base_url;
    private $realm;
    private $client_id;
    private $client_secret;
    private $username;
    private $password;
    private $token;

    public function __construct($config = array())
    {
        $this->admin_url = $config['admin_url'];
        $this->base_url = $config['base_url'];
        $this->realm = $config['realm'];
        $this->client_id = $config['client_id'];
        $this->client_secret = $config['client_secret'];
        $this->token = isset($config['token']) ? $config['token'] : null;
    }

    public function getAdminRealmUrl()
    {
        return "$this->admin_url/admin/realms/{$this->getRealm()}";
    }

    public function getBaseRealmUrl()
    {
        return "$this->base_url/realms/{$this->getRealm()}";
    }

    public function getRealm()
    {
        return $this->realm;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    public function getClientSecret()
    {
        return $this->client_secret;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getToken()
    {
        if (!empty($this->token)) {
            return $this->token;
        } else {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "{$this->getBaseRealmUrl()}/protocol/openid-connect/token",
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
                CURLOPT_USERPWD => "{$this->getClientId()}:{$this->getClientSecret()}"
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
    }

    public function isTokenActive($token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "{$this->getBaseRealmUrl()}/protocol/openid-connect/token/introspect",
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
            CURLOPT_USERPWD => "{$this->getClientId()}:{$this->getClientSecret()}"
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