<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 * RMS – Restaurant Management System
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Optimistically returned from dsn() if not empty.
     */
    public string $defaultGroup = 'default';

    /**
     * Default connection group.
     *
     * @var array<string, mixed>
     */
    public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'root',
        'password'     => 'root',
        'database'     => 'rms_db',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_unicode_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => true,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'foundRows'    => false,
    ];

    /**
     * Test database (used by phpunit / CI tests).
     *
     * @var array<string, mixed>
     */
    public array $tests = [
        'DSN'      => '',
        'hostname' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'database' => 'rms_test',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => 'test_',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8mb4',
        'DBCollat' => 'utf8mb4_unicode_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => true,
        'failover' => [],
        'port'     => 3306,
    ];

    public function __construct()
    {
        parent::__construct();

        // Allow environment variable overrides for deployment flexibility
        if (getenv('DB_HOST') !== false) {
            $this->default['hostname'] = getenv('DB_HOST');
        }
        if (getenv('DB_USER') !== false) {
            $this->default['username'] = getenv('DB_USER');
        }
        if (getenv('DB_PASS') !== false) {
            $this->default['password'] = getenv('DB_PASS');
        }
        if (getenv('DB_NAME') !== false) {
            $this->default['database'] = getenv('DB_NAME');
        }
    }
}
