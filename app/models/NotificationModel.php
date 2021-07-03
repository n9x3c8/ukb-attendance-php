<?php 

class NotificationModel extends DB {

	public function get_detail_notify_for_student( $student_id ) {
		$sql = "SELECT LL.list_leave_id, LL.subject_id, LL.leave_date, LL.is_enable, LL.is_seen, SB.subject_name , T.teacher_name, LL.leave_reason, LL.leave_id, LL.denine_reason
		FROM list_leave LL, subjects SB, students ST, teach_details TD, teachers T
		WHERE LL.student_id = '{$student_id}'
		AND LL.subject_id = SB.subject_id
		AND LL.student_id = ST.student_id
		AND ST.class_id = TD.class_id
		AND SB.subject_id = TD.subject_id
		AND TD.teacher_id = T.teacher_id
		ORDER BY LL.list_leave_id DESC
		LIMIT 10;";


		$data = $this->get_data($sql);

		if(count($data) !== 0) {
			return $data;
		}
		return [];
	}

	public function get_check_seen_notification($list_leave_id) {
		$data = ['is_seen' => 1];
		$result = $this->update('list_leave', $data, "list_leave_id = {$list_leave_id}" );
		// UPDATE list_leave SET is_seen = 1 WHERE list_leave_id = 1;
		return $result ? ['state' => 1] : ['state' => -1];
	}

}