<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
 
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
/**
 * Description of Users Controller
 *
 * @author Team TechArise
 *
 * @email info@techarise.com
 */
   
	public function __construct() {
		parent::__construct();
		$this->load->model('User_model', 'user');
    	$this->load->library('form_validation');
	}
  // Dashboard
	public function index()
	{
		if ($this->session->userdata('is_authenticated') == FALSE) {
			redirect('users/login'); // the user is not logged in, redirect them!
		} else {
			$data['title'] = 'Home';
			$this->user->setUserID($this->session->userdata('user_id'));
			$data['userInfo'] = $this->user->getUserInfo();
			$this->load->view('users/home', $data);
		}
	}
        // Login
	public function login()
	{
		$data['title'] = 'Inicio de sesión';
		$this->load->view('users/login', $data);
	}
  // Login Action 
	function doLogin() {
		// Check form  validation

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
			//Field validation failed.  User redirected to login page
			$this->load->view('users/login');
		} else {  
			$sessArray = array();
			//Field validation succeeded.  Validate against database
			$username = $this->input->post('username');
			$password = $this->input->post('password');

			$this->user->setUsername($username);
			$this->user->setPassword($password);

			//query the database
			$result = $this->user->login();
			
			if($result) {
				foreach($result as $row) {
					$sessArray = array(
					'id' => $row->id,
					'username' => $row->username,
					'is_authenticated' => TRUE
				);

				$this->session->set_userdata($sessArray);
			}
				redirect('users');
			} else {
				redirect('users/login?msg=1');
			} 
		}
	}
  // Logout
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
}