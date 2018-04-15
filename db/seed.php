<?php

namespace database;

class seed {
    
    protected $container;
    protected $db;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
        $this->db = $this->container->db;
    }

    function update() {
        if (!$this->container->db->has("sqlite_master", ["AND" => ["type" => "table", "OR" => [
            "name" => ["users", "audits", "currentState"]
        ]]])) {
            $this->seed();
        }
    }

    function seed() {
        $this->container->logger->addInfo("Seeding database");

        $this->db->query("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY,
            name TEXT,
            pass TEXT,
            token TEXT NULL,
            lastActive INTEGER NULL
        );");

        if (!$this->db->has("users", ["name" => "admin"])) {
            $this->container->logger->addInfo("Seed: admin");
            $this->db->insert("users", [
                "name" => "admin", 
                "pass" => password_hash("admin", PASSWORD_DEFAULT)
            ]);
        }

        $this->db->query("CREATE TABLE IF NOT EXISTS audits (
            id INTEGER PRIMARY KEY,
            uname TEXT,
            station TEXT,
            mode TEXT,
            chtime INTEGER
        );");

        $this->db->query("CREATE TABLE IF NOT EXISTS currentState (
            id INTEGER PRIMARY KEY,
            uname TEXT,
            station TEXT,
            mode TEXT,
            chtime INTEGER
        );");

    }
}
