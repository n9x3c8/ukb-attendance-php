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

			// xac thuc
			$is_verify_password = password_verify($this->password, $hash);

			if(!$is_verify_password) {
				exit(json_encode(['state' => -2]));
			}

			
			$key_security = $info_user['key_security'];
			$permission_id = $info_user['permission_id'];
			if($key_security === NULL) {
				$update = $account->update_key_security($this->username, $uuid);
				$key_security = $uuid;
			}

			$cond_device_id = $uuid === $key_security;
			if(!$cond_device_id) {
				exit(json_encode(['state' => -3]));
			}
			exit(json_encode(['state' => 1, 'permission' => $permission_id]));
		}
	}

	//Student
	public function info_details_student($account_id, $uuid = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($account_id, $uuid);

			$account = $this->model('AccountModel');
			$info = $account->get_info_details_student($account_id);
			exit( json_encode($info) );
		}
	}


	public function update_profile() {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$info = null;
			$table = null;
			$column_id = null;

			$id = $_POST['id'];
			$permission_id = $_POST['permission'];
			$gender = $_POST['gender'];
			$birthday = $_POST['birthday'];
			$address = $_POST['address'];
			$phone = $_POST['phone'];
			$email = $_POST['email'];
			$account = $this->model('AccountModel');

			if(+$permission_id === 1) {
				$table = 'students';
				$column_id = 'student_id';
				$info = [
					'student_gender' => +$gender,
					'student_birthday' => $birthday,
					'student_address' => $address,
					'student_numphone' => $phone,
					'student_email' => $email,
				];
			} elseif(+$permission_id === 2) {
				$table = 'teachers';
				$column_id = 'teacher_id';
				$info = [
					'teacher_gender' => $gender,
					'teacher_birthday' => $birthday,
					'teacher_address' => $address,
					'teacher_numphone' => $phone,
					'teacher_email' => $email,
				];
			}

			$where = " {$column_id} = '{$id}' ";
			$update_state = $account->update_info_details($table, $info, $where);

			$update_state_info = $update_state ? 1 : -1;

			$info = [
				'update_state_info' => $update_state_info
			];

			exit(json_encode($info));
		}

	}


	public function info_details_teacher($teacher_id, $uuid = null) {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->verify($teacher_id, $uuid);			
			
			$account = $this->model('AccountModel');
			$info = $account->get_info_details_teacher($teacher_id);
			echo count($info) !== 0 ? json_encode($info) : json_encode(['state' => -1]);
		}
	}


	public function upload_avatar($permission_id, $avatar) {
		if($_SERVER['REQUEST_METHOD'] !== 'POST') {
			http_response_code(403);
			exit(json_encode(['state' => -1]));
		}

		if(!isset($_POST['username'])) {
			http_response_code(403);
			exit(json_encode(['state' => 'Tài khoản không tồn tại']));	
		}

		$array_info = null;
		$username = $_POST['username'];
		$user_id = $permission_id == 1 ? 'student_id' : 'teacher_id';

		if( !isset($_FILES['image']) ) {
			exit(json_encode(['state' => 'File không tồn tại']));
		}
		
		$temp_name = $_FILES['image']['tmp_name'];
		$file_size = $_FILES['image']['size'];
		$type = explode('/', $_FILES['image']['type']);
		$extension = end($type);
		$allowed = ['png', 'jpg', 'jpeg'];


		if( !in_array($extension, $allowed) ) {
			exit(json_encode(['state' => 'not_match_ext']));
		}

		if( !($file_size <= 500000) )  {
			exit(json_encode(['state' => 'Dung lượng file quá lớn']));
		}

		// kiem tra file ton tai
		$file = '../public/images/' . $avatar;
		$is_exist = file_exists($file);
		if($is_exist) {
			$del_success = unlink($file);
			if(!$del_success) {
				exit(json_encode(['state' => -1]));
			}
		}


		if($permission_id == 1) {
			$array_info = ['students', 'student_id', 'student_avatar'];
		} else {
			$array_info = ['teachers', 'teacher_id', 'teacher_avatar'];
		}

		// bat dau upload
		$uniqid = uniqid($username . '-', false);
		$path = '../public/images/' . $uniqid . '.' . $extension;
		$is_upload_success = move_uploaded_file($temp_name, $path);
		
		if($is_upload_success) {
			$avatar_name = $uniqid . '.' . $extension;
			
			$is_update_avatar = $this->avatar_filename($username, $avatar_name, $array_info);

			if($is_upload_success) {
				$result = ['state' => 'upload_success', 'filename' => $avatar_name];
				exit(json_encode($result));
			}
			$result = ['state' => 'upload_failed'];
			exit(json_encode($result));
		}
		return ['state' => 'upload_failed'];
	}


	private function avatar_filename($username, $avatar_name, $array_info) {
		$account = $this->model('AccountModel');
		return $account->update_avatar_filename($username, $avatar_name, $array_info);
	}



	public function reset() {
		$account = $this->model('AccountModel');
		$data = $account->on_reset();
		exit(json_encode($data));
	}

	private function verify($username = null, $uuid = null) {
		$verify = $this->model('VerifyModel');
		$data = $verify->get_key_security($username);
		if(!$data) {
			exit(json_encode(['state' => -403]));
		}
		
		if($data['id'] !== $username || $data['uuid'] !== $uuid) {
			http_response_code(403);
			exit(json_encode(['state' => -403]));
		}
	}

}
