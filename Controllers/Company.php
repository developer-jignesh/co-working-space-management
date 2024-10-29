<?php
namespace App\Controllers;

use App\Models\Company as Model;
use App\Models\Location;

class Company
{

    protected $CompanyProps = [
        'id' => '',
        'name' => '',
        'contact_name' => '',
        'contact_email' => '',
        'contact_phone' => '',
        'leased_space' => '',
        'location_id' => '',
    ];

    private $Model;
    protected $location_model;
    protected static $exists = true;

    public function __construct($id = null)
    {
        $this->Model = new Model();
        $this->location_model = new Location();
        if ($id) {
            // Fetch data from the model and set in props
            $CompanyData = $this->Model->getCompanyById($id);
            if ($CompanyData) {
                $this->setCompanyProps($CompanyData);
            } else {
                self::$exists = false;
            }
        }
    }

    private function setCompanyProps($data)
    {
        foreach ($this->CompanyProps as $key => $value) {
            if (isset($data->$key)) {
                $this->CompanyProps[$key] = sanitize_text_field($data->$key);
            }
        }
    }

    public function get_data($key = '')
    {
        return isset($this->CompanyProps[$key]) ? $this->CompanyProps[$key] : null;
    }

    public function set_data($key, $value)
    {
        return $this->CompanyProps[$key] = sanitize_text_field($value);
    }

    public function save_data()
    {
        $location_id = $this->CompanyProps['location_id'];
        $new_leased_space = intval($this->CompanyProps['leased_space']);
        
        // Fetch the location data based on location_id
        $location = $this->location_model->getLocationById($location_id);
    
        if (!$location) {
            echo '<div class="error"><p>' . __('Location not found.', 'coworking-text-domain') . '</p></div>';
            return false;
        }
    
        $available_space = intval($location->available_space);
        
        // If updating an existing company, fetch the previous leased space
        $previous_leased_space = 0;
        if (!empty($this->CompanyProps['id'])) {
            $existing_company = $this->Model->getCompanyById($this->CompanyProps['id']);
            $previous_leased_space = intval($existing_company->leased_space);
        }
        
        // Calculate the difference in leased space
        $space_change = $new_leased_space - $previous_leased_space;
        $new_available_space = $available_space - $space_change;
        
        // Check if the operation will result in negative available space
        if ($new_available_space < 0) {
            echo '<div class="error"><p>' . sprintf(
                __('Insufficient space available. Only %d units of space are available at this location.', 'coworking-text-domain'),
                $available_space
            ) . '</p></div>';
            return false;
        }
    
        // If there's enough space, update available_space in the Location model
        $this->location_model->updateAvailableSpace($location_id, $new_available_space);
    
        // Proceed to save or update company data
        if (!empty($this->CompanyProps['id'])) {
            return $this->Model->updateCompany($this->CompanyProps['id'], $this->CompanyProps);
        } else {
            return $this->Model->createCompany($this->CompanyProps);
        }
    }
    
    
    public function delete_data($id = null)
    {
        if (!empty($id)) {
            return $this->Model->deleteCompanyById($id);
        }
        return false;
    }

    // Retrieve all companies
    public function get_all_companies()
    {

        return $this->Model->read_();
    }

    public function is_exists()
    {
        return !empty($this->CompanyProps['id']) || self::$exists;
    }
    public function countCompanies() {
        return $this->Model->countCompanies();
    }
    public function getAllcompaniesforPagination($limit, $offset) {
        return $this->Model->getAllcompaniesforPagination($limit,$offset);
    }
}
