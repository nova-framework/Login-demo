<?php

class Admin extends Controller {

	public function __construct(){
		parent::__construct();
	}	

	public function admin(){

		if(Session::get('loggin') == false){
			url::redirect('admin/login');
		}

		$data['title'] = 'Admin';

		$this->view->rendertemplate('header',$data);
		$this->view->render('admin/admin',$data);
		$this->view->rendertemplate('footer',$data);	
	}

	public function login(){

		if(Session::get('loggin') == true){
			url::redirect('admin');
		}

		if(isset($_POST['submit'])){

			$username = $_POST['username'];
			$password = $_POST['password'];

			$admin = $this->loadModel('admin_model');

			if(Password::verify($password, $admin->get_hash($username)) == false){
				die('wrong username or password');
			} else {
				Session::set('loggin',true);
				Url::redirect('admin');
			}

		}

		$data['title'] = 'Login';

		$this->view->rendertemplate('header',$data);
		$this->view->render('admin/login',$data);
		$this->view->rendertemplate('footer',$data);	
	}

	public function logout(){

		Session::destroy();
		Url::redirect('admin');

	}
}
