<?php 
class ClassModel extends DB {
	public function get_class($teacher_id) {
		$sql = "SELECT DISTINCT C.class_id, C.class_name FROM class AS C, teach_details AS TD ";
		$sql .= " WHERE C.class_id = TD.class_id AND TD.teacher_id = '{$teacher_id}' ; ";
		return $this->get_data($sql);
	}

	// public function get_is_exist_teach_detail($teacher_id, $class_id, $subject_id) {
	// 	$sql = " SELECT IF (EXISTS (SELECT teach_detail_id FROM teach_details ";
	// 	$sql .= " WHERE teacher_id = '{$teacher_id}' AND class_id = '{$class_id}' AND subject_id = '{$subject_id}' ";
	// 	$sql .= "  GROUP BY teacher_id, class_id, subject_id), 1, 0) AS state; ";
	// 	return $this->get_data($sql);
	// }
}