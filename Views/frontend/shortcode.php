<?php
//exit on direct accesss
if(!defined('ABSPATH')) exit;
// Register shortcode
add_action('init', function () {
    add_shortcode('coworking_list', 'coworking_list_shortcode');
});

function coworking_list_shortcode($atts)
{
    $atts = shortcode_atts([
        'type' => 'location', 
        'count' => -1, 
    ], $atts, 'coworking_list');

    switch ($atts['type']) {
        case 'location':
            return coworking_list_locations($atts['count']);
        case 'company':
            return coworking_list_companies($atts['count']);
        case 'employee':
            return coworking_list_employees($atts['count']);
        case 'dashboard':
            return coworking_list_dashboard($atts['count']);
        default:
            return __('Invalid type specified.', 'coworking-text-domain');
    }
    
}
function coworking_list_locations($count)
{
    $location_model = new \App\Models\Location();
    $locations = $location_model->shortcodegetAllLocations($count);

    if (empty($locations)) {
        return __('No locations found.', 'coworking-text-domain');
    }

    $output = '<ul class="coworking-locations">';
    foreach ($locations as $location) {
        $output .= '<li>' . esc_html($location->name) . ' - ' . esc_html($location->address) . '</li>';
    }
    $output .= '</ul>';

    return $output;
}

function coworking_list_companies($count)
{
    $company_model = new \App\Models\Company();
    $companies = $company_model->shortcodegetAllCompanies($count);

    if (empty($companies)) {
        return __('No companies found.', 'coworking-text-domain');
    }

    $output = '<ul class="coworking-companies">';
    foreach ($companies as $company) {
        $output .= '<li>' . esc_html($company->name) .  '</li>';
    }
    $output .= '</ul>';

    return $output;
}

function coworking_list_employees($count)
{
    $employee_model = new \App\Models\Employee();
    $employees = $employee_model->shortcodegetAllEmployees($count);

    if (empty($employees)) {
        return __('No employees found.', 'coworking-text-domain');
    }

    $output = '<ul class="coworking-employees">';
    foreach ($employees as $employee) {
        $output .= '<li>' . esc_html($employee->name) . ' - ' . esc_html($employee->position) . '</li>';
    }
    $output .= '</ul>';

    return $output;
}

function coworking_list_dashboard($count)
{
    
    $output = '<p>' . __('Dashboard data is not available via shortcode.', 'coworking-text-domain') . '</p>';
    return $output;
}

