<?php

namespace App\Models;

class CompanyLocation extends BaseModel {
    protected $wpdb;
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
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
        $table = $this->wpdb->prefix . 'CompanyLocation';
        $sql = $this->wpdb->prepare("
            SELECT * FROM $table 
            WHERE company_id = %d AND location_id = %d
            LIMIT 1
        ", $company_id, $location_id);
    
        return $this->wpdb->get_row($sql); 
    }

    public function updateCompanyLocation($company_id, $location_id, $data) {
        $table = $this->wpdb->prefix . 'CompanyLocation';
        $result = $this->wpdb->update(
            $table,
            $data, 
            ['company_id' => $company_id, 'location_id' => $location_id], 
            array_map(function($value) { return is_int($value) ? '%d' : '%f'; }, $data), // Type casting array
            ['%d', '%d'] 
        );

        if ($this->wpdb->last_error) {
            error_log("Update Error: " . $this->wpdb->last_error);
        }

        return $result;
    }

    public function deleteCompanyLocation($company_id, $location_id) {
        $table = $this->wpdb->prefix . 'CompanyLocation';
        $result = $this->wpdb->delete(
            $table,
            ['company_id' => $company_id, 'location_id' => $location_id],
            ['%d', '%d']
        );

        if ($this->wpdb->last_error) {
            error_log("Delete Error: " . $this->wpdb->last_error);
        }

        return $result;
    }

    public function getTotalMonthlyRent() {
        $table = $this->wpdb->prefix . 'CompanyLocation';
        $query = "SELECT SUM(monthly_rent) AS total_rent FROM $table";
        return $this->wpdb->get_var($query); 
    }

    public function getTotalOccupiedSpace() {
        $table = $this->wpdb->prefix . 'CompanyLocation';
        $query = "SELECT SUM(leased_space) AS total_space FROM $table";
        return $this->wpdb->get_var($query); 
    }
}
