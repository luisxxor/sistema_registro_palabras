<?php
class Error_model extends CI_Model {

    public function form_insert($data){
        $this->db->insert('errors', array(
            'word' => $data['word'],
            'captioner_id' => $data['captioner_id'],
            'error_date' => $data['error_date'],
            'created_by' => $data['created_by'],
        ));
        return $this->db->affected_rows();
    }

    public function form_update($data) {
        $update_data = array(
            'word' => $data['word'],
            'captioner_id' => $data['captioner_id'],
            'error_date' => $data['error_date'],
            'created_by' => $data['created_by'],
        );

        $this->db->update('errors',$update_data,array('id' => $data['id']));
        return $this->db->affected_rows();
    }

    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('errors');

        return $this->db->affected_rows();
    }
    
    public function getAll() {
        $this->db->select('errors.id,errors.word,CONCAT(captioners.name," ",captioners.lastname) as fullname,errors.error_date,users.username');
        $this->db->from('errors');
        $this->db->join('captioners', 'errors.captioner_id = captioners.id');
        $this->db->join('users', 'errors.created_by = users.id');
        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }

    public function find($id) {
        $this->db->select('id,word,captioner_id,error_date');
        $this->db->from('errors');
        $this->db->where('id',$id);
        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }
}