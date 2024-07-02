<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
*/

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->match(['get','post'], "/", "Login::index",['filter'=>'noauth']);
$routes->match(['get','post'], "login", "Login::index",['filter'=>'noauth']);
$routes->match(['get','post'], "dashboard", "Home::index",['filter'=>'auth']);
$routes->match(['get','post'], "lms", "Home::lms",['filter'=>'auth']);
$routes->match(['get','post'], "profile", "Login::profile",['filter'=>'auth']);
$routes->match(['get','post'], "callback", "Login::callback");

/******************* Menu's Managements **************/
// $routes->match(['get','post'], "menu_management", "Home::menu_management",['filter'=>'auth']);
// $routes->match(['get','post'], "fetch_role_details", "Home::fetch_role_details",['filter'=>'auth']);
// $routes->match(['get','post'], "fetch_action_details", "Home::fetch_action_details",['filter'=>'auth']);
// $routes->match(['get','post'], "fetch_staff_details", "Home::fetch_staff_details",['filter'=>'auth']);
// $routes->match(['get','post'], "menu_details", "Home::fetch_menu_details",['filter'=>'auth']);
// $routes->match(['get','post'], "row_reorder", "Home::row_reorder",['filter'=>'auth']);
// $routes->match(['get','post'], "add_menu", "Home::add_menu",['filter'=>'auth']);
// $routes->match(['get','post'], "disable_menu", "Home::disable_menu",['filter'=>'auth']);
// $routes->match(['get','post'], "update_menu", "Home::update_menu",['filter'=>'auth']);
// $routes->match(['get','post'], "fetch_sub_menu", "Home::fetch_sub_menu",['filter'=>'auth']);
// $routes->match(['get','post'], "fetch_menu_list", "Home::fetch_menu_list",['filter'=>'auth']);
// $routes->match(['get','post'], "add_menu_list", "Home::add_menu_list",['filter'=>'auth']);
// $routes->match(['get','post'], "fetch_role_list", "Home::fetch_role_list",['filter'=>'auth']);
// $routes->match(['get','post'], "fetch_roles", "Home::fetch_roles",['filter'=>'auth']);
// $routes->match(['get','post'], "fetch_all_companies", "Home::fetch_all_companies",['filter'=>'auth']);
// $routes->match(['get','post'], "fetch_all_menu", "Home::fetch_all_menu",['filter'=>'auth']);
// $routes->match(['get','post'], "fetch_menu_preference", "Home::fetch_menu_preference",['filter'=>'auth']);
// $routes->match(['get','post'], "change_password", "Home::change_password",['filter'=>'auth']);
// $routes->match(['get','post'], "is_password", "Home::is_password",['filter'=>'auth']);
// $routes->match(['get','post'], "menu_details", "Home::fetch_menu_details",['filter'=>'auth']);
// $routes->match(['get','post'], "menu_reorder", "Home::menu_reorder",['filter'=>'auth']);

// /****************** Custom send email test *******************/ 
// $routes->match(['get','post'], 'send_test', 'Home::send_test',['filter'=>'auth']);
// $routes->match(['get','post'], 'is_attendance_send', 'Home::is_attendance_send');
// $routes->match(['get','post'], 'is_calendar', 'Home::is_calendar',['filter'=>'auth']);
// $routes->match(['get','post'], 'is_reminder_send', 'Home::is_reminder_send');

/****************** Courses Logout Process **************/
$routes->match(['get','post'], "logout", "Login::logout",['filter'=>'auth']);
$routes->match(['get','post'], "error_404", "Home::error_404",['filter'=>'auth']);


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */

if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}