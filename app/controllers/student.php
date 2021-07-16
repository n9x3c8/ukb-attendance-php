<?php 

class Student extends Controller {

	//lay ds sv đi học buổi hôm đó (trạng thai)
	public function list_student($username = null, $uuid = null, $class_id = null, $subject_id = null, $current_date = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($username, $uuid);

			$student = $this->model('StudentModel');
			$data = $student->get_list_student($class_id, $subject_id, $current_date);
			exit( json_encode($data) );
		}
	}
	

	public function get_list_student_by_options($username = null, $uuid = null, $class_id = null, $subject_id = null, $current_date = null, $is_enable = null, $leave_denine = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($username, $uuid);
			$student = $this->model('StudentModel');
			$data = $student->get_list_student_by_options($class_id, $subject_id, $current_date, $is_enable, $leave_denine);
			exit(json_encode($data));
		}
	}


	//ds đơn xin nghỉ của SV
	public function leave_application($student_id = null, $uuid = null, $current_date = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($student_id, $uuid);

			$student = $this->model('StudentModel');
			$data = $student->get_leave_application($student_id, $current_date);
			exit( json_encode($data) );
		}

	}


	public function edit_take_leave($username = null, $uuid = null) {
		if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$this->verify($username, $uuid);

			$data = json_decode(file_get_contents('php://input'), true);
			$leave_id = $data['leave_id'];
			$leave_time = $data['leave_time'];
			$leave_reason = $data['leave_reason'];
			$take_leave_date = $data['take_leave_date'];

			$student = $this->model('StudentModel');
			$data = $student-> update_take_leave ($leave_id, $leave_time, $leave_reason, $take_leave_date);
			exit( json_encode($data) );
		}
	}

	public function remove_take_leave($username = null, $uuid = null, $leave_id = null) {
		if($_SERVER['REQUEST_METHOD']  == 'GET') {
			$this->verify($username, $uuid);
			
			$student = $this->model('StudentModel');
			$data = $student->delete_take_leave($leave_id);
			exit( json_encode($data) );
		}
	}

	private function verify($username = null, $uuid = null) {
		$verify = $this->model('VerifyModel');
		$data = $verify->get_key_security($username);

		if(!$data) {
			exit(json_encode(['state' => -403]));
		}
		
		if(strtolower($data['id']) !== strtolower($username) || $data['uuid'] !== $uuid) {
			exit(json_encode(['state' => -403]));
		}
	}


}