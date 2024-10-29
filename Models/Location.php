<?php

namespace App\Models;

class Location extends BaseModel {
  protected $wpdb;
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        parent::__construct('Location');
    }

    // Method to get all locations
    public function getAllLocations(): array|object|null {
        return $this->read(); // Will fetch all locations from the 'Location' table
    }

    // Method to get location by ID
    public function getLocationById($id) {
        return $this->read($id); // Fetch a single location by its ID
    }

    // Method to create a new location
    public function createLocation($data) {
        $data['available_space'] = $data['capacity'];
        return $this->create($data); // Create a new record in 'Location' table
    }

    // Method to update a location by ID
    public function updateLocation($id, $data) {
        return $this->update($id, $data); // Update the location with given ID
    }

    // Method to delete a location by ID
    public function deleteLocationById($id) {
        return $this->delete($id); // Delete the location with given ID
    }

    // Method to get all employees assigned to a specific location
    public function getEmployeesByLocation($location_id) {
        return $this->getWithJoin(
            'employee', 
            "{$this->table}.id = {$this->wpdb->prefix}employee.location_id", 
            "{$this->table}.*, {$this->wpdb->prefix}employee.first_name, {$this->wpdb->prefix}employee.last_name"
        );
    }

    // Method to get all companies associated with this location (via the CompanyLocation relationship)
    public function getCompaniesByLocation($location_id) {
        return $this->getWithJoin(
            'companylocation',
            "{$this->table}.id = {$this->wpdb->prefix}companylocation.location_id",
            "{$this->table}.*, {$this->wpdb->prefix}companylocation.leased_space, {$this->wpdb->prefix}company.name AS company_name"
        );
    }
    public function countLocations() {
        return $this->countAll();
    }
    public function shortcodegetAllLocations($count = -1)
{
    $query = "SELECT * FROM {$this->table}";
    
    if ($count > 0) {
        $query .= $this->wpdb->prepare(" LIMIT %d", $count);
    }

    return $this->wpdb->get_results($query);
}
 // Method to update available_space
 public function updateAvailableSpace($location_id, $new_available_space) {
    return $this->wpdb->update(
        $this->table,
        ['available_space' => $new_available_space],
        ['id' => $location_id]
    );
}

public function getAllLocationsforPagination($limit,$offset) {
    $query = $this->wpdb->prepare(
        "SELECT * FROM {$this->table} LIMIT %d OFFSET %d",
        $limit,
        $offset
    );
    return $this->wpdb->get_results($query);
}

}
