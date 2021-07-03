<?php 
class AccountModel extends DB {

	public function get_password_by_username($username) {
		$sql = "SELECT user_password AS password, key_security, UP.permission_id FROM users AS U, user_permission AS UP
		WHERE U.user_id = '{$username}'
		AND
		U.user_id = UP.user_id; ";

		$data = $this->get_data($sql);

		if(count($data) !== 0) {
			return $data[0];
		}
		return -1;
	}

	public function update_key_security($username, $key) {
		$sql = "UPDATE users SET key_security = '{$key}' WHERE user_id = '{$username}'; ";
		return$this->_connection->query($sql);
	}


	// public function get_user_id($username, $password) {
	// 	$sql = " SELECT UP.permission_id FROM users AS U, user_permission AS UP ";
	// 	$sql .= " WHERE U.user_id = UP.user_id AND UP.user_id = '{$username}' AND U.user_password = '{$password}' LIMIT 1; ";
	// 	// $sql = " CALL log_in( '{$username}', '{$password}' ) ";
	// 	$result = $this->get_data($sql);

	// 	return count($result) === 1 ? $result[0]['permission_id'] : -1;
	// }


	//Student
	public function get_info_details_student($student_id = '') {
		$sql = "SELECT * FROM students WHERE student_id = '{$student_id}'; ";
		return $this->get_data($sql);
	}

	public function update_info_details_student($id, $name, $address, $email, $phone) {
		$sql = "UPDATE students ";
		$sql .= " SET student_address = '{$address}', student_email = '{$email}', student_numphone = '{$phone}' ";
		$sql .= " WHERE student_id = '{$id}'; ";	
		$this->connect();
		return $this->_connection->query($sql);
	}


	// Teacher
	public function get_info_details_teacher($teacher_id) {
		$sql = "SELECT * FROM teachers WHERE teacher_id = '{$teacher_id}';";
		return $this->get_data($sql);
	}

	public function update_info_details_teacher($teacher_id, $address, $phone, $email) {
		$sql = "UPDATE teachers SET teacher_address = '{$address}', teacher_numphone = '{$phone}', teacher_email = '{$email}'  WHERE teacher_id = '{$teacher_id}'; ";
		$this->connect();
		return $this->_connection->query($sql);
	}


	public function get_avatar_user($username, $array_info) {
		$avatar_name = null;

		$table_name = $array_info[0];
		$user_id = $array_info[1];
		$avatar_user = $array_info[2];
		
		$sql = "SELECT {$avatar_user} FROM {$table_name} WHERE {$user_id} = '{$username}'; ";

		$data = $this->get_data($sql);
		if($data) {
			$avatar_name = $data[0][$avatar_user];
		}
		return ['avatar' => $avatar_name];
	}

	public function update_avatar_filename($username, $avatar_name, $array_info) {
		$table_name = $array_info[0];
		$user_id = $array_info[1];
		$avatar_user = $array_info[2];
		
		$sql = "UPDATE {$table_name} SET {$avatar_user} = '{$avatar_name}' ";
		$sql .= " WHERE {$user_id} = '{$username}'; ";

		$this->connect();
		return $this->_connection->query($sql) ? true : false;
	}


	public function on_reset() {
		$sql = "DELETE FROM attendance_student;";
		$this->connect();
		$state = $this->_connection->query($sql);

		$sql = "DELETE FROM attendances;";
		$this->connect();
		$state = $this->_connection->query($sql);

		$sql = "ALTER TABLE attendance_student AUTO_INCREMENT = 1;";
		$this->connect();
		$state = $this->_connection->query($sql);

		$sql = "ALTER TABLE attendances AUTO_INCREMENT = 1;";
		$this->connect();
		$state = $this->_connection->query($sql);

		$sql = "DELETE FROM list_leave;";
		$this->connect();
		$state = $this->_connection->query($sql);

		$sql = "DELETE FROM leaves;";
		$this->connect();
		$state = $this->_connection->query($sql);

		$sql = "ALTER TABLE list_leave AUTO_INCREMENT = 1;";
		$this->connect();
		$state = $this->_connection->query($sql);

		$sql = "ALTER TABLE leaves AUTO_INCREMENT = 1;";
		$this->connect();
		$state = $this->_connection->query($sql);
		if($state) {
			return ['state' => 1];
		} else 
		return ['state' => -1];
	}
}