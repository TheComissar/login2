<?php
/**
 * Created by PhpStorm.
 * User: Master
 * Date: 3/27/2017
 * Time: 9:43 PM
 */
use \Sessions\PersistentSessionHandler;

require_once __DIR__ . '/Psr4AutoloaderClass.php';
require_once __DIR__ . '/db_connect.php';

$loader = new Psr4AutoloaderClass();
$loader->register();
$loader->addNamespace('Sessions', __DIR__ . '/../Sessions');

//$arr = get_defined_vars(); //CHECK ALL VULNERABLE VARS
//print_r($arr);
//die();

$handler = new PersistentSessionHandler($db);
session_set_save_handler($handler);
session_start();

$_SESSION['active'] = time();