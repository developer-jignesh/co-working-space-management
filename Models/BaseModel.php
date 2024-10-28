<?php

namespace App\Models;

class BaseModel
{
    protected $wpdb;
    protected $table;

    public function __construct($table)
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . $table;
    }
    public function DeleteCompanyLocationByID($company_id, $location_id)
    {

    }
    // Create a new record
    public function create($data)
    {
        return $this->wpdb->insert($this->table, $data);
    }

    // Read all records or a specific record by ID
    public function read($id = null)
    {
        if ($id) {
            return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id));
        }
        return $this->wpdb->get_results("SELECT * FROM {$this->table}");
    }

    public function readById($id): array|object|null
    {
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id));
    }

    // Update a record by ID
    public function update($id, $data)
    {
        return $this->wpdb->update($this->table, $data, ['id' => $id]);
    }

    // Delete a record by ID
    public function delete($id)
    {
        return $this->wpdb->delete($this->table, ['id' => $id]);
    }

    // Retrieve records with a foreign key relationship
    public function getWithJoin($joinTable, $joinCondition, $fields = '*')
    {
        $sql = "SELECT {$fields} FROM {$this->table} 
                JOIN {$this->wpdb->prefix}{$joinTable} ON {$joinCondition}";
        return $this->wpdb->get_results($sql);
    }

    // Custom query
    public function query($sql)
    {
        return $this->wpdb->get_results($sql);
    }
    public function read_()
    {
        return $this->wpdb->get_results("SELECT * FROM $this->table");
    }

    public function readByIdCompanyIdandLocationId($company_id, $location_id)
    {
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $this->table WHERE company_id = %d AND location_id = %d", $company_id, $location_id));
    }
    public function updateByCompanyIdandLocationId($company_id, $location_id, $data)
    {
        $this->wpdb->update($this->table, $data, ['company_id' => $company_id, 'location_id' => $location_id]);
    }

    public function deleteByCompanyIdandLocationId($company_id, $location_id)
    {
        $this->wpdb->delete($this->table, ['company_id' => $company_id, 'location_id' => $location_id]);
    }
    public function countAll() {
        return (int) $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table}");
    }
    public function getTotalMonthlyRent() {
        return $this->wpdb->get_var("SELECT SUM(monthly_rent) FROM {$this->wpdb->prefix}CompanyLocation");
    }
    public function getTotalOccupiedSpace() {
        
       return  $this->wpdb->get_var("SELECT SUM(leased_space) FROM {$this->wpdb->prefix}CompanyLocation");
    }
   
}
