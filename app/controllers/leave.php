<?php 

class Leave extends Controller {
	public function index() {
		echo "string";
	}

	public function student_leave( $class_id = null , $subject_id = null, $teacher_id = null, $uuid = null, $attendance_id_last = null, $date_server = null ) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($teacher_id, $uuid);

			$leave = $this->model('LeaveModel');
			$data = $leave->get_student_leave($class_id, $subject_id, $teacher_id, $attendance_id_last, $date_server);
			exit( json_encode($data) );
		}
		exit( json_encode(['state' => -1]) );
	}


	//Them du lieu sinh vien xin nghi vao bang leaves
	//leave_time: ngay hien tai
	// take_leave_date: ngay sinh vien muon nghi
	public function add_student_in_leaves($username, $uuid) {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->verify($username, $uuid);

			$data = json_decode(file_get_contents('php://input'), true);
			$student_id = $data['student_id'];
			$subject_id = $data['subject_id'];
			$leave_time = $data['leave_time'];
			$leave_reason = $data['leave_reason'];
			$take_leave_date = $data['take_leave_date'];

			$leave = $this->model('LeaveModel');
			$is_success = $leave->insert_student_in_leaves($student_id, $subject_id, $leave_time, $leave_reason, $take_leave_date);
			if($is_success) {
				echo json_encode(['state' => 1]);
				exit();
			}
		}
		echo json_encode(['state' => -1]);
		exit();
	}

	// ktra cos take_leave_date trung khong

	public function check_take_leave($username, $uuid) {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->verify($username, $uuid);

			$data = json_decode(file_get_contents('php://input'), true);
			$student_id = $data['student_id'];
			$subject_id = $data['subject_id'];
			$take_leave_date = $data['take_leave_date'];
			$leave = $this->model('LeaveModel');
			$data = $leave->get_check_take_leave($student_id, $subject_id, $take_leave_date);
			http_response_code(200);
			exit( json_encode($data) );
		}
	}

	// kiem tra so buoi nghi cua sinh vien
	public function count_take_leave($username, $uuid) {
		if($_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$this->verify($username, $uuid);

			$data = json_decode(file_get_contents('php://input'), true);
			$student_id = $data['student_id'];
			$subject_id = $data['subject_id'];
			$leave = $this->model('LeaveModel');
			$data = $leave->get_count_take_leave($student_id, $subject_id);
			if(count($data) !== 0) {
				exit( json_encode($data) );
			}
			exit( json_encode(['state' => -1]) );
		}
	}


	//hien thi thong bao sv xin nghi cho GV
	public function notification_take_leave( $username = null, $uuid = null, $date_current = null ) {			
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($username, $uuid);

			if ( $username !== null && $date_current !== null ) {
				http_response_code(200);
				$leave = $this->model('LeaveModel');
				$data = $leave->get_notification_take_leave($username, $date_current);
				exit(json_encode($data));
			} else {
				http_response_code(400);
				exit(json_encode(['state' => -1]));
			}
		}
	}

	// dem so thong bao sv xin nghi
	public function count_notification_take_leave ( $username = null, $uuid = null, $date_current = null ) {			
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($username, $uuid);

			if ( $username !== null && $date_current !== null ) {
				http_response_code(200);
				$leave = $this->model('LeaveModel');
				$data = $leave->get_count_notification_take_leave($username, $date_current);
				exit(json_encode($data));
			} else {
				http_response_code(400);
				exit(json_encode(['state' => -1]));
			}
		}
	}


	//GV đồng ý cho SV nghi
	public function teacher_agree($username, $uuid) {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->verify($username, $uuid);
			
			$data = json_decode(file_get_contents('php://input'), true);
			$LeaveModel = $this->model('LeaveModel');
			$student_id = $data['student_id'];
			$subject_id = $data['subject_id'];
			$leave_id_leaves = $data['leave_id_leaves'];
			$leave_reason = $data['leave_reason'];
			$take_leave_date = $data['take_leave_date'];

			$data = $LeaveModel->add_student_teacher_agree($student_id, $subject_id, $take_leave_date, $leave_reason, $leave_id_leaves);
			echo  $data ? json_encode(['state' => 1]) : json_encode(['state' => -1]);
			die();
		}
	}

	//GV tu choi cho SV nghi
	public function teacher_denine($username, $uuid) {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->verify($username, $uuid);

			$data = json_decode(file_get_contents('php://input'), true);
			$LeaveModel = $this->model('LeaveModel');
			$student_id = $data['student_id'];
			$subject_id = $data['subject_id'];
			$leave_id_leaves = $data['leave_id_leaves'];
			$leave_reason = $data['leave_reason'];
			$take_leave_date = $data['take_leave_date'];
			$take_leave_date = $data['take_leave_date'];
			$denine_reason = $data['denine_reason'];
			$data = $LeaveModel->add_student_teacher_denine($student_id, $subject_id, $take_leave_date, $leave_reason, $leave_id_leaves, $denine_reason);
			echo  $data ? json_encode(['state' => 1]) : json_encode(['state' => -1]);
			die();
		}
	}

	//GV cap nhap trong chi tiet buoi hoc (SV nghi khong phep)
	public function rm_without_leave($student_id = null, $uuid = null, $subject_id = null, $current_date = null, $username) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($username, $uuid);
			
			$leave = $this->model('LeaveModel');
			$data = $leave->delete_rm_without_leave($student_id, $subject_id, $current_date);
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
