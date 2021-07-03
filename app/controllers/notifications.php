<?php 

class Notifications extends Controller {

	public function notify_for_student( $student_id = null ) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$notifications = $this->model('NotificationModel');
			$data = $notifications->get_detail_notify_for_student( $student_id );
			exit( json_encode($data) );
		}
	}

	public function check_seen_notification($list_leave_id = null ) {
		if($_SERVER['REQUEST_METHOD'] = 'GET') {
			$notifications = $this->model('NotificationModel');
			$data = $notifications->get_check_seen_notification($list_leave_id);
			exit(json_encode($data));
		}
	}

}