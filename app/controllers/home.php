<?php
class Home  extends Controller{
	public function index() {
		$pw = 'matkhau';
		echo $hash = password_hash($pw, PASSWORD_DEFAULT);
		$verify = password_verify('matkhau', $hash);
		// echo $verify;
	}
}
