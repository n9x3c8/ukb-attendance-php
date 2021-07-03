<?php 

class State extends Controller {
	public function index() {}

	public function count_attendance ($teacher_id = null, $uuid = null, $date_current_client = null) {
		if( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
			$this->verify($teacher_id, $uuid);

			$state = $this->model('StateModel');
			$data = $state->get_count_attendance($teacher_id, $date_current_client);
			exit(json_encode($data));
		}
	}

	public function check_off_attendance ($username = null, $uuid = null, $attendance_id_last = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($username, $uuid);
			
			$stateModel = $this->model('StateModel');
			$state = $stateModel->get_check_off_attendance($attendance_id_last);
			exit(json_encode($state));
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