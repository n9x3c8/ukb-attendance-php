<?php 

class StudentModel extends DB {

	// public function get_list_student($class_id, $subject_id, $current_date) {
	// 	$sql = " SELECT L1.student_id, L1.student_name, L1.state, L2.leave_session FROM (SELECT DISTINCT S.student_id student_id, S.student_name,
	// 	CASE WHEN LL.is_enable = 0 OR LL.is_enable = 1 THEN 0 ELSE 1 END state
	// 	FROM students S
	// 	LEFT JOIN  ( SELECT LL.student_id student_id, LL.is_enable is_enable FROM list_leave LL WHERE LL.subject_id = '{$subject_id}' AND leave_date = '{$current_date}' ) LL
	// 	ON LL.student_id = S.student_id
	// 	WHERE S.class_id = '{$class_id}'  ) AS L1
	// 	LEFT JOIN ( SELECT LL.student_id, COUNT(LL.list_leave_id) leave_session
	// 	FROM list_leave LL
	// 	WHERE LL.subject_id = '{$subject_id}'
	// 	GROUP BY student_id ) L2
	// 	ON L1.student_id = L2.student_id;
	// 	";

	// 	$data = $this->get_data($sql);
	// 	if(count($data) !== 0) {
	// 		return $data;
	// 	}
	// 	return [];
	// }

	public function get_list_student($class_id, $subject_id, $current_date) {
		$length = $_GET['length'] ?? 10;
		$current_page = $_GET['p'] ?? 1;
		$sql = "SELECT COUNT(student_id) AS count FROM students
		WHERE class_id = '{$class_id}';";
		$data = $this->get_data($sql);
		$count = +$data[0]['count'];
		$total_page = ceil($count / $length);
		$offset = ($current_page - 1) * $length;

		$sql = " SELECT S.student_id student_id, S.student_name,
		CASE WHEN LL.is_enable = 0 OR LL.is_enable = 1 THEN 0 ELSE 1 END state
		FROM students S
		LEFT JOIN  ( SELECT LL.student_id student_id, LL.is_enable is_enable FROM list_leave LL 
		WHERE LL.subject_id = '{$subject_id}' AND leave_date = '{$current_date}' ) LL
		ON LL.student_id = S.student_id
		WHERE S.class_id = '{$class_id}' LIMIT {$length} OFFSET {$offset}; ";

		$data = $this->get_data($sql);

		if(count($data) !== 0) {
			return [
				'total_page' => $total_page,
				'data' => $data
			];

		}
		return [];
	}


	

	// public function get_list_student_leave_permission_agree($class_id, $subject_id, $current_date) {
	// 	$sql = " SELECT S.student_id, S.student_name, LL.leave_reason, LL.take_leave_date
	// 	FROM ( SELECT student_id, student_name FROM students WHERE class_id = '{$class_id}' ) S
	// 	INNER JOIN (SELECT L1.student_id, L1.leave_reason, L1.denine_reason, DATE_FORMAT(L2.leave_time, '%d-%m-%Y') take_leave_date FROM list_leave L1, leaves L2 WHERE L1.subject_id = '{$subject_id}' AND L1.is_enable = 1 AND L1.leave_date = '{$current_date}' AND L1.leave_reason IS NOT NULL AND L1.leave_id = L2.leave_id   ) LL
	// 	ON LL.student_id  = S.student_id;  ";

	// 	$data = $this->get_data($sql);

	// 	if(count($data) !== 0) {
	// 		return $data;
	// 	}
	// 	return [];
	// }

	public function get_list_student_leave_permission_agree($class_id, $subject_id, $current_date) {
		$sql = "SELECT L1.student_id, L1.leave_reason, DATE_FORMAT(L2.leave_time, '%d-%m-%Y') take_leave_date, S.student_name 
		FROM list_leave L1, leaves L2 , students S 
		WHERE L1.leave_date = '{$current_date}' AND L1.subject_id = '{$subject_id}' AND L1.is_enable = 1 
		AND  L1.leave_reason IS NOT NULL AND L1.leave_id = L2.leave_id
		AND L1.student_id = S.student_id
		AND S.class_id = '{$class_id}' ; ";

		$data = $this->get_data($sql);

		if(count($data) !== 0) {
			return $data;
		}
		return [];
	}


	// public function get_list_student_leave_permission_denine($class_id, $subject_id, $current_date) {
	// 	$sql = " SELECT S.student_id, S.student_name, LL.leave_reason, LL.denine_reason,  LL.take_leave_date
	// 	FROM ( SELECT student_id, student_name FROM students WHERE class_id = '{$class_id}' ) S
	// 	INNER JOIN ( SELECT L1.student_id, L1.leave_reason, L1.denine_reason, DATE_FORMAT(L2.leave_time, '%d-%m-%Y') take_leave_date FROM list_leave L1, leaves L2 WHERE L1.subject_id = '{$subject_id}' AND L1.is_enable = 0 AND L1.leave_date = '{$current_date}' AND L1.leave_reason IS NOT NULL AND L1.leave_id = L2.leave_id) LL
	// 	ON LL.student_id  = S.student_id; ";

