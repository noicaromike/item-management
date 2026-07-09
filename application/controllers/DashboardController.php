<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');

        // SECURITY CHECK: Kung walang active session, balik sa login page
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Need mo munang mag-login!');
            redirect('login');
        }
    }

    public function index() {
        $data['full_name'] = $this->session->userdata('full_name');
        $data['role'] = $this->session->userdata('role');

        $this->load->view('dashboard/index_view', $data);
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }
}