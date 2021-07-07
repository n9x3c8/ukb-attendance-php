<?php 

class StatisticalModel extends DB {

	// public function get_teach_detail($teacher_id, $current_year) {
	public function get_teach_detail($teacher_id) {
		$sql = " SELECT B1.teacher_id, B1.class_id, B1.class_name, B1.subject_id, B1.subject_name, B1.subject_credit, B2.learn_session, B2.month, B2.year
		FROM
		(SELECT TD.teacher_id, C.class_id, C.class_name, SB.subject_id, SB.subject_name, SB.subject_credit
		FROM teach_details TD, class C, subjects SB
		WHERE TD.teacher_id = '{$teacher_id}'
		AND TD.class_id = C.class_id
		AND TD.subject_id = SB.subject_id) B1
		LEFT JOIN
		( SELECT COUNT(AT.attendance_id) learn_session, AT.subject_id, MONTH(AT.attendance_time) month, YEAR(AT.attendance_time) year
		FROM attendances AT
		WHERE AT.teacher_id = '{$teacher_id}'
		GROUP BY AT.subject_id
		) B2
		ON B1.subject_id = B2.subject_id
		ORDER BY B2.learn_session ASC ";

		$data = $this->get_data($sql);
		if(count($data) !== 0) {
			return $data;
		}
		return ['state' => -1];
	}


	public function get_student_detail( $student_id, $current_date ) {
		$sql = " SELECT B1.subject_id, B1.subject_name, B1.teacher_name, B2.learn_session, B3.leave_session, B1.subject_credit
		FROM (SELECT TD.subject_id, SB.subject_credit, SB.subject_name, T.teacher_name
		FROM teach_details TD, students S, subjects SB, teachers T
		WHERE S.student_id = '{$student_id}'
		AND S.class_id = TD.class_id
		AND TD.subject_id = SB.subject_id
		AND TD.teacher_id = T.teacher_id) B1
		LEFT JOIN
		( SELECT A.subject_id, COUNT(*) learn_session
		FROM attendances A, students S
		WHERE S.student_id = '{$student_id}'
		AND A.class_id = S.class_id
		GROUP BY A.subject_id ) B2
		ON B1.subject_id = B2.subject_id 
		LEFT JOIN
		( SELECT LL.subject_id, COUNT(*) leave_session
		FROM list_leave LL
		WHERE LL.student_id = '{$student_id}'
		AND LL.leave_date <= '{$current_date}'
		GROUP BY LL.subject_id ) B3
		ON B1.subject_id = B3.subject_id  ";

		$data = $this->get_data($sql);
		if(count($data) !== 0) {
			return $data;
		}
		return [];

	}

	// public function get_list_student_statistical($class_id, $subject_id, $current_date) {
	// 	$sql = "SELECT T1.student_id, T1.student_name, T1.student_birthday, T2.count
	// 	FROM (
	// 	SELECT S.student_id , S.student_name, S.student_birthday 
	// 	FROM students S 
	// 	WHERE S.class_id = '{$class_id}') T1
	// 	LEFT JOIN(
	// 	SELECT LL.student_id, COUNT(LL.list_leave_id) count
	// 	FROM list_leave LL, students S 
	// 	WHERE LL.subject_id = '{$subject_id}'
	// 	AND LL.leave_date <= '{$current_date}'
	// 	AND LL.student_id = S.student_id
	// 	AND S.class_id = '{$class_id}'
	// 	GROUP BY LL.student_id, LL.subject_id) T2
	// 	ON T1.student_id = T2.student_id; ";


	// 	$data = $this->get_data($sql);
	// 	if(count($data) !== 0) {
	// 		return $data;
	// 	}
	// 	return [];
	// }

	public function get_list_student_statistical($class_id, $subject_id, $current_date) {
		$current_page = $_GET['p'] ?? 1;
		$length = $_GET['length'] ?? 10;

		$sql = "SELECT COUNT(student_id) AS count FROM students
		WHERE class_id = '{$class_id}'; ";

		$data = $this->get_data($sql);

		if(count($data) === 0) {
			return [];
			exit();
		}

		$count = +$data[0]['count'];
		$total_page = ceil($count / $length);
		$offset = ($current_page - 1) * $length;

		$sql = "SELECT T1.student_id, T1.student_name, T1.student_gender, T1.student_birthday, T2.count, T1.student_avatar
		FROM (
		SELECT S.student_id , S.student_name, S.student_birthday, S.student_gender, S.student_avatar 
		FROM students S 
		WHERE S.class_id = '{$class_id}') T1
		LEFT JOIN(
		SELECT LL.student_id, COUNT(LL.list_leave_id) count
		FROM list_leave LL, students S 
		WHERE LL.subject_id = '{$subject_id}'
		AND LL.leave_date <= '{$current_date}'
		AND LL.student_id = S.student_id
		AND S.class_id = '{$class_id}'
		GROUP BY LL.student_id, LL.subject_id) T2
		ON T1.student_id = T2.student_id LIMIT {$length} OFFSET {$offset} ; ";


		$data = $this->get_data($sql);

		if(count($data) !== 0) {
			return [
				'total_page' => $total_page,
				'data' => $data
			];

		}
		return [];
	}


	public function get_list_leave_date($student_id, $subject_id, $current_date) {
		$sql = "SELECT LL.student_id, LL.leave_date, LL.leave_reason, LL.is_enable
		FROM list_leave LL
		WHERE LL.student_id = '{$student_id}'
		AND LL.subject_id = '{$subject_id}'
		AND LL.leave_date <= '{$current_date}'; ";

		$data = $this->get_data($sql);

		if(count($data) !== 0) {
			return $data;
		}
		return [];
	}




}