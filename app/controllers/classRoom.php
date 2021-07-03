<?php 
class ClassRoom extends Controller {
	public function index() {
		$this->get_all_class_by_teacher();
	}


	public function get_all_class_by_teacher($teacher_id = null) {
		$classModelObj = $this->model('ClassModel');
		$result = $classModelObj->get_class($teacher_id);
		exit( json_encode($result) );
	}

	// public function check_teach_detail($teacher_id = null, $class_id = null, $subject_id = null) {
	// 	if( $teacher_id !== null && $subject_id !== null && $class_id !== null) {
	// 		$classModelObj = $this->model('ClassModel');
	// 		$state = $classModelObj->get_is_exist_teach_detail($teacher_id, $class_id, $subject_id);
	// 		exit( json_encode($state) );
	// 	}
	// 	exit( json_encode(['state' => -1]) );
	// }
}
