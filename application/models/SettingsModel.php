<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SettingsModel extends CI_Model {

    // --- BRANDS QUERIES ---
    public function get_all_brands() {
        return $this->db->get('brands')->result();
    }

    public function search_brands_data($search, $last_id, $limit) {
        $this->db->select('*');
        $this->db->from('brands');
        
        if (!empty($search)) {
            $this->db->like('brand_name', $search);
        }
        if (!empty($last_id)) {
            $this->db->where('id >', $last_id);
        }

        $this->db->order_by('id', 'ASC');
        $this->db->limit($limit + 1);
        
        return $this->db->get()->result();
    }

    public function insert_brand($name) {
        return $this->db->insert('brands', ['brand_name' => $name]);
    }

    public function delete_brand($id) {
        return $this->db->delete('brands', ['id' => $id]);
    }

    // --- CATEGORIES QUERIES ---
    public function get_all_categories() {
        return $this->db->get('categories')->result();
    }

    public function search_categories_data($search, $last_id, $limit) {
        $this->db->select('*');
        $this->db->from('categories');
        
        if (!empty($search)) {
            $this->db->like('category_name', $search);
        }
        if (!empty($last_id)) {
            $this->db->where('id >', $last_id);
        }

        $this->db->order_by('id', 'ASC');
        $this->db->limit($limit + 1);
        
        return $this->db->get()->result();
    }

    public function insert_category($name) {
        return $this->db->insert('categories', ['category_name' => $name]);
    }

    public function delete_category($id) {
        return $this->db->delete('categories', ['id' => $id]);
    }
}