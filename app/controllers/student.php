<?php 

class Student extends Controller {

	//lay ds sv đi học buổi hôm đó (trạng thai)
	public function list_student($class_id = null, $subject_id = null, $current_date = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$student = $this->model('StudentModel');
			$data = $student->get_list_student($class_id, $subject_id, $current_date);
			exit( json_encode($data) );
		}
	}

	//Gv đồng ý cho nghỉ
	public function list_student_leave_permission_agree( $class_id = null, $subject_id = null, $current_date = null ) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$student = $this->model('StudentModel');
			$data = $student->get_list_student_leave_permission_agree($class_id, $subject_id, $current_date);
			echo json_encode($data);
			exit();
		}
	}

	//Gv từ chối cho nghỉ
	public function list_student_leave_permission_denine( $class_id = null, $subject_id = null, $current_date = null ) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$student = $this->model('StudentModel');
			$data = $student->get_list_student_leave_permission_denine($class_id, $subject_id, $current_date);
			echo json_encode($data);
			exit();

		}
	}

	//nghỉ học không phép
	public function list_student_without_permission( $class_id = null, $subject_id = null, $current_date = null ) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$student = $this->model('StudentModel');
			$data = $student->get_list_student_without_permission($class_id, $subject_id, $current_date);
			echo json_encode($data);
			exit();
		}
	}


	// lay ra so buoi nghi cua sinh viee

	public function leave_session($student_id = null, $subject_id = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$student = $this->model('StudentModel');
			$d = $student->get_leave_session($subject_id, $subject_id);
			exit(json_encode($d));
		}
	}

	//ds đơn xin nghỉ của SV
	public function leave_application($student_id = null, $current_date = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$student = $this->model('StudentModel');
			$data = $student->get_leave_application($student_id, $current_date);
			exit( json_encode($data) );
		}

	}


	public function edit_take_leave() {
		if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
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

	public function remove_take_leave($leave_id = null) {
		if($_SERVER['REQUEST_METHOD']  == 'GET') {
			$student = $this->model('StudentModel');
			$data = $student->delete_take_leave($leave_id);
			exit( json_encode($data) );
		}
	}


}