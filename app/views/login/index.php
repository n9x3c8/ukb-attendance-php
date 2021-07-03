<style>
	label {
		margin-left: 8px;
	}
	input[type="text"] {
		margin-bottom: 8px;
	}
	.box {
		display: block;
		margin-bottom: 8px;
	}
</style>

<h1>Login</h1>

<?php 
	$id = $data['state'] ?? '';
	if($id == 1) {
		echo 'La sinh vien';
	} elseif($id == 2) {
		echo 'La giang vien';
	} elseif($id == -1) {
		echo 'Dang nhap ko thanh cong';
	}
 ?>

<form method="POST">
	<div class="box">
		<label>Mã sinh viên/giảng viên:</label>
		<input type="text" name="txt-username" />
	</div>
	<div class="box">
		<label>Mật khẩu:</label>
		<input type="password" name="txt-password" />
	</div>
	<button type="submit" name="btn-login">Đăng nhập</button>
</form>