<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function register() {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }
        $this->load->view('templates/header');
        $this->load->view('auth/register_view');
        $this->load->view('templates/footer');
    }

    public function process_register() {

        $this->load->model('UserModel');

        $sanitized_post = sanitize_input($this->input->post());
        $sanitized_post['password'] = $this->input->post('password');

        if (empty($sanitized_post['username']) || empty($sanitized_post['password'])) {
            $this->session->set_flashdata('error', 'Username at Password ay kinakailangan!');
            redirect('register');
        }

        $data = [
            'username'  => $sanitized_post['username'],
            'password'  => $sanitized_post['password'],
            'full_name' => $sanitized_post['full_name'],
            'role'      => 'staff'
        ];

        if ($this->UserModel->create_user($data)) {
            $this->session->set_flashdata('success', 'Registration successful!');
            redirect('login');
        } else {
            $this->session->set_flashdata('error', 'Registration failed. Try again.');
            redirect('register');
        }
    }

    public function login() {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }
        $this->load->view('templates/header');
        $this->load->view('auth/login_view'); 
        $this->load->view('templates/footer');
    }

    public function login_process(){

        $this->load->model('UserModel');

        $sanitized_post = sanitize_input($this->input->post());
        
        $username = $sanitized_post['username'];
        $password = $this->input->post('password');

        if (empty($username) || empty($password)) {
            $this->session->set_flashdata('error', 'Username and Password is Required!');
            redirect('login');
        }


        $user = $this->UserModel->get_user_by_username($username,$password);

        if ($user) {
            
            $session_data = [
                'user_id'   => $user->id,
                'username'  => $user->username,
                'full_name' => $user->full_name,
                'role'      => $user->role,
                'logged_in' => TRUE
            ];
            $this->session->set_userdata($session_data);

            $this->session->set_flashdata('success', 'Welcome back, ' . $user->full_name . '!');
            redirect('dashboard'); 
            
        } else {
            $this->session->set_flashdata('error', 'Incorrect Username or Password');
            redirect('login');
        }
    }
}