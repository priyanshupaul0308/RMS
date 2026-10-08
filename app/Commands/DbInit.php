<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class DbInit extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:init';
    protected $description = 'Initializes the database schema and default seeds from database/rms_db_complete.sql';

    public function run(array $params)
    {
        CLI::write('Checking database connection...', 'yellow');

        try {
            $db = Database::connect();
            $db->initialize();
        } catch (\Throwable $e) {
            CLI::error('Database connection failed: ' . $e->getMessage());
            return;
        }

        CLI::write('Database connected successfully!', 'green');

        // Check if users table already exists
        if ($db->tableExists('users')) {
            $count = $db->table('users')->countAllResults();
            CLI::write("Database already contains tables (users count: {$count}). Skipping schema import.", 'cyan');
            return;
        }

        $sqlFile = ROOTPATH . 'database/rms_db_complete.sql';
        if (!file_exists($sqlFile)) {
            CLI::error("SQL file not found at: {$sqlFile}");
            return;
        }

        CLI::write('Importing complete database schema and seed data...', 'yellow');
        $sql = file_get_contents($sqlFile);

        $mysqli = $db->connID;
        if ($mysqli instanceof \mysqli) {
            if ($mysqli->multi_query($sql)) {
                do {
                    if ($result = $mysqli->store_result()) {
                        $result->free();
                    }
                } while ($mysqli->more_results() && $mysqli->next_result());
            }

            if ($mysqli->errno) {
                CLI::write('SQL import notice: ' . $mysqli->error, 'yellow');
            }
        }

        CLI::write('Database schema and seeds successfully imported!', 'green');
    }
}
