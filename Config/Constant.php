<?php
namespace App\Config;
//block the access when someone try to access direct with the url. 
if(!defined('ABSPATH')) exit;
class Constant {

    public function __construct() {
        die();
    }
    const DIR = __DIR__;

    // const PULGIN = plugin_dir_path(__FILE__);
    public static  $FILE = __FILE__;
}