<?php

namespace App\Models;

class Company extends BaseModel {

    public function __construct() {
        parent::__construct('Company');
    }

    // Getter for retrieving a company by ID
    public function getCompanyById($id): array|object|null {
        return $this->readById($id);
    }

    // Setter for creating a new company
    public function createCompany($data) {
        return $this->create($data);
    }

    // Setter for updating an existing company by ID
    public function updateCompany($id, $data) {
        return $this->update($id, $data);
    }

    // Delete company by ID
    public function deleteCompanyById($id) {
        return $this->delete($id);
    }

    // Additional helper for counting companies
    public function countCompanies() {
        return $this->countAll();
    }
    public function shortcodegetAllCompanies($count = -1)
{
    global $wpdb;
    $query = "SELECT * FROM {$this->table}";

    if ($count > 0) {
        $query .= $wpdb->prepare(" LIMIT %d", $count);
    }

    return $wpdb->get_results($query);
}

}
