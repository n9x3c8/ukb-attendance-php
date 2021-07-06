<?php 
class LeaveModel extends DB {
	public function index() {}

	public function get_student_leave($class_id, $subject_id, $teacher_id, $attendance_id_last, $date_server) {
		// $sql = " INSERT INTO list_leave( subject_id, student_id, leave_date, is_enable, leave_reason )
		// SELECT A.subject_id, ATS.student_id, DATE_FORMAT(A.attendance_time,'%Y%m%d'), 0, null 
		// FROM attendance_student ATS, attendances A 
		// WHERE ATS.attendance_id = A.attendance_id 
		// AND ATS.student_attendance_state = 0 
		// AND A.attendance_id = {$attendance_id_last}
		// AND ATS.student_id NOT IN ( SELECT LL.student_id
		// FROM list_leave LL, students S
		// WHERE S.class_id = '{$class_id}'    
		// AND LL.student_id = S.student_id
		// AND LL.subject_id = '{$subject_id}'  
		// AND DATE_FORMAT(LL.leave_date,'%Y%m%d') = '{$date_server}' ) ;";

		$this->connect();
		$username = $this->_connection->real_escape_string($teacher_id);
		$class__id = $this->_connection->real_escape_string($class_id);
		$subject__id = $this->_connection->real_escape_string($subject_id);
		$attendance_id_last2 = $this->_connection->real_escape_string($attendance_id_last);
		$date__server = $this->_connection->real_escape_string($date_server);

		$sql = " INSERT INTO list_leave( subject_id, student_id, leave_date, is_enable, leave_reason )
		SELECT A.subject_id, ATS.student_id, DATE_FORMAT(A.attendance_time,'%Y%m%d'), 0, null 
		FROM attendance_student ATS, attendances A 
		WHERE ATS.attendance_id = A.attendance_id 
		AND ATS.student_attendance_state = 0 
		AND A.attendance_id = {$attendance_id_last2}
		AND ATS.student_id NOT IN ( SELECT LL.student_id
		FROM list_leave LL, students S
		WHERE LL.student_id = S.student_id
		AND LL.subject_id = '{$subject__id}'  
		AND DATE_FORMAT(LL.leave_date,'%Y%m%d') = '{$date__server}' ) ;";

		$result =  $this->command_by_sql($sql);
		if($result) {
			$is_success = $this->delete('attendance_student', "attendance_id = {$attendance_id_last2}");
			return $is_success ? ['state' => 1] : ['state' => -1];
		}
		return ['state' => -1];
	}



	//Them du lieu sinh vien xin nghi vao bang leaves
	public function insert_student_in_leaves($student_id, $subject_id, $leave_time, $leave_reason, $take_leave_date) {
		$this->connect();
		$username = $this->_connection->real_escape_string($student_id);
		$subject__id = $this->_connection->real_escape_string($subject_id);
		$leave__time = $this->_connection->real_escape_string($leave_time);
		$leave__reason = $this->_connection->real_escape_string($leave_reason);
		$take_leave_date2 = $this->_connection->real_escape_string($take_leave_date);


		$sql = "INSERT INTO leaves(student_id, subject_id, leave_time, leave_reason, take_leave_date) VALUES " ;
		$sql .= "('{$username}', '{$subject__id}', '{$leave__time}', '{$leave__reason}', '{$take_leave_date2}');";
		return $this->command_by_sql($sql) ? true : false;
	}

	public function get_notification_take_leave ($teacher_id, $date_current) {
		$sql = "SELECT T1.leave_id AS leave_id_leaves, T1.subject_id, T1.class_id, T1.student_name, T1.take_leave_date, T1.leave_time, T1.leave_reason, T1.student_id, T1.student_avatar,  T1.student_gender,  T2.subject_name
		FROM
		(SELECT L.leave_id, L.subject_id, ST.class_id, ST.student_name, L.take_leave_date, L.leave_time, L.leave_reason, ST.student_id, ST.student_avatar,  ST.student_gender
		FROM leaves L, students ST
		WHERE L.take_leave_date >= '{$date_current}'
		AND NOT EXISTS (
		SELECT LL.leave_id
		FROM list_leave LL
		WHERE LL.leave_id = L.leave_id
		)
		AND L.student_id = ST.student_id) T1
		INNER JOIN (
		SELECT TD.class_id, TD.subject_id, SB.subject_name
		FROM subjects SB, teach_details TD
		WHERE TD.subject_id = SB.subject_id
		AND TD.teacher_id = '{$teacher_id}'
		) T2
		ON T1.subject_id = T2.subject_id 
		AND T1.class_id = T2.class_id; ";

		$data = $this->get_data($sql);
		$count = count($data);
		if($count !== 0) {
			return $data;
		}
		return [];
	}

