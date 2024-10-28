<?php

namespace App\Seeders;
use App\Models\EmployeeModel;
use App\Models\CompanyLocationModel;
use App\Models\LocationModel;
use App\Models\CompanyModel;

class Seeder {
    private $employee_model;
    private $location_model;
    private $company_model;
    private $company_location_model;

    public function __construct() {
        $this->employee_model = new EmployeeModel();
        $this->location_model = new LocationModel();
        $this->company_model = new CompanyModel();
        $this->company_location_model = new CompanyLocationModel();
    }

    /**
     * Seed the database with large test data
     */
    public function run() {
        $this->seedLocations(100);  // Seed 100 locations
        $this->seedCompanies(50);   // Seed 50 companies
        $this->seedEmployees(200);  // Seed 200 employees
        $this->seedCompanyLocations(100); // Seed 100 company-location associations
    }

    public function seedLocations($count = 100) {
        for ($i = 1; $i <= $count; $i++) {
            $location = [
                'name' => 'Co-Working Location ' . $i,
                'address' => rand(100, 999) . ' ' . $this->getRandomStreetName(),
                'city' => $this->getRandomCity(),
                'state' => $this->getRandomState(),
                'zip' => rand(10000, 99999),
                'country' => 'USA',
                'capacity' => rand(50, 300),
                'amenities' => 'WiFi, Coffee, Printer, Lounge',
            ];
            $this->location_model->create($location);
        }
    }

    public function seedCompanies($count = 50) {
        for ($i = 1; $i <= $count; $i++) {
            $company = [
                'name' => 'Company ' . $i,
                'contact_name' => 'Contact ' . $i,
                'contact_email' => 'contact' . $i . '@example.com',
                'contact_phone' => $this->getRandomPhone(),
            ];
            $this->company_model->create($company);
        }
    }

    public function seedEmployees($count = 200) {
        for ($i = 1; $i <= $count; $i++) {
            $employee = [
                'first_name' => $this->getRandomFirstName(),
                'last_name' => $this->getRandomLastName(),
                'email' => 'employee' . $i . '@example.com',
                'phone' => $this->getRandomPhone(),
                'role' => $this->getRandomRole(),
                'location_id' => rand(1, 100), // Assuming 100 locations
            ];
            $this->employee_model->create($employee);
        }
    }

    public function seedCompanyLocations($count = 100) {
        for ($i = 1; $i <= $count; $i++) {
            $company_location = [
                'company_id' => rand(1, 50),  // Assuming 50 companies
                'location_id' => rand(1, 100), // Assuming 100 locations
                'leased_space' => rand(500, 3000),
                'monthly_rent' => rand(1000, 10000),
                'contract_start_date' => $this->getRandomStartDate(),
                'contract_end_date' => $this->getRandomEndDate(),
            ];
            $this->company_location_model->create($company_location);
        }
    }

    // Helper functions to generate random data
    private function getRandomStreetName() {
        $streets = ['Main St', 'High St', 'Oak St', 'Maple Ave', 'Cedar Blvd'];
        return $streets[array_rand($streets)];
    }

    private function getRandomCity() {
        $cities = ['New York', 'Los Angeles', 'Chicago', 'San Francisco', 'Houston'];
        return $cities[array_rand($cities)];
    }

    private function getRandomState() {
        $states = ['NY', 'CA', 'IL', 'TX', 'FL'];
        return $states[array_rand($states)];
    }

    private function getRandomPhone() {
        return rand(100, 999) . '-' . rand(100, 999) . '-' . rand(1000, 9999);
    }

    private function getRandomFirstName() {
        $firstNames = ['Alice', 'Bob', 'Charlie', 'David', 'Eve'];
        return $firstNames[array_rand($firstNames)];
    }

    private function getRandomLastName() {
        $lastNames = ['Johnson', 'Williams', 'Brown', 'Jones', 'Smith'];
        return $lastNames[array_rand($lastNames)];
    }

    private function getRandomRole() {
        $roles = ['Manager', 'Staff', 'Engineer', 'Designer', 'Sales'];
        return $roles[array_rand($roles)];
    }

    private function getRandomStartDate() {
        return '2024-' . rand(1, 12) . '-' . rand(1, 28);
    }

    private function getRandomEndDate() {
        return '2025-' . rand(1, 12) . '-' . rand(1, 28);
    }
}
