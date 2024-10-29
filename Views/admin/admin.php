<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}


// Register the main admin menu and submenu pages
add_action("admin_menu", function () {
    add_menu_page(
        __('Co-Working Space', 'coworking-text-domain'),
        __('Co-Working Space', 'coworking-text-domain'),
        'manage_options',
        'coworking-dashboard',
        'renderDashboardPage'
    );

    add_submenu_page(
        'coworking-dashboard',
        __('Manage Locations', 'coworking-text-domain'),
        __('Manage Locations', 'coworking-text-domain'),
        'manage_options',
        'manage-locations',
        'manageLocations'
    );

    add_submenu_page(
        'coworking-dashboard',
        __('Manage Companies', 'coworking-text-domain'),
        __('Manage Companies', 'coworking-text-domain'),
        'manage_options',
        'manage-companies',
        'renderCompanyPage'
    );

    // add_submenu_page(
    //     'coworking-dashboard',
    //     __('Manage Company Locations', 'coworking-text-domain'),
    //     __('Manage Company Locations', 'coworking-text-domain'),
    //     'manage_options',
    //     'manage-company-locations',
    //     'renderCompanyLocationPage'
    // );

    add_submenu_page(
        'coworking-dashboard',
        __('Manage Employees', 'coworking-text-domain'),
        __('Manage Employees', 'coworking-text-domain'),
        'manage_options',
        'manage-employees',
        'renderEmployeePage'
    );
});

// Enqueue admin scripts
add_action('admin_enqueue_scripts', 'enqueueAdminScripts');

// Callback functions for menu pages

function manageLocations()
{
    include plugin_dir_path(__FILE__) . 'location.php';
}

function renderCompanyPage()
{
    $companies_view = plugin_dir_path(__FILE__) . 'company.php';
    if (file_exists($companies_view)) {
        include $companies_view;
    } else {
        echo '<h1>' . __('Error: Company management view not found.', 'coworking-text-domain') . '</h1>';
    }
}

function renderCompanyLocationPage()
{
    $result = plugin_dir_path(__FILE__) . 'company-location.php';
    if (file_exists($result)) {
        include $result;
    } else {
        echo '<h1>' . __('Error: Company Location management view not found.', 'coworking-text-domain') . '</h1>';
    }
}

function renderEmployeePage()
{
    $result = plugin_dir_path(__FILE__) . 'employee.php';
    if (file_exists($result)) {
        include $result;
    } else {
        echo '<h1>' . __('Error: Employee management view not found.', 'coworking-text-domain') . '</h1>';
    }
}


function enqueueAdminScripts()
{
    $screen = get_current_screen();
    if ($screen->id === 'toplevel_page_coworking-dashboard') {
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
    }
}
