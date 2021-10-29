<?php

namespace RistekUSDI\ServiceAccount;

use RistekUSDI\ServiceAccount\Base;

class Role
{
    private $token;
    private $realm;

    public function __construct()
    {
        $base = new Base();
        $this->token = $base->getToken();
        $this->realm = $base->getRealm();
    }

    public function delete($role_id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $_ENV['SSO_BASE_URL']."/admin/realms/{$this->realm}/roles-by-id/{$role_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->token,
                'Content-Type: application/json'
            ),
        ));

        $raw_response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return array(
            'data' => json_decode($raw_response, true),
            'code' => (int) $httpcode
        );
    }
}