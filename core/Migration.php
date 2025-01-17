<?php

namespace Core;

use PDO;

class Migration {
    protected PDO $connection;

    public function __construct() {
        $this->connection = Database::connect();
    }

    public function run(string $migrationName) {
        if (method_exists($this, $migrationName)) {
            $this->$migrationName();
        } else {
            echo "Migration method '$migrationName' does not exist.\n";
        }
    }
}
