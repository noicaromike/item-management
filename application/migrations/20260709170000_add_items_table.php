<?php
class Migration_Add_items_table {
    public function up() {
        $CI =& get_instance();
        $CI->load->dbforge();

        $CI->dbforge->add_field([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'brand_name' => ['type' => 'VARCHAR', 'constraint' => '100', 'unique' => TRUE]
        ]);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->create_table('brands', TRUE);

        $CI->dbforge->add_field([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'category_name' => ['type' => 'VARCHAR', 'constraint' => '100', 'unique' => TRUE]
        ]);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->create_table('categories', TRUE);

        $CI->dbforge->add_field([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'item_code'   => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => TRUE],
            'name'        => ['type' => 'VARCHAR', 'constraint' => '150'],
            'cost'        => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'description'=> ['type' => 'TEXT', 'null' => TRUE],
            'picture'     => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE],
            'brand_id'    => ['type' => 'INT', 'constraint' => 11],       
            'category_id' => ['type' => 'INT', 'constraint' => 11]        
        ]);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->create_table('items', TRUE);

        $CI->dbforge->add_field([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'item_id'    => ['type' => 'INT', 'constraint' => 11],
            'qty'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'amount'     => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => TRUE]
        ]);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->create_table('inventory', TRUE);
    }

    public function down() {
        $CI =& get_instance();
        $CI->load->dbforge();
        $CI->dbforge->drop_table('inventory', TRUE);
        $CI->dbforge->drop_table('items', TRUE);
        $CI->dbforge->drop_table('categories', TRUE);
        $CI->dbforge->drop_table('brands', TRUE);
    }
}