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
        'hostname'     => '127.0.0.1',
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

        // Support database URL format (e.g. mysql://user:pass@host:port/dbname)
        $dbUrl = getenv('DATABASE_URL') ?: (getenv('MYSQL_URL') ?: ($_ENV['DATABASE_URL'] ?? null));
        if ($dbUrl) {
            $parsed = parse_url($dbUrl);
            if (!empty($parsed['host'])) {
                $this->default['hostname'] = $parsed['host'];
            }
            if (!empty($parsed['user'])) {
                $this->default['username'] = $parsed['user'];
            }
            if (isset($parsed['pass'])) {
                $this->default['password'] = $parsed['pass'];
            }
            if (!empty($parsed['path'])) {
                $this->default['database'] = ltrim($parsed['path'], '/');
            }
            if (!empty($parsed['port'])) {
                $this->default['port'] = (int)$parsed['port'];
            }
        }

        // Allow explicit individual environment variable overrides
        $envHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? null);
        if ($envHost) {
            $this->default['hostname'] = $envHost;
        }

        $envUser = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? null);
        if ($envUser) {
            $this->default['username'] = $envUser;
        }

        $envPass = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? (getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? null)));
        if ($envPass !== null) {
            $this->default['password'] = $envPass;
        }

        $envName = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? (getenv('DB_DATABASE') ?: ($_ENV['DB_DATABASE'] ?? null)));
        if ($envName) {
            $this->default['database'] = $envName;
        }

        $envPort = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? null);
        if ($envPort) {
            $this->default['port'] = (int)$envPort;
        }
    }
}
