<?php

if (!defined('ABSPATH')) exit;

/**
 * Plugin Name: Co-Working Space Management
 * Description: Manage co-working space, employees, and lease companies
 * Version: 1.0
 * Author: Jignesh Sharma
 * Text Domain: coworking-text-domain
 * Domain Path: /languages
 */

use App\Database\DatabaseManager;
use App\Controllers\AjaxController;

// Autoload dependencies
require_once __DIR__ . "/vendor/autoload.php";

// Include Admin setup file
if(is_admin()) {

    require_once __DIR__ . "/Views/admin/admin.php";
} else {

    require_once __DIR__ . "/Views/frontend/shortcode.php";
}
  
add_action("plugins_loaded", function() {
    $database_manager = new DatabaseManager();
    $database_manager->installDatabase();
    load_plugin_textdomain('coworking-text-domain', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// Initialize AjaxController for AJAX requests
if (wp_doing_ajax()) {
    $ajax_controller = new AjaxController();
}

// Register CLI commands if WP-CLI is available
if (defined('WP_CLI') && WP_CLI) {
    require_once __DIR__ . '/Commands/CliCommands.php';
    \App\Commands\CliCommands::register_commands();
}
