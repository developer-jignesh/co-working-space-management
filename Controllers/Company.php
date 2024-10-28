<?php
namespace App\Controllers;

use App\Models\Company as Model;

class Company
{

    protected $CompanyProps = [
        'id' => '',
        'name' => '',
        'contact_name' => '',
        'contact_email' => '',
        'contact_phone' => '',
    ];

    private $Model;

    protected static $exists = true;

    public function __construct($id = null)
    {
        $this->Model = new Model();

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
    { // need mapping of the each field with the company field 
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
}
