<?php
namespace App\Database;


use Exception;

class DatabaseManager
{
    private $wpdb;
    private $option_key = 'coworking_space_plugin_database_setup';

    /**
     * Below magic method will setup the 
     * private $wpdb value with the global $wpdb
     * so that it will only accessible through the current class.
     */
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    /**
     * @return void
     * method(wrapper method) will first check for option_key if the options_key is exist 
     * then no need to create the table 
     * else create table . 
     * this will prevent from the rerun the each time of table creation sql statement 
     * when you activate the db.
     * 
     */
    public function installDatabase()
    {
       try {
          
        if (!$this->isDatabaseSetupComplete()) {
            $this->createTables();
            $this->markDatabaseSetupComplete();
        } else {
            // if the setup is complete , but any table is mission then recreate them
            if(!$this->areAllTablesPresent()) {
                $this->createTables();
            }
        }

       } catch (Exception $e) {
        error_log('database installation error:'. $e->getMessage());
        wp_die('An error occured while setting up the database: ', $e->getMessage());
       }
    }
    private function areAllTablesPresent():bool {
        $tables = ['Location', 'Employee', 'Company', 'CompanyLocation'];
        foreach ($tables as $table) {
            if (!$this->isTableExists($table)) {
                return false; 
            }
        }
        return true;
    }
    /**
     * 
     * 
     * @return bool
     * isDatabaseSetupComplete will check db table is created or not . 
     */
    private function isDatabaseSetupComplete()
    {
        $setup_complete = get_option($this->option_key);
        return $setup_complete === 'yes';
    }
    /**
     * Summary of markDatabaseSetupComplete
     * @return void
     * this will add the key and their respective option to the wp_option table. 
     */
    private function markDatabaseSetupComplete()
    {
        add_option($this->option_key, 'yes');
    }
    /**
     * 
     * createTables method contain all the sql statement 
     * via we are creating a custom wp_table to the wp_db
     * @return void
     */
    public function createTables()
    {
        $charset_collate = $this->wpdb->get_charset_collate();
    
        // Location Table with Capacity and Available Space
        $sql_location = "CREATE TABLE {$this->wpdb->prefix}Location (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            address VARCHAR(255) NOT NULL,
            city VARCHAR(100) NOT NULL,
            state VARCHAR(100) NOT NULL,
            zip VARCHAR(20) NOT NULL,
            country VARCHAR(100) NOT NULL,
            capacity INT NOT NULL,               
            available_space INT NOT NULL,            
            amenities VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
    
        $sql_employee = "CREATE TABLE {$this->wpdb->prefix}Employee (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            role VARCHAR(100) NOT NULL,
            location_id BIGINT(20) UNSIGNED NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY location_id_idx (location_id)
        ) $charset_collate;";
    
        $alter_employee = "ALTER TABLE {$this->wpdb->prefix}Employee 
            ADD CONSTRAINT fk_employee_location FOREIGN KEY (location_id) REFERENCES {$this->wpdb->prefix}Location(id) ON DELETE CASCADE;";
    
        $sql_company = "CREATE TABLE {$this->wpdb->prefix}Company (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            contact_name VARCHAR(100) NOT NULL,
            contact_email VARCHAR(255) NOT NULL,
            contact_phone VARCHAR(20) NOT NULL,
            location_id BIGINT(20) UNSIGNED NOT NULL,
            leased_space INT NOT NULL,              
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY location_id_idx (location_id)
        ) $charset_collate;";
    
        $alter_company = "ALTER TABLE {$this->wpdb->prefix}Company 
            ADD CONSTRAINT fk_company_location FOREIGN KEY (location_id) REFERENCES {$this->wpdb->prefix}Location(id) ON DELETE SET NULL;";
    
        try {
            // Execute each SQL query with dbDelta
            $this->executeQuery($sql_location);
            $this->executeQuery($sql_employee);
            $this->executeQuery($sql_company);
    
            // Execute the ALTER TABLE queries for foreign key constraints
            $this->executeQuery($alter_employee);
            $this->executeQuery($alter_company);
    
        } catch (Exception $e) {
            error_log('Database creation error: ' . $e->getMessage());
            wp_die('An error occurred while setting up the database: ' . $e->getMessage());
        }
    }
    

    /**
     * this execute query will take each sql query and then execute them
     * if any error has been occured then it will throw an 
     * exception , via the devloper easily understand what thing going on
     * back of the db if the error will happen else nothing. 
     */

    private function executeQuery($sql)
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Check for errors
        if (!empty($wpdb->last_error)) {
            throw new Exception('SQL Error: ' . $wpdb->last_error);
        }
    }
     /**
     * Checks if a specific table exists in the database.
     *
     * @param string $table_name The name of the table without the prefix.
     * @return bool True if the table exists, false otherwise.
     */
    public function isTableExists($table_name): bool
    {
        $full_table_name = $this->wpdb->prefix . $table_name;
        return $this->wpdb->get_var("SHOW TABLES LIKE '$full_table_name'") === $full_table_name;
    }
}