<?php 
class StateModel extends DB {

	// public function get_count_attendance($teacher_id, $date_current_client) {
	// 	$sql = " SELECT count(*) count_number_session FROM attendances WHERE teacher_id = '{$teacher_id}' ";
	// 	$sql .= " AND DATE_FORMAT(attendance_time,'%Y%m%d') = '{$date_current_client}' ";
	// 	$count = $this->get_data($sql)[0]['count_number_session'];


	// 	// lay thong tin mon hoc va lop hoc
	// 	$sql = " SELECT C.class_id, C.class_name, S.subject_id, S.subject_name, TIME_FORMAT( attendance_time, '%H' ) timeServer, A.attendance_id ";
	// 	$sql .= " FROM class C, subjects S, attendances A  ";
	// 	$sql .= " WHERE C.class_id = A.class_id AND S.subject_id = A.subject_id ";
	// 	$sql .= " AND A.teacher_id = '{$teacher_id}'  ";
	// 	$sql .= " AND DATE_FORMAT(A.attendance_time,'%Y%m%d') = '{$date_current_client}' ";
	// 	$sql .= " ORDER BY A.attendance_id DESC LIMIT 1; ";
	// 	$data = $this->get_data($sql);

	// 	$time = null;
	// 	$attendance_id_last = null;
	// 	$infoSubject = [
	// 		'subject_id' => null,
	// 		'subject_name' => null
	// 	];
	// 	$infoClass = [
	// 		'class_id' => null,
	// 		'class_name' => null
	// 	];


	// 	if(count($data) !== 0) {
	// 		$time = (int)$data[0]['timeServer'];
	// 		$attendance_id_last = (int)$data[0]['attendance_id'];
	// 		$infoSubject = [
	// 			'subject_id' => $data[0]['subject_id'],
	// 			'subject_name' => $data[0]['subject_name'],
	// 		];

	// 		$infoClass = [
	// 			'class_id' => $data[0]['class_id'],
	// 			'class_name' => $data[0]['class_name']
	// 		];
			
	// 	}

	// 	$a = [
	// 		'count_number_session' => $count,
	// 		'timeserver' => $time,
	// 		'attendance_id_last' => $attendance_id_last,
	// 		'infoClass' => $infoClass,
	// 		'infoSubject' => $infoSubject
	// 	];
	// 	return $a;
	// 	// return ['count_number_session' => $count];
	// }

	public function get_count_attendance($teacher_id, $date_current_client) {
		$sql = " SELECT count(*) count_number_session FROM attendances WHERE teacher_id = '{$teacher_id}' ";
		$sql .= " AND DATE_FORMAT(attendance_time,'%Y%m%d') = '{$date_current_client}' ";
		$count = $this->get_data($sql)[0]['count_number_session'];


		// lay thong tin mon hoc va lop hoc
		$sql = " SELECT C.class_id, C.class_name, S.subject_id, S.subject_name, TIME_FORMAT( attendance_time, '%H' ) timeServer, A.attendance_id, A.radius, DATE_FORMAT(A.attendance_time, '%Y%m%d') date_server ";
		$sql .= " FROM class C, subjects S, attendances A  ";
		$sql .= " WHERE C.class_id = A.class_id AND S.subject_id = A.subject_id ";
		$sql .= " AND A.teacher_id = '{$teacher_id}'  ";
		$sql .= " AND DATE_FORMAT(A.attendance_time,'%Y%m%d') = '{$date_current_client}' ";
		$sql .= " ORDER BY A.attendance_id DESC LIMIT 1; ";

		$data = $this->get_data($sql);

		$time = null;
		$date_server = null;
		$attendance_id_last = null;
		$infoSubject = [
			'subject_id' => null,
			'subject_name' => null
		];
		$infoClass = [
			'class_id' => null,
			'class_name' => null
		];


		if(count($data) !== 0) {
			$time = (int)$data[0]['timeServer'];
			$radius = +$data[0]['radius'];
			$date_server = $data[0]['date_server'];
			$attendance_id_last = (int)$data[0]['attendance_id'];
			$infoSubject = [
				'subject_id' => $data[0]['subject_id'],
				'subject_name' => $data[0]['subject_name'],
			];

			$infoClass = [
				'class_id' => $data[0]['class_id'],
				'class_name' => $data[0]['class_name']
			];
			
		}

		$data = [
			'count_number_session' => $count,
			'radius' => $radius ?? 5,
			'timeserver' => $time,
			'dateServer' => $date_server,
			'attendance_id_last' => $attendance_id_last,
			'infoClass' => $infoClass,
			'infoSubject' => $infoSubject
		];
		return $data;
	}






	//1 - chua tat diem danh 			0 - da tat diem danh
	public function get_check_off_attendance($attendance_id_last) {
		$sql = " SELECT ( IF ( EXISTS ( SELECT attendance_id FROM attendance_student WHERE attendance_id = {$attendance_id_last} ), 1, 0 ) ) AS state ";
		$state = (int)$this->get_data($sql)[0]['state'];
		return ['state' => $state];
	}
}