	public function get_count_notification_take_leave ($teacher_id, $date_current) {
		//lấy ra những môn học mà giảng viên đnag giảng dạy
		$sql = "SELECT count(T1.leave_id) count
		FROM
		(SELECT L.leave_id, L.subject_id, ST.class_id, ST.student_name, L.take_leave_date, L.leave_time, L.leave_reason, ST.student_id, ST.student_avatar,  ST.student_gender
		FROM leaves L, students ST
		WHERE L.take_leave_date >= '{$date_current}'
		AND NOT EXISTS (
		SELECT LL.leave_id
		FROM list_leave LL
		WHERE LL.leave_id = L.leave_id
		)
		AND L.student_id = ST.student_id) T1
		INNER JOIN (
		SELECT TD.class_id, TD.subject_id, SB.subject_name
		FROM subjects SB, teach_details TD
		WHERE TD.subject_id = SB.subject_id
		AND TD.teacher_id = '{$teacher_id}'
		) T2
		ON T1.subject_id = T2.subject_id 
		AND T1.class_id = T2.class_id;";

		$data = $this->get_data($sql);
		$count = count($data);
		if($count !== 0) {
			return $data[0];
		}
		return [];
	}




	//them SV được GV phê duyệt nghỉ vào list_leave
	public function add_student_teacher_agree($student_id, $subject_id, $take_leave_date, $leave_reason, $leave_id_leaves) {
		$this->connect();
		$username = $this->_connection->real_escape_string($student_id);
		$subject__id = $this->_connection->real_escape_string($subject_id);
		$take__leave_date = $this->_connection->real_escape_string($take_leave_date);
		$leave__reason = $this->_connection->real_escape_string($leave_reason);
		$leave_id_leaves2 = $this->_connection->real_escape_string($leave_id_leaves);

		$sql = " INSERT INTO list_leave ( student_id, subject_id, leave_date, is_enable, leave_reason, leave_id, denine_reason) VALUES ( '{$username}', '{$subject__id}', '{$take__leave_date}', 1, '{$leave__reason}', {$leave_id_leaves2}, null ) ; ";
		return $this->command_by_sql($sql);
	}

	// them sv bi GV tu choi vao list leave
	public function add_student_teacher_denine($student_id, $subject_id, $take_leave_date, $leave_reason, $leave_id_leaves, $denine_reason) {
		$this->connect();
		$username = $this->_connection->real_escape_string($student_id);
		$subject__id = $this->_connection->real_escape_string($subject_id);
		$take__leave_date = $this->_connection->real_escape_string($take_leave_date);
		$leave__reason = $this->_connection->real_escape_string($leave_reason);
		$denine__reason = $this->_connection->real_escape_string($denine_reason);
		$leave_id_leaves2 = $this->_connection->real_escape_string($leave_id_leaves);

		$sql = " INSERT INTO list_leave (student_id, subject_id, leave_date, is_enable, leave_reason, leave_id, denine_reason) VALUES ( '{$username}', '{$subject__id}', '{$take__leave_date}', 0, '{$leave__reason}', {$leave_id_leaves2}, '{$denine__reason}' ); ";
		return $this->_connection->query($sql);
	}


	public function get_check_take_leave ($student_id, $subject_id, $take_leave_date) {
		$sql = " SELECT ( IF ( EXISTS ( SELECT leave_id FROM leaves WHERE student_id = '{$student_id}' AND subject_id = '{$subject_id}' AND take_leave_date = '{$take_leave_date}' ), 1, 0 ) ) AS state; ";
		return $this->get_data($sql);
	}

	public function get_count_take_leave( $student_id, $subject_id ) {
		$sql = " SELECT COUNT(*) quantity_take_leave_subject FROM leaves WHERE student_id = '{$student_id}'  ";
		$sql .= " AND subject_id = '{$subject_id}' GROUP  BY student_id, subject_id; ";
		return $this->get_data($sql);

	}

	public function delete_rm_without_leave($student_id, $subject_id, $current_date) {
		$sql = " DELETE FROM list_leave WHERE leave_date = '{$current_date}' AND student_id = '{$student_id}' AND subject_id = '{$subject_id}'; ";

		$query = $this->command_by_sql($sql);

		if($query) {
			return ['state' => 1];
		}
		return ['state' => -1];

	}



	private function command_by_sql($sql)
	{
		$this->connect();
		return $this->_connection->query($sql);
	}
}
