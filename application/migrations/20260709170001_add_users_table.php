<?php
class Migration_Add_users_table {
    public function up() {
        $CI =& get_instance();
        $CI->load->dbforge();
        $CI->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'username' => ['type' => 'VARCHAR', 'constraint' => '50'],
            'full_name' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'password' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'role' => ['type' => 'ENUM("admin", "staff")', 'default' => 'staff']
        ]);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->create_table('users', TRUE);
    }
    public function down() {
        $CI =& get_instance();
        $CI->load->dbforge();
        $CI->dbforge->drop_table('users', TRUE);
    }
}