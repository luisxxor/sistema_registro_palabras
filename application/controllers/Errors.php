<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Errors extends CI_Controller {
  
	public function __construct() {
		parent::__construct();
		$this->load->model('Error_model', 'error');
    	$this->load->library('form_validation');
	}
	
	public function index()
	{
		if($this->input->get('id') == NULL)
		{
			$data['title'] = 'Listado de errores';
			$data['content'] = 'captioners_errors/index';
			$data['vue'] = TRUE;
			$this->load->view('template',$data);
		}
		else
		{
			if($this->input->get('id') != NULL)
			{
				echo json_encode(['captioner_error' => $this->error->find($this->input->get('id'))]);
			}
		}
	}

	function read() {
		if (!$this->session->userdata('is_authenticated')) {
			http_response_code(403);
			echo json_encode(['message' => 'Permission Denied']);
			return null;
		}
		$data['captioners_errors'] = $this->error->getAll();
		header('Content-Type: application/json');
		echo json_encode(['captioners_errors' => $data['captioners_errors']]);

	}

	public function create() {
		if ($this->session->userdata('is_authenticated') == FALSE) {
			http_response_code(403);
			echo json_encode(['message' => 'Permission Denied']);
			return null;
		}

		$data = json_decode($this->input->post('error_form'),true);
		$data['created_by'] = $this->session->userdata('id');

		$result = $this->error->form_insert($data);

		if($result > 0)
		{
			echo json_encode(['status' => '201', 'message' => 'Error creado exitosamente']);
		}
		else
		{
			echo json_encode(['status' => '500', 'message' => 'Error no creado, ha ocurrido un problema']);
		}
	}

	public function update() {
		if ($this->session->userdata('is_authenticated') == FALSE) {
			http_response_code(403);
			echo json_encode(['message' => 'Permission Denied']);
			return null;
		} else if($this->session->userdata('is_admin') == FALSE) {
			http_response_code(403);
			echo json_encode(['message' => 'Permission Denied']);
			return null;
		}

		$data = json_decode($this->input->post('error_form'),true);
		$data['created_by'] = $this->session->userdata('id');

		$result = $this->error->form_update($data);

		if($result > 0)
		{
			echo json_encode(['status' => '200', 'message' => 'Error actualizado exitosamente']);
		}
		else
		{
			echo json_encode(['status' => '500', 'message' => 'Error no actualizado, ha ocurrido un problema', 'response' => $result]);
		}
	}

	public function delete() {
		if ($this->session->userdata('is_authenticated') == FALSE) {
			http_response_code(403);
			echo json_encode(['message' => 'Permission Denied']);
			return null;
		} else if($this->session->userdata('is_admin') == FALSE) {
			http_response_code(403);
			echo json_encode(['message' => 'Permission Denied']);
			return null;
		}

		$id = $this->input->post('id');

		$result = $this->error->delete($id);

		if($result > 0)
		{
			echo json_encode(['status' => '200', 'message' => 'Error eliminado correctamente']);
		}
		else
		{
			echo json_encode(['status' => '500', 'message' => 'Error no eliminado, ha ocurrido un problema', 'response' => $result]);
		}
	}

	public function errorIsRegistered() {
		if($this->input->method(TRUE) == 'POST')
		{
			if($this->input->post('error_form'))
			{
				$data = json_decode($this->input->post('error_form'),TRUE);

				$response = $this->error->errorIsAlreadyRegistered($data['id'],$data['word'],$data['captioner_id']);

				echo json_encode([
					'errorIsRegistered' => $response
				]);
			}
			else
			{
				http_response_code(400);
				echo json_encode([
					'message' => 'bad request'
				]);
			}
		}
		else
		{
			http_response_code(400);
			echo json_encode([
				'message' => 'bad request'
			]);
		}
	}
}