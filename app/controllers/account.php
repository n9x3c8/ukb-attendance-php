<?php 
class Account extends Controller {
	private $username;
	private $password;
	private $id;

	public function __construct() {
		$this->username = null;
		$this->password = null;
		$this->id = null;
	}

	public function index() {}

	public function login() {
		$data = json_decode(file_get_contents('php://input'), true);
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$account = $this->model('AccountModel');
			$uuid = $data['uuid'];
			$this->username = $data['username'];
			$this->password = $data['password'];


			$info_user = $account->get_password_by_username($this->username);
			if($info_user === -1) {
				exit(json_encode(['state' => -1])); // -1 ko ton tai tai khoan
			}

			$hash = $info_user['password'];
			$key_security = $info_user['key_security'];
			$permission_id = $info_user['permission_id'];
			if($key_security === NULL) {
				$update = $account->update_key_security($this->username, $uuid);
			}

			// xac thuc
			$is_verify_password = password_verify($this->password, $hash);
			$cond_device_id = $uuid === $key_security;

			if(!$is_verify_password) {
				exit(json_encode(['state' => -2]));
			}

			if(!$cond_device_id) {
				exit(json_encode(['state' => -3]));
			}
			exit(json_encode(['state' => 1, 'permission' => $permission_id]));
		}
	}

	//Student
	public function info_details_student($account_id) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$account = $this->model('AccountModel');
			if($account_id) {
				$info = $account->get_info_details_student($account_id);
				exit( json_encode($info) );
			}
			exit( json_encode(['state' => -1]) );
		}
	}



	public function update_profile_student() {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$id = $_POST['student_id'];
			$permission_id = $_POST['permission_id'];
			$name = $_POST['student_name'];
			$address = $_POST['student_address'];
			$email = $_POST['student_email'];
			$phone = $_POST['student_numphone'];
			$avatar = $_POST['student_avatar'];

			$upload_avatar = $this->upload_avatar($avatar, $permission_id);
			$upload_state_avatar = $upload_avatar['state'] === 'upload_success' ? 1 : $upload_avatar['state'];

			$account = $this->model('AccountModel');
			$update_state = $account->update_info_details_student($id, $name, $address, $email, $phone);

			$update_state_info = $update_state ? 1 : -1;

			$info = [
				'avatar_new' => $upload_avatar['filename'] ?? '',
				'upload_state_avatar' => $upload_state_avatar,
				'update_state_info' => $update_state_info
			];

			exit(json_encode($info));
		}

	}





	public function info_details_teacher($teacher_id) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			if($teacher_id !== null) {
				$account = $this->model('AccountModel');
				$info = $account->get_info_details_teacher($teacher_id);
				echo count($info) !== 0 ? json_encode($info) : json_encode(['state' => -1]);
				exit();
			}
			exit(json_encode(['state' => -1]));
		}
	}

	public function update_profile_teacher() {
		$data = json_decode(file_get_contents('php://input'), true);
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if($data !== null) {
				$account = $this->model('AccountModel');
				$id = $data['id'];
				$address = $data['address'];
				$phone = $data['phone'];
				$email = $data['email'];
				echo $account->update_info_details_teacher($id, $address, $phone, $email) ? json_encode(['state' => 1]) : json_encode(['state' => -1]);
				exit();
			}
			exit( json_encode(['state' => -1]) );
		}
	}

	// permission_id = 1 || 2
	private function upload_avatar($avatar, $permission_id) {
		$username = null;
		$array_info = null;
		
		$user_id = $permission_id == 1 ? 'student_id' : 'teacher_id';
		
		if(isset($_POST[$user_id])) {
			$username = $_POST[$user_id];
		}


		if( !isset($_FILES['image']) ) {
			return ['state' => 'file_not_found'];
			exit();
		}
		
		$temp_name = $_FILES['image']['tmp_name'];
		$file_size = $_FILES['image']['size'];
		$type = explode('/', $_FILES['image']['type']);
		$extension = end($type);
		$allowed = ['png', 'jpg', 'jpeg'];

		if( !in_array($extension, $allowed) ) {
			return ['state' => 'not_match_ext'];
		}

		if( !($file_size <= 500000) )  {
			return ['state' => 'file_is_too_large'];
		}


		// kiem tra file ton tai
		$file = '../public/images/' . $avatar;
		$is_exist = file_exists($file);

		if($is_exist) {
			unlink($file);
		}

		$uniqid = uniqid($username . '-', false);
		$path = '../public/images/' . $uniqid . '.' . $extension;

		$is_upload_success = move_uploaded_file($temp_name, $path);

		if($is_upload_success) {
			
			if($permission_id == 1) {
				$array_info = ['students', 'student_id', 'student_avatar'];
			} else {
				$array_info = ['teachers', 'teacher_id', 'teacher_avatar'];
			}

			$avatar_name = $uniqid . '.' . $extension;
			
			$is_update_avatar = $this->avatar_filename($username, $avatar_name, $array_info);

			return $is_update_avatar ? ['state' => 'upload_success', 'filename' => $avatar_name] : ['state' => 'upload_failed'];
		}
		return ['state' => 'upload_failed'];
	}

	private function avatar_filename($username, $avatar_name, $array_info) {
		$account = $this->model('AccountModel');
		return $account->update_avatar_filename($username, $avatar_name, $array_info);
	}


	// col = 1 | 2
	public function avatar_user($username = null, $permission = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$array_info = null;

			$account = $this->model('AccountModel');
			
			if($permission == 2) {
				$array_info = ['teachers', 'teacher_id', 'teacher_avatar'];
			} else {
				$array_info = ['students', 'student_id', 'student_avatar'];
			}

			$data = $account->get_avatar_user($username, $array_info);
			exit( json_encode($data) );
		}
	}



	public function reset() {
		$account = $this->model('AccountModel');
		$data = $account->on_reset();
		exit(json_encode($data));
	}


}