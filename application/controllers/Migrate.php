<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller {

    public function index() {
        $this->run_migration('up');
    }

    // Tawagin ito sa browser kung gusto mong burahin ang tables: /migrate/rollback
    public function rollback() {
        $this->run_migration('down');
    }

    private function run_migration($method) {
        $target_db = 'item_management';
        $this->load->database();
        $this->db->conn_id->exec("USE `$target_db`");

        $files = glob(APPPATH . 'migrations/*.php');
        // Kung rollback, i-reverse natin ang order para mauna burahin ang huling ginawa
        if ($method == 'down') rsort($files); 
        else sort($files);

        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $version = substr($filename, 0, 14);
            
            require_once($file);
            $className = 'Migration_' . ucfirst(explode('_', $filename, 2)[1]);
            $migration = new $className();
            
            if ($method == 'up') {
                $query = $this->db->query("SELECT * FROM migrations WHERE version = ?", [$version]);
                if ($query->num_rows() == 0) {
                    echo "Migrating UP: $filename... ";
                    $migration->up();
                    $this->db->insert('migrations', ['version' => $version]);
                    echo "DONE!<br>";
                }
            } else {
                echo "Migrating DOWN: $filename... ";
                $migration->down();
                $this->db->delete('migrations', ['version' => $version]);
                echo "REMOVED!<br>";
            }
        }
        echo "<h2>DATABASE OPERATION COMPLETED!</h2>";
    }
}