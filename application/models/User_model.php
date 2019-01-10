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

    public function form_insert($data){
        $this->db->insert('users', array(
            'username' => $data['username'],
            'password' => $data['password'],
            'is_admin' => $data['is_admin'] || 0,
        ));
        return $this->db->affected_rows();
    }

    public function form_update($data) {
        $update_data = array(
            'username' => $data['username'],
            'is_admin' => $data['is_admin'] || 0,
        );

        if(isset($data['password']))
        {
            if($data['password'] != null)
            {
                $update_data['password'] = $data['password'];
            }
        }

        $this->db->update('users',$update_data,array('id' => $data['id']));
        return $this->db->affected_rows();
    }

    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('users');

        return $this->db->affected_rows();
    }

    public function getUserInfo() {
        $this->db->select(array('u.id', 'u.username'));
        $this->db->from('users as u');
        $this->db->where('u.id', $this->_userID);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    function login() {
        $this->db->select('id, username, is_admin');
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
    
    public function getAll() {
        $this->db->select('id,username,is_admin');
        $this->db->from('users');
        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }

    public function usernameIsAvailable($id,$username) {
        $this->db->select('id');
        $this->db->from('users');
        $this->db->where('username',$username);
        $this->db->where('id !=',$id);
        $query = $this->db->get();
        return $query->num_rows() == 0;
    }
}