<?php 
class VerifyModel extends DB {
	public function get_key_security($username) {
		$sql = "SELECT user_id AS id, key_security AS uuid FROM users WHERE user_id = '{$username}'; ";
		$data = $this->get_data($sql);
		return count($data) !== 0 ? $data[0] : false;
	}
}
