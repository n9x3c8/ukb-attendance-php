<?php 
class LeaveModel extends DB {
	public function index() {}

	public function get_student_leave($class_id, $subject_id, $teacher_id, $attendance_id_last, $date_server) {
		$sql = " INSERT INTO list_leave( subject_id, student_id, leave_date, is_enable, leave_reason )
		SELECT A.subject_id, ATS.student_id, DATE_FORMAT(A.attendance_time,'%Y%m%d'), 0, null 
		FROM attendance_student ATS, attendances A 
		WHERE ATS.attendance_id = A.attendance_id 
		AND ATS.student_attendance_state = 0 
		AND A.attendance_id = {$attendance_id_last}
		AND ATS.student_id NOT IN ( SELECT LL.student_id
		FROM list_leave LL, students S
		WHERE S.class_id = '{$class_id}'    
		AND LL.student_id = S.student_id
		AND LL.subject_id = '{$subject_id}'  
		AND DATE_FORMAT(LL.leave_date,'%Y%m%d') = '{$date_server}' ) ;";

		$result =  $this->command_by_sql($sql);
		if($result) {
			$is_success = $this->delete('attendance_student', "attendance_id = {$attendance_id_last}");
			return $is_success ? ['state' => 1] : ['state' => -1];
		}
		return ['state' => -1];
	}



	//Them du lieu sinh vien xin nghi vao bang leaves
	public function insert_student_in_leaves($student_id, $subject_id, $leave_time, $leave_reason, $take_leave_date) {
		$sql = "INSERT INTO leaves(student_id, subject_id, leave_time, leave_reason, take_leave_date) VALUES " ;
		$sql .= "('{$student_id}', '{$subject_id}', '{$leave_time}', '{$leave_reason}', '{$take_leave_date}');";
		return $this->command_by_sql($sql) ? true : false;
	}

	public function get_notification_take_leave ($teacher_id, $date_current) {
		//lấy ra những môn học mà giảng viên đnag giảng dạy
		$sql = "
		SELECT L.leave_id, L.student_id, L.subject_id, L.take_leave_date, L.subject_id, L.leave_time, L.leave_reason, ST.student_name, ST.student_avatar, ST.student_gender, C.class_id, SB.subject_name, L.leave_id leave_id_leaves
		FROM leaves L , class C, subjects SB, students ST
		WHERE L.subject_id IN (SELECT subject_id FROM teach_details WHERE teacher_id = '{$teacher_id}')
		AND L.take_leave_date >= '{$date_current}'
		AND NOT EXISTS  (
		SELECT LL.list_leave_id
		FROM list_leave LL
		WHERE L.leave_id = LL.leave_id
		)
		AND L.student_id = ST.student_id
		AND L.subject_id = SB.subject_id
		AND ST.class_id = C.class_id;";

		$data = $this->get_data($sql);
		$count = count($data);
		if($count !== 0) {

			return $data;
		}
		return [];
	}

	public function get_count_notification_take_leave ($teacher_id, $date_current) {
		//lấy ra những môn học mà giảng viên đnag giảng dạy
		$sql = "
		SELECT count(L.leave_id) count
		FROM leaves L , class C, subjects SB, students ST
		WHERE L.subject_id IN (SELECT subject_id FROM teach_details WHERE teacher_id = '{$teacher_id}')
		AND L.take_leave_date >= '{$date_current}'
		AND NOT EXISTS  (
		SELECT LL.list_leave_id
		FROM list_leave LL
		WHERE L.leave_id = LL.leave_id
		)
		AND L.student_id = ST.student_id
		AND L.subject_id = SB.subject_id
		AND ST.class_id = C.class_id;";

		$data = $this->get_data($sql);
		$count = count($data);
		if($count !== 0) {
			return $data[0];
		}
		return [];
	}




	//them SV được GV phê duyệt nghỉ vào list_leave
	public function add_student_teacher_agree($student_id, $subject_id, $take_leave_date, $leave_reason, $leave_id_leaves) {
		$sql = " INSERT INTO list_leave ( student_id, subject_id, leave_date, is_enable, leave_reason, leave_id, denine_reason) VALUES ( '{$student_id}', '{$subject_id}', '{$take_leave_date}', 1, '{$leave_reason}', {$leave_id_leaves}, null ) ; ";
		return $this->command_by_sql($sql);
	}

	// them sv bi GV tu choi vao list leave
	public function add_student_teacher_denine($student_id, $subject_id, $take_leave_date, $leave_reason, $leave_id_leaves, $denine_reason) {
		$sql = " INSERT INTO list_leave (student_id, subject_id, leave_date, is_enable, leave_reason, leave_id, denine_reason) VALUES ( '{$student_id}', '{$subject_id}', '{$take_leave_date}', 0, '{$leave_reason}', {$leave_id_leaves}, '{$denine_reason}' ); ";
		return $this->command_by_sql($sql);
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
