<?php

if (! function_exists('flatten_groups')) {
    function flatten_groups(array $array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (!empty($value['subGroups'])) {
                $result = array_merge($result, flatten_groups($value['subGroups']));
            }
            $result[] = array(
                'id' => $value['id'],
                'name' => $value['name'],
                'path' => $value['path']
            );
        }
        return $result;
    }
}