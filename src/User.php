<?php

namespace RistekUSDI\ServiceAccount;

use RistekUSDI\ServiceAccount\Base;

class User
{
    private $token;
    private $realm;

    public function __construct()
    {
        $base = new Base();
        $this->token = $base->getToken();
        $this->realm = $base->getRealm();
    }

    public function get($params)
    {
        $curl = curl_init();

        $query = '';
        if (isset($params)) {
            $query = http_build_query($params);
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $_ENV['SSO_BASE_URL']."/admin/realms/{$this->realm}/users?".$query,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->token
            ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $users = [];

        if ($httpcode === 200) {
            $users = json_decode($response, true);
        }

        return $users;
    }

    public function getImpersonateUrl($data)
    {
        $users = $this->get($data);
        
        $user_id = (!empty($users)) ? $users[0]['id'] : '';

        if (empty($user_id)) {
            return response()->json(array(
                'status' => 'error',
                'message' => 'Cannot impersonate user because user not found!'
            ), 401);
        }

        return response()->json(array(
            'status' => 'ok',
            'impersonate_url' => $_ENV['SSO_BASE_URL']."/admin/realms/{$this->realm}/users/{$user_id}/impersonation",
            'access_token' => $this->token
        ), 200);
    }

    public function update($user_id, $data)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $_ENV['SSO_BASE_URL']."/admin/realms/{$this->realm}/users/{$user_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $users = [];

        if ($httpcode === 200) {
            $users = json_decode($response, true);
        }

        return $users;
    }

    public function getRawAvailableRoles($user_id, $client_id)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $_ENV['SSO_BASE_URL']."/admin/realms/{$this->realm}/users/{$user_id}/role-mappings/clients/{$client_id}/available",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->token
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

    public function getAvailableRoles($user_id, $client_id)
    {
        $raw_available_roles = $this->getRawAvailableRoles($user_id, $client_id);
        
        $roles = [];
        $filtered_roles = ['uma_protection'];
        
        foreach ($raw_available_roles as $raw_role) {
            if (!in_array($raw_role['name'], $filtered_roles)) {
                $roles[] = $raw_role;
            }  
        }

        return $roles;
    }

    public function getAssignedRoles($user_id, $client_id)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $_ENV['SSO_BASE_URL']."/admin/realms/{$this->realm}/users/{$user_id}/role-mappings/clients/{$client_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->token
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

    public function getEffectiveRoles($user_id, $client_id)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $_ENV['SSO_BASE_URL']."/admin/realms/{$this->realm}/users/{$user_id}/role-mappings/clients/{$client_id}/composite",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->token
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

    public function storeAssignedClientRoles($user_id, $client_id, $roles)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $_ENV['SSO_BASE_URL']."/admin/realms/{$this->realm}/users/{$user_id}/role-mappings/clients/{$client_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($roles),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $result = [];
        
        if ($httpcode === 204) {
            $result = json_decode($response, true);
        }

        return $result;
    }

    public function deleteAssignedClientRoles($user_id, $client_id, $roles)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $_ENV['SSO_BASE_URL']."/admin/realms/{$this->realm}/users/{$user_id}/role-mappings/clients/{$client_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_POSTFIELDS => json_encode($roles),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->token,
                'Content-Type: application/json'
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
}