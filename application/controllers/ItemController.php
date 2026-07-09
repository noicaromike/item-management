<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ItemController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('ItemModel');
        $this->load->model('SettingsModel');

        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
    }

    public function index() {
        $data['full_name']  = $this->session->userdata('full_name');
        $data['role']       = $this->session->userdata('role');
        $data['title']      = 'Items & Inventory Management';
        
        $data['brands']     = $this->SettingsModel->get_all_brands();
        $data['categories'] = $this->SettingsModel->get_all_categories();

        $this->load->view('items/index_view', $data);
    }

    public function add_item() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules(
            'sku', 
            'SKU/Item Code', 
            'required|is_unique[items.item_code]',
            array('is_unique' => 'Ang SKU / Unique Code na ito ay ginagamit na ng ibang produkto.')
        );

        if ($this->form_validation->run() == FALSE) {
            // Ibalik ang error bilang JSON response
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400) // Bad Request status
                ->set_output(json_encode(['error' => validation_errors(' ', ' ')]));
        }

        $picture = NULL;
        if (!empty($_FILES['item_image']['name'])) {
            $config['upload_path']   = './uploads/items/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size']      = 2048;
            $config['encrypt_name']  = TRUE;

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('item_image')) {
                $upload_data = $this->upload->data();
                $picture = $upload_data['file_name'];
            }
        }

        $item_data = [
            'name'        => $this->input->post('item_name', TRUE),
            'item_code'   => $this->input->post('sku', TRUE),
            'cost'        => $this->input->post('price', TRUE),
            'brand_id'    => $this->input->post('brand_id', TRUE),
            'category_id' => $this->input->post('category_id', TRUE),
            'picture'     => $picture,
            'description' => 'No description provided.'
        ];

        $qty    = $this->input->post('quantity', TRUE);
        $amount = $this->input->post('price', TRUE);

        $this->ItemModel->insert_item_with_inventory($item_data, $qty, $amount);

        $this->session->set_flashdata('success', 'Item successfully added to inventory!');
        // Success JSON response
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => true]));
    }

    public function update_item($id) {
        // 1. Check duplicate SKU para sa IBANG items (id != $id)
        $input_sku = $this->input->post('sku', TRUE);
        $check_duplicate = $this->db->get_where('items', ['item_code' => $input_sku, 'id !=' => $id])->row();

        if ($check_duplicate) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'Ang SKU / Unique Code na ito ay nakatalaga na sa ibang produkto.']));
        }

        // 2. Pag-handle sa Image Upload (kung may binago)
        $existing_item = $this->ItemModel->get_item_by_id($id);
        $picture = NULL; // GAWIN NATING DEFAULT NA NULL!

        // TINGNAN KUNG MAY INUPLOAD NA BAGONG LARAWAN
        if (!empty($_FILES['item_image']['name'])) {
            $config['upload_path']   = './uploads/items/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size']      = 2048;
            $config['encrypt_name']  = TRUE;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('item_image')) {
                // Burahin ang lumang file sa folder bago itapon ang bago
                if ($existing_item->picture && file_exists('./uploads/items/' . $existing_item->picture)) {
                    unlink('./uploads/items/' . $existing_item->picture);
                }
                $upload_data = $this->upload->data();
                $picture = $upload_data['file_name']; // Gamitin ang bagong upload file name
            } else {
                // Kung may error sa upload (ex: lumagpas sa 2MB), ibalik ang error sa modal
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['error' => $this->upload->display_errors(' ', ' ')]));
            }
        } else {
            // KUNG WALANG INUPLOAD:
            // Titingnan natin kung ang preview box sa screen ay kasalukuyang nakatago (na-remove gamit ang X button)
            $remove_tracker = $this->input->post('remove_image');
            
            if ($remove_tracker === "1") {
                // Kung pinindot ang X button, burahin ang file sa folder at panatilihing NULL sa DB
                if ($existing_item->picture && file_exists('./uploads/items/' . $existing_item->picture)) {
                    unlink('./uploads/items/' . $existing_item->picture);
                }
                $picture = NULL;
            } else {
                // Kung hindi naman pinindot ang X (ibig sabihin nandoon pa rin ang preview ng sapatos sa modal), panatilihin ang lumang image name
                $picture = $existing_item->picture;
            }
        }

        // Isaksak sa item_data array ang selyadong picture value
        $item_data = [
            'name'        => $this->input->post('item_name', TRUE),
            'item_code'   => $this->input->post('sku', TRUE),
            'cost'        => $this->input->post('price', TRUE),
            'brand_id'    => $this->input->post('brand_id', TRUE),
            'category_id' => $this->input->post('category_id', TRUE),
            'picture'     => $picture
        ];

        $qty    = $this->input->post('quantity', TRUE);
        $amount = $this->input->post('price', TRUE);

        $this->ItemModel->update_item_with_inventory($id, $item_data, $qty, $amount);
        
        $this->session->set_flashdata('success', 'Product profile configuration saved.');
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => true]));
    }

    public function delete_item($id) {
        $item = $this->ItemModel->get_item_by_id($id);
        if ($item) {
            if ($item->picture && file_exists('./uploads/items/' . $item->picture)) {
                unlink('./uploads/items/' . $item->picture);
            }
            $this->ItemModel->delete_item_with_inventory($id);
            $this->session->set_flashdata('success', 'Item removed from system index.');
        }
        redirect('items');
    }

    public function search_items() {
        $search = $this->input->get('q', TRUE);
        $last_id = $this->input->get('last_id', TRUE);
        $limit = 5;

        $query = $this->ItemModel->search_items_data($search, $last_id, $limit);

        $has_more = count($query) > $limit;
        if ($has_more) {
            array_pop($query);
        }

        // I-map natin ang lumang front-end keys para walang masira sa javascript rendering mo
        foreach ($query as $row) {
            $row->item_name = $row->name;
            $row->sku       = $row->item_code;
            $row->price     = $row->cost;
            $row->quantity  = $row->qty;
            $row->item_image = $row->picture;
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
}