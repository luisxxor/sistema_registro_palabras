<?php
class Captioner_model extends CI_Model {

    public function form_insert($data){
        $this->db->insert('captioners', array(
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'rut' => $data['rut'],
        ));
        return $this->db->affected_rows();
    }

    public function form_update($data) {
        $update_data = array(
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'rut' => $data['rut'],
        );

        $this->db->update('captioners',$update_data,array('id' => $data['id']));
        return $this->db->affected_rows();
    }

    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('captioners');

        return $this->db->affected_rows();
    }
    
    public function getAll() {
        $this->db->select('id,name,lastname,rut');
        $this->db->from('captioners');
        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }
}