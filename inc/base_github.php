<?php

/**
 * 
 * Base class for github
 * 
 * Classname : base_github
 * Filename  : base_github.php
 * Author    : Vignesh
 * 
 */


if (!function_exists('curl_init')) {
  throw new Exception('Github needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('Github needs the JSON PHP extension.');
}

class base_github {

	
}

?>