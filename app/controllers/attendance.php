<?php 
class Attendance extends Controller {


	public function add_attendance($username = null, $uuid = null) {
		$this->verify($username, $uuid);

		$data = json_decode(file_get_contents('php://input'), true);
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$AttendanceModelObj = $this->model('AttendanceModel');
			$class_id = $data['class_id'];
			$subject_id = $data['subject_id'];
			$teacher_id = $data['teacher_id'];
			$attendance_time = $data['attendance_time'];
			$latitude = $data['latitude'];
			$longitude = $data['longitude'];
			$radius = $data['radius'];
			
			//1 thanh cong, -1 fail, -2 else
			echo $AttendanceModelObj->add_attendance_time($class_id, $subject_id, $teacher_id, $attendance_time, $latitude, $longitude, $radius) ? json_encode(['state' => 1]) : json_encode(['state' => -1]);
			exit();
		}
		exit( json_encode(['state' => -2]) );
	}


	// kiem tra sinh vien co lop hoc hay khong
	public function check_exit_in_room($student_id = null, $uuid = null) {
		$this->verify($student_id, $uuid);
		$attendance = $this->model('AttendanceModel');

		$state = $attendance->get_check_exist_in_room($student_id);
		echo json_encode($state); // 1 or 0
	}

	// kiem tra xem  giang vien da bat diem danh hay chua
	// public function state_turn_on_attendance($class_id = null, $subject_id = null, $teacher_id = null, $day = null) {
	// 	if($class_id !== null && $subject_id !== null && $teacher_id !== null && $day !== null) {
	// 		$attendance = $this->model('AttendanceModel');
	// 		$data = $attendance->get_state_turn_on_attendance($class_id, $subject_id, $teacher_id, $day);
	// 		exit(json_encode($data));
	// 	}
	// 	exit(json_encode(['state' => -1]));
	// }


	// lay thong tin chi tiet diem danh
	public function info_details_attendance_student($student_id = null, $current_date = null, $uuid = null) {
		$this->verify($student_id, $uuid);

		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$attendance = $this->model('AttendanceModel');
			$data = $attendance->get_info_details_attendance($student_id,  $current_date);
			echo json_encode($data);
			exit();
		}
		exit( json_encode(['state' => -1]) );
	}

	//lay ra datetime server
	// public function datetime_attendance_last($class_id = null, $subject_id = null, $teacher_id = null, $day = null) {

	// 	if( $class_id !== null && $subject_id !== null && $teacher_id !== null && $day != null) {
	// 		$attendance = $this->model('AttendanceModel');
	// 		$data = $attendance->get_datetime_attendance_last($class_id, $subject_id, $teacher_id, $day);
	// 		exit( json_encode($data) );
	// 	}
	// 	exit( json_encode(['state' => -1]) );
	// }


	// kiem tra id sv da diem trong attendance student 
	public function exist_in_attendance_student($student_id = null, $uuid = null) {
		$this->verify($student_id, $uuid);

		$attendance = $this->model('AttendanceModel');
		$is_exist = $attendance->get_exist_in_attendance_student($student_id);

		if(count($is_exist) !== 0) {
			exit(json_encode($is_exist[0]));
		}
		exit( json_encode(['state' => -1]) );
	}


	// sinh vien diem danh - cap nhat state bang attendance_student = 1
	public function state_attendance_student($student_id = null, $uuid = null) {
		$this->verify($student_id, $uuid);

		if( $student_id !== null) {
			$attendance = $this->model('AttendanceModel');
			$is_success = $attendance->update_state_attendance_student($student_id);
			if($is_success) {
				exit( json_encode(['state' => 1]) );
			}
			exit( json_encode(['state' => -1]) );
		}
	}


	// kiem tra giang vien da duyet sv nghi hoc hay chua. Neu la NULL -> ton tai sv chua duoc phe duyet

	public function check_exist_in_take_leave($username = null, $uuid = null, $class_id = null, $subject_id = null, $current_date = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($username, $uuid);
			$attendance = $this->model('AttendanceModel');
			$data = $attendance->get_check_exist_in_take_leave($class_id, $subject_id, $current_date);
			exit(json_encode($data));
		}
	}


	// diem danh thu cong
	public function handmade_at() {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$data = json_decode(file_get_contents('php://input'), true);
			$attendance = $this->model('AttendanceModel');
			$student_id = $data['student_id'];
			$d = $attendance->post_handmade_at($student_id);
			exit( json_encode($d) );
		}
	}

	//kiem tra xem co sv nao diem danh thu cong k
	public function exist_student_handmade($attendance_id_last = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$attendance = $this->model('AttendanceModel');
			$state = $attendance->is_exist_student_handmade($attendance_id_last);
			exit( json_encode($state) );
		}
	}

	//lay ds sinh vien diem danh thu cong
	public function list_student_handmade( $attendance_id_last = null ) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$attendance = $this->model('AttendanceModel');
			$state = $attendance->get_list_student_handmade($attendance_id_last);
			exit( json_encode($state) );
		}
	}

	public function state_when_attendance_handmade($student_id = null, $state = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET' ) {
			$attendance = $this->model('AttendanceModel');
			$data = $attendance->update_state_when_attendance_handmade($student_id, $state);
			exit( json_encode($data) );
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
