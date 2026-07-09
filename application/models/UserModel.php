<?php
class UserModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function create_user($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->db->insert('users', $data);
    }

    public function get_user_by_username($username,$password){
        $query = $this->db->get_where('users', ['username' => $username]);
        $user = $query->row();

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return FALSE;
            
    }
}