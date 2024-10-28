<?php

namespace App\Commands;

use App\Seeders\Seeder;

class CliCommands {

    /**
     * Register the custom WP-CLI command.
     */
    public static function register_commands() {
        // Register the 'coworking-seed' command if WP_CLI is defined
        if (defined('WP_CLI') && \WP_CLI) { // Use \WP_CLI for the global namespace
            \WP_CLI::add_command('coworking-seed', [__CLASS__, 'seed_data']);
        }
    }

    /**
     * Run the seeder to populate the database with test data.
     *
     *
     *
     *     wp coworking-seed
     *
     * @when after_wp_load
     */
    public static function seed_data() {

        $seeder = new Seeder();
        $seeder->run();

        \WP_CLI::success('Database has been seeded successfully!'); 
    }
}
