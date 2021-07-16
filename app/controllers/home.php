<?php
class Home  extends Controller{
	public function index() {
		$pw = 123;
		echo $hash = password_hash($pw, PASSWORD_DEFAULT);
	}
}
