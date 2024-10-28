<?php

namespace App\Models;

class Employee extends BaseModel {

   

    public function __construct() {
       
        parent::__construct('Employee');
    }

    // Create a new employee
    public function createEmployee($data) {
       return parent::create($data);
    }

    // Read all employees
    public function read_() {
        return parent::read_();
    }

    // Read a single employee by ID
    public function getEmployeeById($id) {
        return parent::readById($id);
    }

    // Update an employee
    public function updateEmployee($id, $data) {
       return parent::update($id,$data);      
     }

    // Delete an employee
    public function deleteEmployeeById($id) {
   return parent::delete($id);   
    }
    public function countEmployees() {
        return parent::countAll();
    }
    public function shortcodegetAllEmployees($count = -1)
{
    global $wpdb;
    $query = "SELECT * FROM {$this->table}";

    if ($count > 0) {
        $query .= $wpdb->prepare(" LIMIT %d", $count);
    }

    return $wpdb->get_results($query);
}

}
