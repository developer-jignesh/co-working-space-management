<?php

namespace App\Controllers;

use App\Models\Company;
use App\Models\Location;

class AjaxController
{
    public function __construct()
    {
        // Register the AJAX handler for fetching companies
        add_action("wp_ajax_get_companies", array($this, "handleGetCompanies"));
        // Register the AJAX handler for fetching locations
        add_action("wp_ajax_get_locations", array($this, "handleGetLocations"));
    }

    // Handle the AJAX request to fetch available companies
    public function handleGetCompanies()
    {
        check_ajax_referer('get_companies_nonce', 'security');
        
        $company_model = new Company();
        $companies = $company_model->read();

        $response = [];
        foreach ($companies as $company) {
            $response[] = [
                'id' => $company->id,
                'name' => $company->name,
            ];
        }

        wp_send_json_success($response); // Return JSON response
    }

    // Handle the AJAX request to fetch available locations
    public function handleGetLocations()
    {
        check_ajax_referer('get_locations_nonce', 'security'); // Security check

        $location_model = new Location();
        $locations = $location_model->read();

        $response = [];
        foreach ($locations as $location) {
            $response[] = [
                'id' => $location->id,
                'name' => $location->name,
            ];
        }

        wp_send_json_success($response); // Return JSON response
    }
}
