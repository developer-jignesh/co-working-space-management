<?php

namespace App\Controllers;

use App\Models\Location as Model;

class Location
{
    private $model;
    protected static $exists = true;

    protected $LocationProps = [
        'id' => '',
        'name' => '',
        'address' => '',
        'city' => '',
        'state' => '',
        'zip' => '',
        'country' => '',
        'capacity' => '',
        'amenities' => '',
    ];

    public function __construct($id = null)
    {
        $this->model = new Model();

        if ($id) {
            $locationData = $this->model->getLocationById($id);
            if ($locationData) {
                $this->setLocationProps($locationData);
            } else {
                self::$exist = false;

            }
        }
    }

    private function setLocationProps($data)
    {
        foreach ($this->LocationProps as $key => $value) {
            if (isset($data->$key)) {
                $this->LocationProps[$key] = sanitize_text_field($data->$key);
            }
        }
    }

    public function get_data($key = '')
    {
        return isset($this->LocationProps[$key]) ? $this->LocationProps[$key] : null;
    }

    public function set_data($key, $value)
    {
        return $this->LocationProps[$key] = sanitize_text_field($value);
    }

    public function save_data()
    {
        if (!empty($this->LocationProps['id'])) {
            // Update existing location
            return $this->model->updateLocation($this->LocationProps['id'], $this->LocationProps);
        } else {
            // Create new location
            return $this->model->createLocation($this->LocationProps);
        }
    }

    public function delete_data($id)
    {
        if (!empty($id)) {
            return $this->model->deleteLocationById($id);
        }
        return false;
    }

    public function get_all_locations()
    {
        return $this->model->getAllLocations();
    }
   //method for get location for pagination.
    public function getAllLocationforPagination($locations_per_page, $offset) {
         return $this->model->getAllLocationsforPagination($locations_per_page, $offset);
    }
    public function get_employees_by_location($location_id)
    {
        return $this->model->getEmployeesByLocation($location_id);
    }

    public function get_companies_by_location($location_id)
    {
        return $this->model->getCompaniesByLocation($location_id);
    }

    public function count_locations()
    {
        return $this->model->countLocations();
    }
    public function is_exists()
    {
        return !empty($this->LocationProps['id']) || self::$exists;
    }
}
