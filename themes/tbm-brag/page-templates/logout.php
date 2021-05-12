<?php
/*
 * Template Name: Logout
 */

require_once(  ABSPATH . 'sso-sp/simplesaml/lib/_autoload.php');
$auth = new SimpleSAML_Auth_Simple('default-sp');

wp_logout();
$auth->logout( wp_get_referer() ? : home_url() );
