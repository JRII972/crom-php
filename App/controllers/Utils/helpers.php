<?php 

if (!function_exists('route')) {
    function route($name, $parameters = []) {
        $routes = [
            'activite.show' => '/activite?id=%d',
            'activite.create' => '/activite?action=create',
            'activite.edit' => '/activites?action=edit&id=%d',
            'receipts.show' => '/receipts?id=%d',
            'payments.pay' => '/payments?action=pay&id=%d',
            'payments.renew' => '/payments?action=renew'
        ];
        
        if (!isset($routes[$name])) {
            return '#';
        }
        
        $url = $routes[$name];
        if (!empty($parameters) && is_array($parameters)) {
            $args = array_values($parameters);
            $url = vsprintf($url, $args);
        }
        
        return $url;
    }
}

// Define isSessionDisplay helper if not exists
if (!function_exists('isSessionDisplay')) {
    function isSessionDisplay($object) {
        return $object instanceof \App\Controllers\Class\SessionDisplay;
    }
}