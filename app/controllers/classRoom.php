<?php 
class ClassRoom extends Controller {
	public function index() {
		$this->get_all_class_by_teacher();
	}


	public function get_all_class_by_teacher($teacher_id, $uuid = null) {
		$this->verify($teacher_id, $uuid);

		$classModelObj = $this->model('ClassModel');
		$result = $classModelObj->get_class($teacher_id);
		exit( json_encode($result) );
	}

	private function verify($username = null, $uuid = null) {
		$verify = $this->model('VerifyModel');
		$data = $verify->get_key_security($username);
		if(!$data) {
			exit(json_encode(['state' => -403]));
		}
		
		if($data['id'] !== $username || $data['uuid'] !== $uuid) {
			exit(json_encode(['state' => -403]));
		}
	}

}
