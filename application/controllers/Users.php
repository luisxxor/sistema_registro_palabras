<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
  
	public function __construct() {
		parent::__construct();
		$this->load->model('User_model', 'user');
    	$this->load->library('form_validation');
	}
	
	public function index()
	{
		if ($this->session->userdata('is_authenticated') == FALSE) {
			redirect('users/login');
		} else {
			$data['title'] = 'Home';
			$data['content'] = 'users/home';
			$this->user->setUserID($this->session->userdata('user_id'));
			$this->load->view('template', $data);
		}
	}

	public function login()
	{
		if ($this->session->userdata('is_authenticated') == TRUE) {
			redirect('users/');
		}

		$data['title'] = 'Inicio de sesión';
		$this->load->view('users/login', $data);
	}

	function doLogin() {

		$this->form_validation->set_rules('username', 'Nombre de usuario', 'required');
		$this->form_validation->set_rules('password', 'Contraseña', 'required');

		$validations = array(
			array(
				'field' => 'username',
				'label' => 'Nombre de Usuario',
				'rules' => 'required|trim'
			),
			array(
				'field' => 'password',
				'label' => 'Contraseña',
				'rules' => 'required|trim',
				'errors' => array(
					'required' => 'Este campo es obligatorio.'
				)
			)
		);

		$this->form_validation->set_rules($validations);

		if($this->form_validation->run() == FALSE) {
			$this->load->view('users/login');
		} else {  
			$sessArray = array();

			$username = $this->input->post('username');
			$password = $this->input->post('password');

			$this->user->setUsername($username);
			$this->user->setPassword($password);

			$result = $this->user->login();
			
			if($result) {
				foreach($result as $row) {
					$sessArray = array(
					'id' => $row->id,
					'username' => $row->username,
					'is_authenticated' => TRUE,
					'is_admin' => $row->is_admin
				);

				$this->session->set_userdata($sessArray);
			}
				redirect('users');
			} else {
				redirect('users/login?msg=1');
			} 
		}
	}

	function getUsers() {
		if (!$this->session->userdata('is_admin')) {
			redirect('403');
		}
		$data['users'] = $this->user->getAll();
		header('Content-Type: application/json');
		echo json_encode(['users' => $data['users']]);

	}

	public function logout() {
        $this->session->unset_userdata('id');
        $this->session->unset_userdata('name');
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('is_authenticated');
        $this->session->sess_destroy();
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
        redirect('login');
	}
	
	public function list() {
		if ($this->session->userdata('is_authenticated') == FALSE) {
			redirect('users/login');
		} else if($this->session->userdata('is_admin') == FALSE) {
			redirect('users/');
		}

		$data['title'] = 'Listado de Usuarios';
		$data['content'] = 'users/list';
		$data['vue'] = TRUE;
		$this->load->view('template',$data);
	}

	public function create() {
		if ($this->session->userdata('is_authenticated') == FALSE) {
			echo json_encode(['status' => '403','message' => 'Permission Denied']);
			return null;
		} else if($this->session->userdata('is_admin') == FALSE) {
			echo json_encode(['status' => '403','message' => 'Permission Denied']);
			return null;
		}

		$data = json_decode($this->input->post('user_form'),true);

		$result = $this->user->form_insert($data);

		if($result > 0)
		{
			echo json_encode(['status' => '201', 'message' => 'Usuario creado exitosamente']);
		}
		else
		{
			echo json_encode(['status' => '500', 'message' => 'Usuario no creado, ha ocurrido un error']);
		}
	}

	public function update() {
		if ($this->session->userdata('is_authenticated') == FALSE) {
			echo json_encode(['status' => '403','message' => 'Permission Denied']);
			return null;
		} else if($this->session->userdata('is_admin') == FALSE) {
			echo json_encode(['status' => '403','message' => 'Permission Denied']);
			return null;
		}

		$data = json_decode($this->input->post('user_form'),true);

		$result = $this->user->form_update($data);

		if($result > 0)
		{
			echo json_encode(['status' => '200', 'message' => 'Usuario actualizado exitosamente']);
		}
		else
		{
			echo json_encode(['status' => '500', 'message' => 'Usuario no actualizado, ha ocurrido un error', 'response' => $result]);
		}
	}

	public function delete() {
		if ($this->session->userdata('is_authenticated') == FALSE) {
			echo json_encode(['status' => '403','message' => 'Permission Denied']);
			return null;
		} else if($this->session->userdata('is_admin') == FALSE) {
			echo json_encode(['status' => '403','message' => 'Permission Denied']);
			return null;
		}

		$id = $this->input->post('id');

		$result = $this->user->delete($id);

		if($result > 0)
		{
			echo json_encode(['status' => '200', 'message' => 'Usuario eliminado correctamente']);
		}
		else
		{
			echo json_encode(['status' => '500', 'message' => 'Usuario no eliminado, ha ocurrido un error', 'response' => $result]);
		}
	}

	public function usernameIsAvailable() {
		if ($this->session->userdata('is_authenticated') == FALSE) {
			echo json_encode(['status' => '403','message' => 'Permission Denied']);
			return null;
		} else if($this->session->userdata('is_admin') == FALSE) {
			echo json_encode(['status' => '403','message' => 'Permission Denied']);
			return null;
		}

		$data = json_decode($this->input->post('usernameTest'),true);

		$result = $this->user->usernameIsAvailable($data['id'],$data['username']);

		echo json_encode(['response' => $result]);
	}
}