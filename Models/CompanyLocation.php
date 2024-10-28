<?php

namespace App\Models;

class CompanyLocation extends BaseModel {

    public function __construct() {
        parent::__construct('CompanyLocation');
    }

    // Create a new company-location association
    public function createCompanyLocation($data) {
        error_log("Attempting to create CompanyLocation with data: " . print_r($data, true));
        $result = parent::create($data);
        if ($this->wpdb->last_error) {
            error_log("Database Error: " . $this->wpdb->last_error);
        }
        return $result;
    }

    // Read all company-location associations
    public function read_() {
        return parent::read_();
    }

    public function getCompanyLocationById($company_id, $location_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'CompanyLocation';
        $sql = $wpdb->prepare("
            SELECT * FROM $table 
            WHERE company_id = %d AND location_id = %d
            LIMIT 1
        ", $company_id, $location_id);
    
        return $wpdb->get_row($sql); 
    }

    public function updateCompanyLocation($company_id, $location_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'CompanyLocation';
        $result = $wpdb->update(
            $table,
            $data, 
            ['company_id' => $company_id, 'location_id' => $location_id], 
            array_map(function($value) { return is_int($value) ? '%d' : '%f'; }, $data), // Type casting array
            ['%d', '%d'] 
        );

        if ($wpdb->last_error) {
            error_log("Update Error: " . $wpdb->last_error);
        }

        return $result;
    }

    public function deleteCompanyLocation($company_id, $location_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'CompanyLocation';
        $result = $wpdb->delete(
            $table,
            ['company_id' => $company_id, 'location_id' => $location_id],
            ['%d', '%d']
        );

        if ($wpdb->last_error) {
            error_log("Delete Error: " . $wpdb->last_error);
        }

        return $result;
    }

    public function getTotalMonthlyRent() {
        global $wpdb;
        $table = $wpdb->prefix . 'CompanyLocation';
        $query = "SELECT SUM(monthly_rent) AS total_rent FROM $table";
        return $wpdb->get_var($query); 
    }

    public function getTotalOccupiedSpace() {
        global $wpdb;
        $table = $wpdb->prefix . 'CompanyLocation';
        $query = "SELECT SUM(leased_space) AS total_space FROM $table";
        return $wpdb->get_var($query); 
    }
}
