<?php

namespace RistekUSDI\ServiceAccount;

use RistekUSDI\ServiceAccount\Base;

class Group extends Base
{
    /**
     * Get list of groups
     */
    public function get($params = array())
    {
        $curl = curl_init();

        $query = '';
        if (isset($params)) {
            $query = http_build_query($params);
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getAdminRealmUrl()."/groups?".$query,
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

    public function findById($id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "{$this->getAdminRealmUrl()}/groups/{$id}",
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

    public function getRawAvailableRoles($group_id, $client_id)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getAdminRealmUrl()."/groups/{$group_id}/role-mappings/clients/{$client_id}/available",
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

    public function getAvailableRoles($group_id, $client_id)
    {
        $raw_available_roles = $this->getRawAvailableRoles($group_id, $client_id);
        
        $roles = [];
        $filtered_roles = ['uma_protection'];
        
        foreach ($raw_available_roles as $raw_role) {
            if (!in_array($raw_role['name'], $filtered_roles)) {
                $roles[] = $raw_role;
            }  
        }

        return $roles;
    }

    public function getAssignedRoles($group_id, $client_id)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getAdminRealmUrl()."/groups/{$group_id}/role-mappings/clients/{$client_id}",
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

    public function getEffectiveRoles($group_id, $client_id)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getAdminRealmUrl()."/groups/{$group_id}/role-mappings/clients/{$client_id}/composite",
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

    public function storeAssignedClientRoles($group_id, $client_id, $roles)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getAdminRealmUrl()."/groups/{$group_id}/role-mappings/clients/{$client_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($roles),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->getToken(),
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

    public function deleteAssignedClientRoles($group_id, $client_id, $roles)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getAdminRealmUrl()."/groups/{$group_id}/role-mappings/clients/{$client_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_POSTFIELDS => json_encode($roles),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->getToken(),
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

    public function members($group_id, $params = array())
    {
        $curl = curl_init();

        $query = '';
        if (isset($params)) {
            $query = http_build_query($params);
        }
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getAdminRealmUrl()."/groups/{$group_id}/members?".$query,
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
     * Mendapatkan daftar kelompok berdasarkan role mapping client
     * @param $role_name, $client_id (id of client NOT client-id)
     * @return array of groups
     */
    public function getRoleMapping($role_name, $client_id)
    {
        $groups = flatten_groups($this->get());

        $filtered_groups = [];
        foreach ($groups as $group) {
            $assigned_roles = $this->getAssignedRoles($group['id'], $client_id);
            foreach ($assigned_roles as $assigned_role) {
                if ($assigned_role['name'] == $role_name) {
                    $filtered_groups[] = $group;
                }
            }
        }
        
        return $filtered_groups;
    }
}