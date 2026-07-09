<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ItemModel extends CI_Model {

    public function search_items_data($search, $last_id, $limit) {
        // Naka-JOIN na ngayon pati ang inyong inventory table
        $this->db->select('items.*, brands.brand_name, categories.category_name, inventory.qty, inventory.amount');
        $this->db->from('items');
        $this->db->join('brands', 'brands.id = items.brand_id', 'left');
        $this->db->join('categories', 'categories.id = items.category_id', 'left');
        $this->db->join('inventory', 'inventory.item_id = items.id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('items.name', $search);
            $this->db->or_like('items.item_code', $search);
            $this->db->group_end();
        }
        
        if (!empty($last_id)) {
            $this->db->where('items.id >', $last_id);
        }

        $this->db->order_by('items.id', 'ASC');
        $this->db->limit($limit + 1);
        
        return $this->db->get()->result();
    }

    public function insert_item_with_inventory($item_data, $qty, $amount) {
        $this->db->trans_start(); // Simulan ang SQL Transaction

        // 1. Insert muna sa items table
        $this->db->insert('items', $item_data);
        $item_id = $this->db->insert_id(); // Kukunin ang bagong gawang ID

        // 2. Insert naman sa inventory table gamit ang nakuhang item_id
        $inventory_data = [
            'item_id'    => $item_id,
            'qty'        => $qty,
            'amount'     => $amount,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('inventory', $inventory_data);

        $this->db->trans_complete(); // Tapusin ang transaction
        return $this->db->trans_status();
    }

    public function get_item_by_id($id) {
        $this->db->select('items.*, inventory.qty, inventory.amount');
        $this->db->from('items');
        $this->db->join('inventory', 'inventory.item_id = items.id', 'left');
        $this->db->where('items.id', $id);
        return $this->db->get()->row();
    }

    public function update_item_with_inventory($id, $item_data, $qty, $amount) {
        $this->db->trans_start();

        // 1. Update items table
        $this->db->where('id', $id)->update('items', $item_data);

        // 2. Update inventory table
        $inventory_data = [
            'qty'        => $qty,
            'amount'     => $amount,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->where('item_id', $id)->update('inventory', $inventory_data);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function delete_item_with_inventory($id) {
        $this->db->trans_start();
        $this->db->delete('inventory', ['item_id' => $id]);
        $this->db->delete('items', ['id' => $id]);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}