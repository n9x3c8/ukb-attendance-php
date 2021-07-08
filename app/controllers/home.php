<?php
class Home  extends Controller{
	public function index() {
		$pw = 'password123';
		echo $hash = password_hash($pw, PASSWORD_DEFAULT);
	}
}