	// 	$data = $this->get_data($sql);

	// 	if(count($data) !== 0) {
	// 		return $data;
	// 	}
	// 	return [];
	// }

	public function get_list_student_leave_permission_denine($class_id, $subject_id, $current_date) {
		$sql = "SELECT L1.student_id, L1.leave_reason, DATE_FORMAT(L2.leave_time, '%d-%m-%Y') take_leave_date, S.student_name, L1.denine_reason 
		FROM list_leave L1, leaves L2 , students S 
		WHERE L1.leave_date = '{$current_date}' AND L1.subject_id = '{$subject_id}' AND L1.is_enable = 0 
		AND  L1.leave_reason IS NOT NULL AND L1.leave_id = L2.leave_id
		AND L1.student_id = S.student_id
		AND S.class_id = '{$class_id}' ; ";

		$data = $this->get_data($sql);

		if(count($data) !== 0) {
			return $data;
		}
		return [];
	}


	public function get_list_student_without_permission($class_id, $subject_id, $current_date) {
		$sql = " SELECT S.student_id, S.student_name, LL.leave_reason, LL.denine_reason
		FROM ( SELECT student_id, student_name FROM students WHERE class_id = '{$class_id}' ) S
		INNER JOIN ( SELECT student_id, leave_reason, denine_reason FROM list_leave WHERE subject_id = '{$subject_id}' AND is_enable = 0 AND leave_date = '{$current_date}'  AND denine_reason IS NULL) LL
		ON LL.student_id  = S.student_id; ";
		$data = $this->get_data($sql);
		if(count($data) !== 0) {
			return $data;
		}
		return [];
	}


	public function get_leave_session($student_id, $subject_id) {
		$sql = "SELECT COUNT(list_leave_id) leave_session
		FROM list_leave
		WHERE student_id = '{$student_id}' AND subject_id = '{$subject_id}'; ";
		$data = $this->get_data($sql);
		if(count($data) !== 0) {
			var_dump($data); die();
			return ['leave_session' => $data[0]['leave_session']];
		}
		return [];
	}


	public function get_leave_application($student_id, $current_date) {
		$sql = " SELECT L1.leave_id leave_id_leaves, L1.student_name, L1.subject_name, L1.leave_time, L1.take_leave_date, IF( L1.take_leave_date > '{$current_date}', 1, 0) isDelete  ,L1.leave_reason, L2.list_leave_id, L2.leave_id leave_id_list_leave, L2.is_enable, L2.denine_reason
		FROM
		(SELECT L.leave_id,L.student_id, ST.student_name, SB.subject_name, L.leave_time, L.take_leave_date, L.leave_reason
		FROM leaves L, students ST, subjects SB
		WHERE L.student_id = '{$student_id}'
		AND L.student_id = ST.student_id
		AND L.subject_id = SB.subject_id ) L1

		LEFT JOIN (
		SELECT LL.list_leave_id, LL.leave_id, LL.is_enable, LL.denine_reason
		FROM list_leave LL
		WHERE LL.student_id = '{$student_id}'
		) L2
		ON L1.leave_id = L2.leave_id
		ORDER BY leave_id_leaves DESC;
		";
		$data = $this->get_data($sql);
		if(count($data) !== 0) {
			return $data;
		}
		return [];

	}


	public function update_take_leave ($leave_id, $leave_time, $leave_reason, $take_leave_date) {
		$this->connect();
		$leave__id = $this->_connection->real_escape_string($leave_id);
		$leave__time = $this->_connection->real_escape_string($leave_time);
		$leave__reason = $this->_connection->real_escape_string($leave_reason);
		$take_leave_date2 = $this->_connection->real_escape_string($take_leave_date);


		$sql = " UPDATE leaves SET leave_time = '{$leave__time}', leave_reason = '{$leave__reason}', take_leave_date = '{$take_leave_date2}' WHERE leave_id = {$leave__id}; ";

		$result = $this->_connection->query($sql);
		// if($result) return ['state' => 1];
		// return ['state' => -1];
		return $result ? ['state' => 1] : ['state' => -1];
	}


	public function delete_take_leave($leave_id) {
		$sql = " DELETE FROM list_leave WHERE leave_id = {$leave_id}; ";
		$query = $this->cmd_by_sql($sql);

		if($query) {
			$sql = " DELETE FROM leaves WHERE leave_id = {$leave_id}; ";
			$query2 = $this->cmd_by_sql($sql);
			if($query2) {
				return ['state' => 1];
			}
			return ['state' => -1];
		}
		return ['state' => -1];
	}


	private function cmd_by_sql($sql) {
		$this->connect();
		return $this->_connection->query($sql);
	}
}