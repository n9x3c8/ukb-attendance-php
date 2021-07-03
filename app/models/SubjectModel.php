<?php 
class SubjectModel extends DB {
	public function get_subjects($teacher_id, $class_id) {
		$sql = " SELECT DISTINCT S.subject_id, S.subject_name FROM subjects AS S, teach_details AS TD ";
		$sql .= "WHERE S.subject_id = TD.subject_id AND TD.teacher_id = '{$teacher_id}' AND TD.class_id = '{$class_id}'; ";
		return $this->get_data($sql);
	}

	public function get_list_subject_by_student($student_id) {
		$sql = " SELECT Sb.subject_id, Sb.subject_name ";
		$sql .= " FROM students S, teach_details TD, subjects Sb ";
		$sql .= " WHERE S.class_id = TD.class_id AND TD.subject_id = Sb.subject_id AND S.student_id = '{$student_id}' ; ";
		return $this->get_data($sql);
	}
}
