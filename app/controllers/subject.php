<?php 
class Subject extends Controller {
	public function index() {
		$this->get_all_subject_by_teacher();
	}

	public function get_all_subject_by_teacher($teacher_id = null, $class_id = null) {
		// Fix bug dang nhap
		$subjectModelObj = $this->model('SubjectModel');
		$subjects = $subjectModelObj->get_subjects($teacher_id, $class_id);
		exit( json_encode($subjects) );
	}


	// lay ra thong tin mon hoc voi id sinh vien
	public function list_subject_by_student($student_id) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$subjectModelObj = $this->model('SubjectModel');
			$subjects = $subjectModelObj->get_list_subject_by_student($student_id);
			if(count($subjects) !== 0) {
				exit( json_encode($subjects) );
			}
			exit( json_encode(['state' => -1]) );
		}
	}
}
