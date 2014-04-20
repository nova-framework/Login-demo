<?php

class Admin_model extends Model {

	public function __construct(){
		parent::__construct();
	}	

	public function get_hash($username){
		$data = $this->_db->select("SELECT password FROM ".PREFIX."users WHERE username = :username", array(':username' => $username));
		return $data[0]->password;
	}
}