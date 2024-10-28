<?php

namespace App\Controllers;

use App\Models\CompanyLocation as Model;

class CompanyLocation {

    private $model;
    public static $exists = true;
    protected $CompanyLocationProps = [
        'company_id' => '',
        'location_id' => '',
        'leased_space' => '',
        'monthly_rent' => '',
        'contract_start_date' => '',
        'contract_end_date' => '',
    ];

    public function __construct($company_id = null, $location_id = null) {
        $this->model = new Model();
        if ($company_id && $location_id) {
            $data = $this->model->getCompanyLocationById($company_id, $location_id);
            if ($data) {
                $this->setCompanyLocationProps($data);
            } else {
                self::$exists = false;
            }
        }
    }

    private function setCompanyLocationProps($data) {
        foreach ($this->CompanyLocationProps as $key => $value) {
            if (isset($data->$key)) {
                $this->CompanyLocationProps[$key] = sanitize_text_field($data->$key);
            }
        }
    }

    public function get_data($key = '') {
        return $this->CompanyLocationProps[$key] ?? null;
    }

    public function set_data($key, $value) {
        if (array_key_exists($key, $this->CompanyLocationProps)) {
            if ($key === 'leased_space' || $key === 'monthly_rent') {
                $this->CompanyLocationProps[$key] = floatval($value);
            } else {
                $this->CompanyLocationProps[$key] = sanitize_text_field($value);
            }
        }
    }

    public function save_data() {
        error_log("Saving data... Company ID: " . $this->CompanyLocationProps['company_id']);
        error_log("Location ID: " . $this->CompanyLocationProps['location_id']);
        
        if (!empty($this->CompanyLocationProps['company_id']) && !empty($this->CompanyLocationProps['location_id'])) {
            $existing = $this->model->getCompanyLocationById($this->CompanyLocationProps['company_id'], $this->CompanyLocationProps['location_id']);
            error_log("Checking if record exists for update...");
            
            if ($existing) {
                error_log("Updating existing record.");
                return $this->model->updateCompanyLocation(
                    $this->CompanyLocationProps['company_id'],
                    $this->CompanyLocationProps['location_id'],
                    $this->CompanyLocationProps
                );
            } else {
                error_log("No existing record found; creating a new record.");
                return $this->model->createCompanyLocation($this->CompanyLocationProps);
            }
        } else {
            error_log("Insufficient data: Company ID and Location ID are required.");
            return false;
        }
    }

    public function delete_data($company_id, $location_id) {
        error_log("Deleting company location with Company ID: $company_id and Location ID: $location_id");
        return $this->model->deleteCompanyLocation($company_id, $location_id);
    }

    public function get_all_company_locations() {
        return $this->model->read_();
    }

    public function get_total_rent() {
        return $this->model->getTotalMonthlyRent();
    }

    public function get_total_occupied_space() {
        return $this->model->getTotalOccupiedSpace();
    }

    public function is_exists() {
        return !empty($this->CompanyLocationProps['company_id']) && !empty($this->CompanyLocationProps['location_id']) && self::$exists;
    }
}
