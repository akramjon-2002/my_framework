<?php

namespace console;

use Core\Database;
use Core\Migration;

class Migrate
{
    private $migrationsPath = __DIR__ . '/../migrations/';
    private $connection;

    public function __construct()
    {
        $this->connection = Database::connect();
    }

    public function run()
    {
        $migrations = $this->getMigrations();
        if (empty($migrations)) {
            echo "No migrations found.\n";
            return;
        }
        
        echo "Starting migrations...\n";
        foreach ($migrations as $migration) {
            echo "\nApplying migration: " . basename($migration) . "\n";
            $this->applyMigration($migration);
        }
        echo "\nAll migrations completed successfully!\n";
    }

    private function getMigrations()
    {
        $files = glob($this->migrationsPath . '*.php');
        usort($files, function ($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        return $files;
    }

    private function applyMigration($migrationFile)
    {
        require_once $migrationFile;

        $migrationClass = $this->getMigrationClassName($migrationFile);
        $migration = new $migrationClass();

        echo "Running migration: $migrationClass\n";
        $migration->up();
    }

    private function getMigrationClassName($migrationFile)
    {
        $filename = basename($migrationFile, '.php');
        $className = str_replace('_', '', ucwords($filename, '_'));
        return 'Migrations\\' . $className;
    }



    public function rollback()
    {
        $migrations = array_reverse($this->getMigrations());
        foreach ($migrations as $migration) {
            $this->revertMigration($migration);
        }
    }

    private function revertMigration($migrationFile)
    {
        require_once $migrationFile;

        $migrationClass = $this->getMigrationClassName($migrationFile);
        $migration = new $migrationClass();

        echo "Rolling back migration: $migrationClass\n";
        $migration->down();
    }

}
