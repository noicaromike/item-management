<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SettingsController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('SettingsModel');

        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
    }

    public function index() {
        $data['full_name']  = $this->session->userdata('full_name');
        $data['role']       = $this->session->userdata('role');
        $data['title']      = 'Settings - Brand & Category';

        $this->load->view('settings/index_view', $data);
    }

    // --- JSON API ENDPOINTS FOR KEYSET PAGINATION ---
    public function search_brands() {
        $search = $this->input->get('q', TRUE);
        $last_id = $this->input->get('last_id', TRUE);
        $limit = 5;

        $query = $this->SettingsModel->search_brands_data($search, $last_id, $limit);

        $has_more = count($query) > $limit;
        if ($has_more) {
            array_pop($query);
        }

        $response = [
            'data' => $query,
            'has_more' => $has_more,
            'last_id' => !empty($query) ? end($query)->id : null
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function search_categories() {
        $search = $this->input->get('q', TRUE);
        $last_id = $this->input->get('last_id', TRUE);
        $limit = 5;

        $query = $this->SettingsModel->search_categories_data($search, $last_id, $limit);

        $has_more = count($query) > $limit;
        if ($has_more) {
            array_pop($query);
        }

        $response = [
            'data' => $query,
            'has_more' => $has_more,
            'last_id' => !empty($query) ? end($query)->id : null
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    // --- FORM ACTIONS ---
    public function add_brand() {
        $brand_name = $this->input->post('brand_name', TRUE);
        if (!empty($brand_name)) {
            $this->SettingsModel->insert_brand($brand_name);
            $this->session->set_flashdata('success', 'Brand successfully added!');
            $this->session->set_flashdata('active_tab', 'brands-section');
        }
        redirect('settings');
    }

    public function delete_brand($id) {
        $this->SettingsModel->delete_brand($id);
        $this->session->set_flashdata('success', 'Brand deleted successfully.');
        $this->session->set_flashdata('active_tab', 'brands-section');
        redirect('settings');
    }

    public function add_category() {
        $category_name = $this->input->post('category_name', TRUE);
        if (!empty($category_name)) {
            $this->SettingsModel->insert_category($category_name);
            $this->session->set_flashdata('success', 'Category successfully added!');
            $this->session->set_flashdata('active_tab', 'categories-section');
        }
        redirect('settings');
    }

    public function delete_category($id) {
        $this->SettingsModel->delete_category($id);
        $this->session->set_flashdata('success', 'Category deleted successfully.');
        $this->session->set_flashdata('active_tab', 'categories-section');
        redirect('settings');
    }

    
    public function update_brand($id) {
        $new_name = $this->input->post('update_name', TRUE);
        if (!empty($new_name)) {
            $this->db->where('id', $id)->update('brands', ['brand_name' => $new_name]);
            $this->session->set_flashdata('success', 'Brand updated successfully!');
            $this->session->set_flashdata('active_tab', 'brands-section');
        }
        redirect('settings');
    }

    public function update_category($id) {
        $new_name = $this->input->post('update_name', TRUE);
        if (!empty($new_name)) {
            $this->db->where('id', $id)->update('categories', ['category_name' => $new_name]);
            $this->session->set_flashdata('success', 'Category updated successfully!');
            $this->session->set_flashdata('active_tab', 'categories-section');
        }
        redirect('settings');
    }
}