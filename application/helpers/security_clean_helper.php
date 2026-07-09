<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('sanitize_input')) {
    
    function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data); 
    }
    return html_escape(trim($data));
}
}