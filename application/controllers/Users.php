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
				'label' => 'Password',
				'rules' => 'required|trim',
				'errors' => array(
					'required' => 'You must provide a %s.'
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

		$data['title'] = 'Listado de Usuario';
		$data['content'] = 'users/list';
		$this->load->view('template',$data);
	}
}