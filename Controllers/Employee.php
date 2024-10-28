<?php

namespace App\Controllers;
use App\Models\Employee as Model;

class Employee
{

    private $model;
    protected static $exists = true;

    protected $EmployeeProps = [
        'id' => '',
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'phone' => '',
        'role' => '',
        'location_id' => '',
    ];

    public function __construct($id = null)
    {
        $this->model = new Model();
        if ($id) {
            $EmployeeData = $this->model->getEmployeeById($id);
            if ($EmployeeData) {
                $this->setEmployeeProps($EmployeeData);
            } else {
                self::$exist = false;
            }
        }
    }

    private function setEmployeeProps($data)
    {
        foreach ($this->EmployeeProps as $key => $value) {
            if (isset($data->$key)) {
                $this->EmployeeProps[$key] = sanitize_text_field($data->$key);
            }
        }
    }

    public function get_data($key = '')
    {
        return isset($this->EmployeeProps[$key]) ? $this->EmployeeProps[$key] : null;
    }

    public function set_data($key, $value)
    {
        return $this->EmployeeProps[$key] = sanitize_text_field($value);
    }

    public function save_data()
    { // need mapping of the each field with the company field 
        if (!empty($this->EmployeeProps['id'])) {
            return $this->model->updateEmployee($this->EmployeeProps['id'], $this->EmployeeProps);
        } else {
            return $this->model->createEmployee($this->EmployeeProps);
        }
    }

    public function delete_data($id = null)
    {
        if (!empty($id)) {
            return $this->model->deleteEmployeeById($id);
        }
        return false;
    }

    public function get_all_employees()
    {

        return $this->model->read_();
    }

    public function is_exists()
    {
        return !empty($this->EmployeeProps['id']) || self::$exists;
    }
    
}