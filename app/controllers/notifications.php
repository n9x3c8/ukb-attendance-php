<?php 

class Notifications extends Controller {

	public function notify_for_student( $student_id = null, $uuid = null ) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($student_id, $uuid);

			$notifications = $this->model('NotificationModel');
			$data = $notifications->get_detail_notify_for_student( $student_id );
			exit( json_encode($data) );
		}
	}

	public function check_seen_notification($username = null, $list_leave_id = null, $uuid = null ) {
		if($_SERVER['REQUEST_METHOD'] = 'GET') {
			$this->verify($username, $uuid);
			
			$notifications = $this->model('NotificationModel');
			$data = $notifications->get_check_seen_notification($list_leave_id);
			exit(json_encode($data));
		}
	}

	private function verify($username = null, $uuid = null) {
		$verify = $this->model('VerifyModel');
		$data = $verify->get_key_security($username);

		if(!$data) {
			exit(json_encode(['state' => -403]));
		}
		
		if(strtolower($data['id']) !== strtolower($username) || $data['uuid'] !== $uuid) {
			exit(json_encode(['state' => -403]));
		}
	}

}