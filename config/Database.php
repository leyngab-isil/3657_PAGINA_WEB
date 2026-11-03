<?php

namespace Config;

use PDO;
use PDOException;

class Database
{
    private string $host = 'localhost';
    private string $database = 'db_restaurante';
    private string $username = 'root';
    private string $password = '123456';

    public function connect(): PDO
    {
        try {

            $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4";

            $connection = new PDO(
                $dsn,
                $this->username,
                $this->password
            );

            $connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $connection->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

            $connection->setAttribute(
                PDO::ATTR_EMULATE_PREPARES,
                false
            );

            return $connection;

        } catch (PDOException $e) {

            throw new PDOException(
                'Error al conectar con la base de datos.'
            );
        }
    }
}