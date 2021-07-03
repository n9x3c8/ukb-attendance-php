<?php 

class State extends Controller {
	public function index() {}

	public function count_attendance ($teacher_id = null, $date_current_client = null) {
		if( $teacher_id !== null && $date_current_client !== null ) {
			$state = $this->model('StateModel');
			$data = $state->get_count_attendance($teacher_id, $date_current_client);
			exit(json_encode($data));
		}
	}

	public function check_off_attendance ($attendance_id_last = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$stateModel = $this->model('StateModel');
			$state = $stateModel->get_check_off_attendance($attendance_id_last);
			exit(json_encode($state));
		}

	}

}