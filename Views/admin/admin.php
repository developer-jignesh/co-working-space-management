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

    add_submenu_page(
        'coworking-dashboard',
        __('Manage Company Locations', 'coworking-text-domain'),
        __('Manage Company Locations', 'coworking-text-domain'),
        'manage_options',
        'manage-company-locations',
        'renderCompanyLocationPage'
    );

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
function renderDashboardPage()
{
    global $wpdb;

    $location_model = new \App\Models\Location();
    $employee_model = new \App\Models\Employee();
    $company_model = new \App\Models\Company();
    $company_location_model = new \App\Models\CompanyLocation();

    $total_locations = $location_model->countLocations();
    $total_employees = $employee_model->countEmployees();
    $total_companies = $company_model->countCompanies();
    $occupied_space = $company_location_model->getTotalOccupiedSpace();
    $total_space = 10000;
    $monthly_rent = $company_location_model->getTotalMonthlyRent();

    $dashboard_view = plugin_dir_path(__FILE__) . 'dashboard.php';
    if (file_exists($dashboard_view)) {
        include $dashboard_view;
    } else {
        echo '<h1>' . __('Error: Dashboard view not found', 'coworking-text-domain') . '</h1>';
    }
}

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
