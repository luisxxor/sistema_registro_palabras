<?php
class User_model extends CI_Model {

    private $_userID;
    private $_userName;
    private $_password;
    private $_isAdmin;

    public function setUserID($userID) {
        $this->_userID = $userID;
    }

    public function setUsername($username) {
        $this->_userName = $username;
    }

    public function setPassword($password) {
        $this->_password = $password;
    }       

    public function insertar($data){
        $this->db->insert('users', array(
            'username' => $data['username'],
            'password' => $data['password'],
            'is_admin' => $data['is_admin'],
        ));
    }

    public function getUserInfo() {
        $this->db->select(array('u.id', 'u.username'));
        $this->db->from('users as u');
        $this->db->where('u.id', $this->_userID);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    function login() {
        $this->db->select('id, username');
        $this->db->from('users');
        $this->db->where('username', $this->_userName);
        $this->db->where('password', $this->_password);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query -> num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }    
}