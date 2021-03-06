<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Captioners extends CI_Controller {
  
	public function __construct() {
		parent::__construct();
		$this->load->model('Captioner_model', 'captioner');
    	$this->load->library('form_validation');
	}
	
	public function index()
	{
        if($this->session->userdata('is_authenticated') == FALSE) {
			http_response_code(403);
			echo json_encode(['message' => 'Permission Denied']);
			return null;
		}

		$data['title'] = 'Listado de Captioners';
		$data['content'] = 'captioners/index';
		$data['vue'] = TRUE;
		$this->load->view('template',$data);
	}

	function read() {
		if (!$this->session->userdata('is_authenticated')) {
			http_response_code(403);
			echo json_encode(['message' => 'Permission Denied']);
			return null;
		}
		$data['captioners'] = $this->captioner->getAll();
		header('Content-Type: application/json');
		echo json_encode(['captioners' => $data['captioners']]);

	}

	public function create() {
		if ($this->session->userdata('is_authenticated') == FALSE) {
			echo json_encode(['status' => '403','message' => 'Permission Denied']);
			return null;
		}

		$data = json_decode($this->input->post('captioner_form'),true);

		$result = $this->captioner->form_insert($data);

		if($result > 0)
		{
			echo json_encode(['status' => '201', 'message' => 'Digitador creado exitosamente']);
		}
		else
		{
			echo json_encode(['status' => '500', 'message' => 'Digitador no creado, ha ocurrido un error']);
		}
	}

	public function update() {
		if ($this->session->userdata('is_authenticated') == FALSE) {
			echo json_encode(['status' => '403','message' => 'Permission Denied']);
			return null;
		}

		$data = json_decode($this->input->post('captioner_form'),true);

		$result = $this->captioner->form_update($data);

		if($result > 0)
		{
			echo json_encode(['status' => '200', 'message' => 'Digitador actualizado exitosamente']);
		}
		else
		{
			echo json_encode(['status' => '500', 'message' => 'Digitador no actualizado, ha ocurrido un error', 'response' => $result]);
		}
	}

	public function delete() {
		if ($this->session->userdata('is_authenticated') == FALSE) {
			echo json_encode(['status' => '403','message' => 'Permission Denied']);
			return null;
		}

		$id = $this->input->post('id');

		$result = $this->captioner->delete($id);

		if($result > 0)
		{
			echo json_encode(['status' => '200', 'message' => 'Captioner eliminado correctamente']);
		}
		else
		{
			echo json_encode(['status' => '500', 'message' => 'Captioner no eliminado, ha ocurrido un error', 'response' => $result]);
		}
	}
}