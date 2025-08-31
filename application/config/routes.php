<?php
defined('BASEPATH') or exit('No direct script access allowed');
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['login']['POST'] = 'auth/login';
$route['auth/me']['GET'] = 'auth/me';

$route['users']['GET'] = 'users/index';
$route['users/(:any)']['GET']      = 'users/show/$1';    // detail user
$route['users']['POST']            = 'users/store';      // tambah user
$route['users/(:any)']['PUT']      = 'users/update/$1';  // update user
$route['users/(:any)']['DELETE']   = 'users/destroy/$1'; // hapus user

$route['incoming/ecertin']['GET'] = 'incoming/ecertin';
$route['incoming/ephytoin']['GET'] = 'incoming/ephytoin';
$route['outgoing/ecertout']['GET'] = 'outgoing/ecertout';
$route['outgoing/ephytoout']['GET'] = 'outgoing/ephytoout';
$route['dashboard/stats']['GET'] = 'dashboard/stats';
$route['dashboard/tabledata']['GET'] = 'dashboard/tabledata';
$route['dashboard/monthly']['GET'] = 'dashboard/monthly';

$route['countryset'] = 'countryset/index';
$route['countryset/(:num)'] = 'countryset/index/$1';
