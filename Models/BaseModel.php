<?php

namespace App\Models;
use App\Database\DatabaseManager;
use Exception;
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

   
  
   
   
    public function countAll(): int {
        return (int) $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table}");
    }
    
    
   
}
