<?php

namespace RistekUSDI\ServiceAccount;

use RistekUSDI\ServiceAccount\Base;

class Key extends Base
{
    public function get()
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getBaseUrl()."/admin/realms/{$this->getRealm()}/keys",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->getToken()
            ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $result = [];

        if ($httpcode === 200) {
            $result = json_decode($response, true);
        }

        return $result;
    }

    /**
     * Get RSA256 public key signature
     */
    public function getRSA256PublicKey()
    {
        $keys = $this->get();
        $available_keys = $keys['keys'];
        $public_key = null;

        foreach ($available_keys as $value) {
            if ($value['algorithm'] === 'RS256' && $value['use'] === 'SIG') {
                $public_key = $value['publicKey'];
            }
        }

        return $public_key;
    }
}