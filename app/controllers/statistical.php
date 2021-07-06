<?php 

class Statistical extends Controller {

	public function index() {
		echo "statiscal";
	}

	// public function teach_detail($teacher_id = null, $uuid = null, $current_year = null) {
	public function teach_detail($teacher_id = null, $uuid = null, $current_year = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($teacher_id, $uuid);

			$statistical = $this->model('StatisticalModel');
			// $data = $statistical->get_teach_detail($teacher_id, $current_year);
			$data = $statistical->get_teach_detail($teacher_id);
			exit( json_encode($data) );
		}
	}

	public function student_detail( $student_id = null, $uuid = null, $current_date = null ) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($student_id, $uuid);

			$statistical = $this->model('StatisticalModel');
			$data = $statistical->get_student_detail($student_id, $current_date);
			exit( json_encode($data) );
		}
	}

	public function list_student_by_statistical($username = null, $uuid = null, $class_id = null, $subject_id = null, $current_date = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($username, $uuid);

			$statistical = $this->model('StatisticalModel');
			$data = $statistical->get_list_student_statistical($class_id, $subject_id, $current_date);
			exit( json_encode($data) );
		}
	}


	// lay ra danh sach nghi cua sinh vien | chuc nang thong ke
	public function list_leave_date($student_id = null, $uuid = null, $subject_id = null, $current_date = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($student_id, $uuid);
			
			$statistical = $this->model('StatisticalModel');
			$data = $statistical->get_list_leave_date($student_id, $subject_id, $current_date);

			exit(json_encode($data));
		}
	}

	private function verify($username = null, $uuid = null) {
		$verify = $this->model('VerifyModel');
		$data = $verify->get_key_security($username);
		if(!$data) {
			exit(json_encode(['state' => -403]));
		}
		
		if($data['id'] !== $username || $data['uuid'] !== $uuid) {
			exit(json_encode(['state' => -403]));
		}
	}


}
