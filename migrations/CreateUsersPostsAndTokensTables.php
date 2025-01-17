<?php

namespace migrations;

use Core\Migration;

class CreateUsersPostsAndTokensTables extends Migration
{
    public function up()
    {
        $sql = "
            CREATE TABLE users (
                id SERIAL PRIMARY KEY,
                username VARCHAR(255) NOT NULL UNIQUE,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(50) DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->connection->exec($sql);
        echo "Table 'users' created.\n";

        $sql = "
            CREATE TABLE posts (
                id SERIAL PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                content TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );
        ";
        $this->connection->exec($sql);
        echo "Table 'posts' created.\n";

        $sql = "
            CREATE TABLE tokens (
                id SERIAL PRIMARY KEY,
                user_id INT NOT NULL,
                token VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );
        ";
        $this->connection->exec($sql);
        echo "Table 'tokens' created.\n";
    }

    public function down()
    {
        $sql = "DROP TABLE IF EXISTS tokens;";
        $this->connection->exec($sql);
        echo "Table 'tokens' dropped.\n";

        $sql = "DROP TABLE IF EXISTS posts;";
        $this->connection->exec($sql);
        echo "Table 'posts' dropped.\n";

        $sql = "DROP TABLE IF EXISTS users;";
        $this->connection->exec($sql);
        echo "Table 'users' dropped.\n";
    }
}
