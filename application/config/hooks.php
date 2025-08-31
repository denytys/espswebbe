<?php
defined('BASEPATH') or exit('No direct script access allowed');

$hook['post_controller_constructor'][] = array(
    'class'    => 'Cors',
    'function' => 'enable',
    'filename' => 'Cors.php',
    'filepath' => 'hooks'
);


/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/userguide3/general/hooks.html
|
*/
