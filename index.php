<?php 
	include_once 'php/include.php';
	if(isset($_POST['submit'])){
		if(empty($_POST['username'])){
			$message = "Username is empty.";
			if(empty($_POST['password'])){
				$message = "Username and Password are empty.";
			}
		}elseif(empty($_POST['password'])){
			$message = "Password is empty.";
		}else{
			$result = login($connect, $_POST['username'], $_POST['password']);
			if ($result) {
				header("Location: php/home.php");
			}else{
				$message = 'Invalid Login Credentials';
			}
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="icon" type="image/png" href="../img/logo.png">
	<title>Attendance Monitoring System</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<div id="header">
		<h1></h1>
	</div><br><br>
	<br><br><br><br>
	<?php if(!empty($message)){ ?>
		<center>
			<div id="error_message_index"><?php echo $message; ?>
				<span style="float: right;margin-right: 10px;">
					<a href="index.php" style="text-decoration: none;color: black;">&times;</a>
				</span>
			</div>
		</center>
	<?php } ?>
	<div id="ams">
		<center>
			<img src="img/ams.gif" height="300" width="800"><br><br>
			<div id="menu"><span id="login_btn"><img src="img/sign-in.png" height="23" width="23"> Login </span></div>
		</center>
	</div>
	<div id="login_form">
		<div id="login-form">
			<form method="POST">
				<center>
					<div id="close_login_form">&times;</div><br><br>
					<span><b>A</b>ccount <b>L</b>ogin <br></span><br>
					<br>
					<img src="img/user-shape.png" height="23" width="23"> <input type="text" name="username" placeholder="Username"><br>
					<img src="img/padlock.png" height="23" width="23"> <input type="password" name="password" placeholder="Password"><br>
					<button name="submit">Login</button><br><br>
				</center>
			</form>
		</div>
	</div>
	<div id="footer">
		<h3>&copy; EVSU-CC Students</h3>
	</div>
	<script type="text/javascript" src="js/js.js"></script>
</body>
</html>