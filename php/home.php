<?php  
	include('include.php');
	date_default_timezone_set("Asia/Manila");
	if($_SESSION['logged_in']){
		if(!empty($_GET['logout']) && $_GET['logout'] == true){
			logout();
			header('Location: ../index.php');
		}
		$student_details = get_students($connect);

// BUTTON FUNCTIONS
		if(isset($_POST['add_student'])){
			if($_POST['acc_password1'] == $_POST['acc_password2']){
				$result = add_student($connect, $_POST['acc_fname'], $_POST['acc_mname'], $_POST['acc_lname'], $_POST['acc_email'], $_POST['acc_username'], $_POST['acc_password1'], $_POST['acc_type']);
				if(!$result){
					$message = 'Account Information is Invalid';
				}else{
					$message = 'Account has been added!';
				}
			}else{
				$message = 'Password do not match!';
			}
			
		}elseif(isset($_POST['edit_student'])){
			edit_student($connect, $_POST['acc_id'], $_POST['acc_fname'],$_POST['acc_mname'], $_POST['acc_lname'], $_POST['acc_email'], $_POST['acc_username'], $_POST['acc_password'], $_POST['acc_type']);
			$message = 'Account Information has been updated';
									
		}elseif(isset($_POST['delete_student_yes'])){
			delete_student($connect, $_POST['acc_id']);
			$message = 'Account has been deleted!';
		}elseif(isset($_POST['delete_student_no'])){
			header('Location: home.php');
		}elseif (isset($_POST['add_subject'])) {
			$result = add_subject($connect, $_POST['subject_code'], $_POST['subject_name']);
			if ($result) {
				$message = 'Subject has been added';
			}else{
				$message = 'Subject already exist';
			}
			
		}elseif(isset($_POST['enroll'])){
			enroll_student($connect, $_POST['acc_id'], $_POST['subject_id']);

		}elseif(isset($_POST['present'])){
			attendance_present($connect, $_POST['subject_id'], $_POST['acc_id'], $_POST['date']);
		}elseif(isset($_POST['absent'])){
			attendance_absent($connect, $_POST['subject_id'], $_POST['acc_id'], $_POST['date']);
		}elseif(isset($_POST['edit_present'])){
			edit_attendance_present($connect, $_POST['subject_id'], $_POST['acc_id'], $_POST['date']);
		}elseif(isset($_POST['edit_absent'])){
			edit_attendance_absent($connect, $_POST['subject_id'], $_POST['acc_id'], $_POST['date']);
		}elseif(isset($_POST['clear_remarks'])){
			clear_remarks($connect, $_POST['subject_id'], $_POST['acc_id'], $_POST['date']);
			header('Location: home.php?action=student_attendance');
		}elseif (isset($_POST['save_subject'])) {
			save_subject($connect, $_POST['subject_id'], $_POST['subject_code'], $_POST['subject_name']);
			$message_subject='Edit Subject was successful';
		}elseif(isset($_POST['cancel'])){
			header('Location: home.php?action=subjects');
		}elseif(isset($_POST['drop_student'])){
			drop_student($connect, $_POST['acc_id'], $_POST['subject_id']);
			header('Location: home.php?action=subjects');
		}
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="icon" type="image/png" href="../img/logo.png">
	<title>Attendance Monitoring System - Home</title>
	<link rel="stylesheet" type="text/css" href="../css/style.css">
</head>
<body>
	<div id="header">
		<div id="account_details_back">
		</div>
			<div id="account_details">
				<img src="../img/default-img.png" height="70" width="70">
				<label><?php echo $_SESSION['acc_fname'].' '.$_SESSION['acc_mname'].' '.$_SESSION['acc_lname'] ?></label>
				<span><a href="?logout=true"><img src="../img/sign-out.png" height="20" width="20" title="Logout"></a><a href="?action=settings"><img src="../img/settings.png" height="20" width="20" style="margin-top: -20px;margin-left: 30px;" title="Settings"></a><a href="?"><img src="../img/home.png" height="20" width="20" style="margin-top: -20px;margin-left: 60px;" title="Go to home"></a></span>
			
			</div>
		<h1>Attendance Monitoring System</h1>
	</div><br><br><br>
	<?php if(empty($_GET['action'])){ ?>
	<div id="container1">
		<center>
			<?php if(!empty($message)){ ?>
				<?php if($message == 'Account has been added!' || $message == 'Account Information has been updated'){ ?>
					<div id="success_message_home"><?php echo $message; ?>
						<span style="float: right;margin-right: 10px;">
							<a href="home.php" style="text-decoration: none;color: black;">&times;</a>
						</span>
					</div>
				<?php }else{ ?>
					<div id="error_message_home"><?php echo $message; ?>
						<span style="float: right;margin-right: 10px;">
							<a href="home.php" style="text-decoration: none;color: black;">&times;</a>
						</span>
					</div>
				<?php } ?>
			<?php } ?>
			<div><br><br><br>
				<h1 style="width: 45%;"><code>Welcome</code><hr><b style="font-size: 40pt;margin-top: 200px;"><?php echo strtoupper($_SESSION['acc_type']); ?></b></h1>
			</div><br><br><br><br><br>
			<div id="menu_home">
				<?php if($_SESSION['acc_type'] == 'Administrator'){ ?>
				<div id="student_record">Student Record</div>
				<?php } ?>
				<?php if($_SESSION['acc_type'] == 'Student'){ ?>
				<a href="?action=my_attendance" style="color: black;"><div id="student_attendance">My Attendance</div></a>
				<?php }else{ ?>
				<a href="?action=student_attendance" style="color: black;"><div id="student_attendance">Student Attendance</div></a>
				<?php } ?>
				<a href="?action=subjects" style="color: black;"><div id="subjects">Subject</div></a>
				<?php if($_SESSION['acc_type'] == 'Administrator'){ ?>
				<a href="?action=accounts" style="color: black;"><div id="accounts">Accounts</div></a>
				<?php } ?>
			</div>
		</center>
	</div><br><br><br>

<!-- STUDENT RECORDS SECTION -->
	<div id="container_student_record">
			<h1><img src="../img/students.png" height="30" width="30"> Student Records<span style="float: right;margin-right: 20px;cursor: pointer;" title="Back" id="close_container_student_record">&times;</span></h1><hr>
			<span id="add_student">Add Student</span><hr><br>
			<table width="100%" border="1" cellspacing="0" cellpadding="8">
				<tr style="background: gray">
					<th width="30%" colspan="2">Name</th>
					<th width="20%">Email</th>
					<th width="15%">Password</th>
					<th colspan="2">Action</th>
				</tr>
				<?php foreach ($student_details as $value_students) { ?>
					<tr>
						<td width="30%" colspan="2"><?php echo $value_students['acc_fname'].' '.$value_students['acc_mname'].' '.$value_students['acc_lname'] ?></td>
						<td width="30%"><?php echo $value_students['acc_email'] ?></td>
						<td width="25%"><?php echo $value_students['acc_password'] ?></td>
						<td colspan="2" align="center">
							<a href="?acc_id=<?php echo $value_students['acc_id'] ?>&action=edit_student" id="edit_student">Edit</a>
							<a href="?acc_id=<?php echo $value_students['acc_id'] ?>&action=delete_student" id="delete_student">Delete</a>
						</td>
					</tr>
				<?php } ?>
			</table>
	</div>
	<center>
		<div id="add_student_form"><span id="close_add_student_form">&times;</span>
			<h3>Add Student Form</h3><hr style="width: 80%;"><br>
			<form method="POST">
				<input type="text" name="acc_fname" placeholder="Firstname" required><br>
				<input type="text" name="acc_mname" placeholder="Middlename" required><br>
				<input type="text" name="acc_lname" placeholder="Lastname" required><br>
				<input type="email" name="acc_email" placeholder="Email Address" required><br>
				<input type="text" name="acc_username" placeholder="Username" required><br>
				<input type="password" name="acc_password1" placeholder="Password" required><br>
				<input type="password" name="acc_password2" placeholder="Confirm Password" required><br>
				<input type="hidden" name="acc_type" value="Student">
				<button name="add_student"><img src="../img/add.png" height="30" width="30" style="position: absolute;margin-left: -33px;">Add Student</button><br><br><br>
			</form>
		</div>
	</center>

		<?php }elseif(!empty($_GET['action']) && $_GET['action'] == 'edit_student'){ ?>
			<style type="text/css">
				#add_student_form{
					display: none;
				}
				#container_student_record{
					display: none;
				}
				#container1{
					display: none;
				}
			</style>
			<?php $edit_student_details = get_students_edit($connect, $_GET['acc_id']); ?>
			<br><br><br><br>
			<div id="edit_student_form">
				<?php if(!empty($message)){ ?>
					<?php if($message == 'Account Information has been updated'){ ?>
						<div id="success_message_home" style="width: 100%;text-indent: 10px;"><?php echo $message; ?>
							<span style="float: right;margin-right: 10px;">
								<a href="home.php" style="text-decoration: none;color: black;">&times;</a>
							</span>
						</div>
					<?php } ?>
				<?php } ?>
				<h3 style="text-indent: 30px;"><img src="../img/edit.png" height="20" width="20" style="position: absolute;margin-left: -25px;">Edit Account Form <a href="home.php"><img src="../img/close.png" height="20" width="20" style="float: right;margin-right: 15px;" title="Close"></a></h3><hr><br>
				<form method="POST">
					<?php foreach ($edit_student_details as $value_students_edit) { ?>
					<span>
						<input type="hidden" name="acc_id" value="<?php echo $value_students_edit['acc_id'] ?>">
						Firstname: <br><input type="text" name="acc_fname" placeholder="Firstname" value="<?php echo $value_students_edit['acc_fname'] ?>" required><br>
						Middlename: <br><input type="text" name="acc_mname" placeholder="Middlename" value="<?php echo $value_students_edit['acc_mname'] ?>" required><br>
						Lastname: <br><input type="text" name="acc_lname" placeholder="Lastname" value="<?php echo $value_students_edit['acc_lname'] ?>" required><br>
						Email Address: <br><input type="email" name="acc_email" placeholder="Email Address" value="<?php echo $value_students_edit['acc_email'] ?>" required><br>
					</span>
					<label>
						Username: <br><input type="text" name="acc_username" placeholder="Username" value="<?php echo $value_students_edit['acc_username'] ?>" required><br>
						Password: <br><input type="text" name="acc_password" placeholder="Password" value="<?php echo $value_students_edit['acc_password'] ?>" required><br>
						Account Type: <br>
						<select name="acc_type" required style="border-radius: 0px;">
							<option value="Student">Student</option>
							<option value="Administrator">Administrator</option>
							<option value="Instructor">Instructor</option>
						</select><br>
					</label>
					<?php } ?>
					<button name="edit_student">Save</button><br><br><br>
				</form>
			</div>
		<?php }elseif(!empty($_GET['action']) && $_GET['action'] == 'delete_student'){ ?>
				
				<?php if(!empty($message)){ ?>
				<br><br><br><br><br><br><br><br><br><br><br><br>
					<?php if($message == 'Account has been deleted!'){ ?>
						<div id="error_message_home" style="width: 36.5%;text-indent: 10px;margin: 0 auto;border-radius: 0px;"><?php echo $message; ?>
							<span style="float: right;margin-right: 10px;">
								<a href="home.php" style="text-decoration: none;color: black;">&times;</a>
							</span>
						</div>
					<?php } ?>
				<?php } ?><br>
				<?php $delete_students_details = get_students_edit($connect, $_GET['acc_id']); ?>
				<?php foreach ($delete_students_details as $value) { ?>
				<div id="delete_student_form">

					<center><h3>Are you sure you want to delete?</h3></center><hr><br>
					<table cellspacing="10">
						<tr>
							<td align="right" width="120px;"><b>Fullname:</b></td>
							<td><?php echo $value['acc_fname'].' '.$value['acc_mname'].' '.$value['acc_lname']; ?></td>
						</tr>
						<tr>
							<td align="right"><b>Username:</b></td>
							<td><?php echo $value['acc_username'] ?></td>
						</tr>
						<tr>
							<td align="right"><b>Email:</b></td>
							<td><?php echo $value['acc_email'] ?></td>
						</tr>
					</table>
					<br><hr><br>
					<center>
						<form method="POST">
							<input type="hidden" name="acc_id" value="<?php echo $value['acc_id'] ?>">
							<button name="delete_student_yes">Yes</button>
							<button name="delete_student_no">No</button>
						</form>
					</center><br>
				</div>
				<?php } ?>
<!-- STUDENT ATTENDANCE SECTION -->
		<?php }elseif(!empty($_GET['action']) && $_GET['action'] == 'student_attendance'){ ?>
			<?php 
				if(empty($_GET['subject'])){ 
					if(isset($_POST['search_student_attendance_btn'])){
						if(empty($_POST['search_student_attendance'])){
							$student_attendance = get_students($connect); 
						}else{
							$student_attendance = get_students_search_student_attendance($connect, $_POST['search_student_attendance']);
						}
						
					}else{
						$student_attendance = get_students($connect); 
					}
				}else{ 
					$student_attendance = get_students_subject($connect, $_GET['subject']); 
				} 
			?>
			<br><br><br>
		<div id="container_student_attendace">
				<br><img src="../img/check.png" height="30" width="30" style="float: left;margin-right: 5px;"> <b style="font-size: 18pt;">Attendance</b>
				<a href="home.php"><img src="../img/close.png" height="31" width="31" style="float: right;" title="Close"></a>
				<br><br><hr>
				<a href="home.php?action=attendance_sheet" style="font-size: 10pt; color: black;text-decoration: none;border: 1px solid black;padding: 5px 5px;">View Attendance Sheet</a>
				<?php if($_SESSION['acc_type'] == 'Administrator'){ ?>
				<a href="home.php?action=subjects" style="font-size: 10pt; color: black;text-decoration: none;border: 1px solid black;padding: 5px 5px;">View Subjects</a>
				<?php } ?>
				<br><hr>
				<div id="form_subjects_stud_att">
						<?php $subjects2 = get_subjects($connect); ?>
						<?php if(empty($_GET['subject'])){ ?>
							<a href="home.php?action=student_attendance" style="text-decoration: underline;">All</a>
						<?php }else{ ?>
							<a href="home.php?action=student_attendance">All</a>
						<?php } ?>
						<?php foreach($subjects2 as $value){ ?>

							<a href="home.php?action=student_attendance&subject=<?php echo $value['subject_id'] ?>" style="margin-right: 4px;">
								<?php if(!empty($_GET['subject']) && $value['subject_id'] == $_GET['subject']){ ?>
									<?php $subject = $value['subject_code'] ?>
									<span style="text-decoration: underline;"><?php echo $value['subject_code'] ?></span>
								<?php }else{ ?>
									<?php echo $value['subject_code'] ?>
								<?php } ?>
							</a> 
						<?php } ?> 
				</div><hr><br>
				<?php if(empty($_GET['subject'])){ ?>
					<form method="POST" id="form_search_student_attendance" ?>
						<input type="text" name="search_student_attendance" placeholder="Search...">
						<button name="search_student_attendance_btn" style="padding: 2px 5px;float: right;border: 1px solid gray;"><img src="../img/search.png" width="20" height="20"></button>
					</form>
				<?php } ?>
				<?php if(empty($_GET['subject'])){ ?>
					<span style="font-size: 10pt;color: red;">*Below are the list of students who registered in the system.</span>
						<br><br>
				<?php }else{ ?>
					<span style="font-size: 10pt;color: red;">*Below are the list of students who enrolled <b><?php echo $subject ?></b> subject.</span><br><br>
				<?php } ?>
			<br>
			<table border="1" cellspacing="0" cellpadding="10" width="100%" style="margin: 0 auto;">
				<tr style="background: gray;">
					<th width="60%">Name</th>
					<th>Remarks</th>
				</tr>
				<?php if(empty($student_attendance)){ ?>
					<tr>
						<td colspan="2" align="center" style="color: red;">*** NO ENROLLED STUDENT FOUND ***</td>
					</tr>
				<?php }else{ ?>

				<?php foreach($student_attendance as $value){ ?>
				<?php if(empty($student_attendance) || $value['acc_type'] != 'Student'){ ?>
					<tr>
						<td colspan="2" align="center" style="color: red;">*** NO RESULTS FOUND ***</td>
					</tr>
				<?php }else{ ?>
					<tr>
						<td><?php echo $value['acc_fname'].' '.$value['acc_mname'].' '.$value['acc_lname'] ?></td>
						<td align="center">
							<?php if(empty($_GET['subject'])){ ?>
							<a href="home.php?action=subjects" style="text-decoration: none;color: black;padding: 5px 5px;border: 1px solid black;">Enroll Now</a>
							<?php }else{ ?>
							<?php $result = check_attendance($connect, $value['acc_id'], $_GET['subject']); ?>
							<?php if(!empty($result)){ ?>
								<?php foreach($result as $value_attendance){ ?>
									<?php if($value_attendance['date_attendance'] == date('M d, Y')){ ?>
										<?php if($value_attendance['remarks'] == 'Present'){$color='blue';}else{$color='red';} ?>
										<span style="font-size: 10pt;color: <?php echo $color ?>;">
											<?php echo $value_attendance['remarks'] ?> as of <?php echo $value_attendance['date_attendance'] ?> 
											<a href="home.php?action=edit_student_attendance&subject_id=<?php echo $_GET['subject'] ?>&acc_id=<?php echo $value['acc_id'] ?>&date=<?php echo $value_attendance['date_attendance'] ?>">
												<img src="../img/edit.png" height="10" width="10">
											</a>
										</span>
									<?php }else{ ?>
										<form method="POST">
											<input type="hidden" name="subject_id" value="<?php echo $_GET['subject'] ?>">
											<input type="hidden" name="acc_id" value="<?php echo $value['acc_id'] ?>">
											<input type="hidden" name="date" value="<?php echo date('M d, Y') ?>">
											<button name="present">Present</button>
											<button name="absent">Absent</button>
										</form>
									<?php } ?>
								<?php } ?>
							<?php }else{ ?>
								<form method="POST">
									<input type="hidden" name="subject_id" value="<?php echo $_GET['subject'] ?>">
									<input type="hidden" name="acc_id" value="<?php echo $value['acc_id'] ?>">
									<input type="hidden" name="date" value="<?php echo date('M d, Y') ?>">
									<button name="present">Present</button>
									<button name="absent">Absent</button>
								</form>
							<?php } ?>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
				<?php } ?>
				<?php } ?>
			</table>
		</div>
<!-- EDIT STUDENT ATTENDANCE SECTION -->
		<?php }elseif(!empty($_GET['action']) && $_GET['action'] == 'edit_student_attendance'){ ?>
			<?php $result_attendance = get_attendance_edit($connect, $_GET['subject_id'], $_GET['acc_id'], $_GET['date']); ?><br><br><br><br><br>
				<div id="form_edit_student_attendance">
					<h3><img src="../img/edit.png" height="25" width="25"> Edit Student Attendance <a href="home.php?action=student_attendance"><img src="../img/close.png" height="20" width="20" style="float: right;margin-right: 15px;" title="Close"></a></h3><hr>
						<?php foreach($result_attendance as $value_attendance){ ?>
							<table cellspacing="10">
								<tr>
									<td align="right"><b>Student Name:</b></td>
									<td><?php echo $value_attendance['acc_fname'].' '.$value_attendance['acc_mname'].' '.$value_attendance['acc_lname'] ?></td>
								</tr>
								<tr>
									<td align="right"><b>Subject: </b></td>
									<td><?php echo $value_attendance['subject_code'] ?> (<?php echo $value_attendance['subject_name'] ?>)</td>
								</tr>
								<tr>
									<td align="right"><b>Remarks: </b></td>
									<?php if($value_attendance['remarks'] == 'Present'){$color='blue';}else{$color='red';} ?>
									<td><span style="color: <?php echo $color; ?>"><?php echo $value_attendance['remarks'] ?> as of <?php echo $value_attendance['date_attendance'] ?></span></td>
								</tr>
							</table>
							<hr><br>
								<?php if($value_attendance['remarks'] == 'Present'){ ?>
									<form method="POST">
										<input type="hidden" name="subject_id" value="<?php echo $value_attendance['subject_id'] ?>">
										<input type="hidden" name="acc_id" value="<?php echo $value_attendance['acc_id'] ?>">
										<input type="hidden" name="date" value="<?php echo $value_attendance['date_attendance'] ?>">
										<button name="edit_absent">Make Absent</button>
										<button name="clear_remarks" style="float: right;">Clear Remarks</button>
									</form>
								<?php }else{ ?>
									<form method="POST">
										<input type="hidden" name="subject_id" value="<?php echo $value_attendance['subject_id'] ?>">
										<input type="hidden" name="acc_id" value="<?php echo $value_attendance['acc_id'] ?>">
										<input type="hidden" name="date" value="<?php echo $value_attendance['date_attendance'] ?>">
										<button name="edit_present">Make Present</button>
										<button name="clear_remarks" style="float: right;">Clear Remarks</button>
									</form>
								<?php } ?>
						<?php } ?>
						
					</form>
				</div>
<!-- ATTENDANCE SHEET SECTION -->
		<?php }elseif(!empty($_GET['action']) && $_GET['action'] == 'attendance_sheet'){ ?>
			<?php if(isset($_POST['search_subject'])){ ?>
				<?php if($_POST['subjects'] == 'none'){
					$message = 'Please select subject.';
					$attendance = get_attendance($connect);
				}else{ 
					
					$attendance = get_attendance_by_subject($connect, $_POST['subjects']);
					foreach($attendance as $value){
						$message_attendance_sheet = 'Attendance for the subject '.'<b>'.$value['subject_code'].' ('.$value['subject_name'].')</b>';
					}
				} ?>
			<?php }elseif(isset($_POST['search_date_attendance'])){ ?>
				<?php if($_POST['date_attendance'] == 'none'){ 
					$message = 'Please select date.';
					$attendance = get_attendance($connect);
				}else{ 
					 $attendance = get_attendance_by_date_attendance($connect, $_POST['date_attendance']); 
					 $message_attendance_sheet = 'Attendance for the date '.'<b>'.$_POST['date_attendance'].'</b>';
				 } ?>
			<?php }else{ ?>
				<?php $attendance = get_attendance($connect); ?>
				<?php $message_attendance_sheet = 'Attendance for all subjects and dates.'; ?>
			<?php } ?>
			
			<?php $subjects = get_subjects($connect); ?>
			<?php $attendance_sheet_date = get_attendance_sheet_date($connect); ?>
			<br><br>
			<?php if(!empty($message)){ ?>
				<div id="error_message_home" style="width: 61.5%;text-indent: 10px;margin: 0 auto;border-radius: 0px;"><?php echo $message; ?>
							<span style="float: right;margin-right: 10px;">
								<a href="home.php?action=attendance_sheet" style="text-decoration: none;color: black;">&times;</a>
							</span>
						</div>
			<?php } ?>
			<div id="attendance_sheet">
				<h1><img src="../img/attendance.png" height="30" width="30" style="float: left;margin-right: 10px;"> Attendance Sheet <a href="home.php?action=student_attendance"><img src="../img/close.png" height="30" width="30" style="float: right;" title="Close"></a></h1><hr>
				<form method="POST" style="float: left;margin-top: -7px;">
					<select name="subjects">
						<option value="none">Search by subjects</option>
						<?php foreach($subjects as $value_subjects){ ?>
						<option value="<?php echo $value_subjects['subject_id'] ?>"><?php echo $value_subjects['subject_code'] ?></option>
						<?php } ?>
					</select>
					<button name="search_subject" style="cursor: pointer;float: right;margin-top: 10px;"><img src="../img/search.png" height="15" width="15"></button>
				</form>
				<form method="POST" style="float: right;margin-top: -7px;">
					<select name="date_attendance">
						<option value="none">Search by date</option>
						<?php foreach($attendance_sheet_date as $value_subjects){ ?>
						<option value="<?php echo $value_subjects['date_attendance'] ?>"><?php echo $value_subjects['date_attendance'] ?></option>
						<?php } ?>
					</select>
					<button name="search_date_attendance" style="cursor: pointer;float: right;margin-top: 10px;"><img src="../img/search.png" height="15" width="15"></button>
				</form>
				<br><br><hr>
				<?php if(!empty($message_attendance_sheet)){ ?>
					<span style="color: red;font-size: 10pt;">* <?php echo $message_attendance_sheet; ?></span>
				<?php } ?>
				<br><br>
				<table cellspacing="0" cellpadding="10" border="1" width="100%">
					<tr style="background: gray;">
						<th>Student Name</th>
						<th width="35%">Remarks</th>
					</tr>
					<?php if(empty($attendance)){ ?>
						<tr>
							<td colspan="2" align="center"><span style="color: red;font-size: 12pt">*** NO ATTENDANCE FOUND ***</span></td>
						</tr>
					<?php }else{ ?>
						<?php foreach($attendance as $value_attendance){ ?>
							<tr>
								<td><?php echo $value_attendance['acc_fname'].' '.$value_attendance['acc_mname'].' '.$value_attendance['acc_lname'] ?></td>
								<td align="center">
									<?php if($value_attendance['remarks'] == 'Present'){$color = 'blue';}else{$color='red';} ?>
									<span style="color: <?php echo $color; ?>"><?php echo $value_attendance['remarks'] ?> as of <?php echo $value_attendance['date_attendance'] ?></span>
									<a href="home.php?action=edit_student_attendance&subject_id=<?php echo $value_attendance['subject_id'] ?>&acc_id=<?php echo $value_attendance['acc_id'] ?>&date=<?php echo $value_attendance['date_attendance'] ?>">
													<img src="../img/edit.png" height="10" width="10">
												</a>
								</td>
							</tr>
						<?php } ?>
					<?php } ?>
				</table>
			</div>
<!-- SUBJECTS SECTION -->
		<?php }elseif(!empty($_GET['action']) && $_GET['action'] == 'subjects'){ ?>
			<br><br>
			<?php if(empty($_GET['action_subject'])){ ?>
			<div id="subjects_container">

				<?php 
					if(isset($_POST['search_subject_btn'])){
						if(empty($_POST['search_subjects'])){
							$subjects_get = get_subjects($connect); 
						}else{
							$subjects_get = get_subjects_search($connect, $_POST['search_subjects']);
						}
					}else{
						$subjects_get = get_subjects($connect); 
					}
				?>
				<h3><img src="../img/list.png" height="20" width="20" style="float: left;margin-right: 10px;"> Subjects 
					<a href="?">
						<img src="../img/close.png" height="31" width="31" style="float: right;margin-right: 20px;" title="Close">
					</a>
					
				</h3>
				<hr>
				<?php if($_SESSION['acc_type'] == 'Administrator'){ ?>
				<a href="?action=add_subject" style="border: 1px solid black; padding: 5px 5px;text-decoration: none;text-indent: 20px; width: 200px;color: black;">Add Subject
					</a>
				<form method="POST" id="form_search_subject">
					<input type="text" name="search_subjects" placeholder="Search...">
					<button name="search_subject_btn" style="float: right;margin-right: 11px;margin-left: -20px;"><img src="../img/search.png" height="15" width="15"></button>
				</form>
					<br><hr>
				<?php } ?>
					<br>
				<table border="1" cellpadding="10" cellspacing="0" width="100%">
					<tr style="background: gray;">
						<th width="15%">Subject Code</th>
						<th width="55%">Subject Description</th>
						<th>Action</th>
					</tr>
					<?php foreach($subjects_get as $value){ ?>
					<tr>
						<td align="center"><?php echo $value['subject_code'] ?></td>
						<td><?php echo $value['subject_name'] ?></td>
						<td align="center">
							<?php if($_SESSION['acc_type'] == 'Administrator'){ ?>
							<a href="home.php?action=subjects&action_subject=edit&subject_id=<?php echo $value['subject_id'] ?>">Edit</a>
							<?php } ?>
							<a href="home.php?action=enroll_student&subject_id=<?php echo $value['subject_id'] ?>&subject_code=<?php echo $value['subject_code'] ?>">Enroll Student</a>
						</td>
					</tr>
					<?php } ?>
				</table>
			</div>
<!-- EDIT SUBJECT SECTION -->
			<?php }else{ ?>
				<?php if(!empty($message_subject)){ ?>
						<div id="success_message_home" style="margin: 0 auto;border-radius: 0px;text-indent: 10px;width: 36.5%;"><?php echo $message_subject; ?>
							<span style="float: right;margin-right: 10px;">
								<a href="home.php?action=subjects" style="text-decoration: none;color: black;">&times;</a>
							</span>
						</div>
				<?php } ?>
				<?php $result_subjects = get_subjects_edit($connect, $_GET['subject_id']); ?>
				<div id="form_edit_subject">
					<h3><img src="../img/edit.png" height="25" width="25"> Edit Subject</h3><hr>
					<form method="POST">
						<?php foreach($result_subjects as $value_subjects){ ?>
							<input type="hidden" name="subject_id" value="<?php echo $value_subjects['subject_id'] ?>">
							<input type="text" name="subject_code" value="<?php echo $value_subjects['subject_code'] ?>"><br>
							<input type="text" name="subject_name" value="<?php echo $value_subjects['subject_name'] ?>"><br><br><hr><br>
							<button name="save_subject">Save</button>
							<a href="?"><button name="cancel">Cancel</button></a>
						<?php } ?>
					</form>
				</div>
				
				
			<?php } ?>
<!-- ADD SUBJECTS SECTION -->
		<?php }elseif(!empty($_GET['action']) && $_GET['action'] == 'add_subject'){ ?>
			<br><br><br>
			<?php if(!empty($message)){ ?>
				<?php if($message == 'Subject has been added'){ ?>
					<div id="success_message_home" style="margin: 0 auto;width: 36.5%;border-radius: 0px;text-indent: 10px;"><?php echo $message; ?>
						<span style="float: right;margin-right: 10px;">
							<a href="?action=subjects" style="text-decoration: none;color: black;">&times;</a>
						</span>
					</div>
				<?php }else{ ?>
					<div id="error_message_home" style="margin: 0 auto;width: 36.5%;border-radius: 0px;text-indent: 10px;"><?php echo $message; ?>
						<span style="float: right;margin-right: 10px;">
							<a href="?action=subjects" style="text-decoration: none;color: black;">&times;</a>
						</span>
					</div>
				<?php } ?>
			<?php } ?>
			<div id="add_subject_form">
				<h3><img src="../img/add.png" height="25" width="25" style="float: left;margin-right: 5px;"> Add Subject <a href="?action=subjects"><span style="color: black; float: right;margin-right: 15px;font-size: 15pt;">&times;</span></a></h3><hr>
				<form method="POST">
					<input type="text" name="subject_code" placeholder="Subject Code" required>
					<input type="text" name="subject_name" placeholder="Subject Description" required><br><br>
					<button name="add_subject">Add</button>
				</form><br><hr>
			</div>
<!-- ENROLLMENT SECTION -->
		<?php }elseif(!empty($_GET['action']) && $_GET['action'] == 'enroll_student'){ ?>
			<br><br><br>
			<div id="container_enroll">
			<?php $students = get_students($connect); ?>
					<div id="enroll_student"><br>
						<span style="font-weight: bold;font-size: 18pt;">Enrollment for <code><u><?php echo $_GET['subject_code'] ?></u></code></span>
						<a href="?action=subjects"><img src="../img/close.png" height="31" width="31" style="float: right;margin-right: 20px;" title="Close"></a><br><br><hr>
						<table cellpadding="10" cellspacing="0" border="1" width="100%">
							<tr style="background: gray;">
								<th>Student Name</th>
								<th width="30%">Action</th>
							</tr>
							<?php foreach ($students as $value) { ?>
							<tr>
								<td><?php echo $value['acc_fname'].' '.$value['acc_mname'].' '.$value['acc_lname']; ?></td>
								<?php $result = check_student_enroll($connect, $value['acc_id'], $_GET['subject_id']); ?>
								<?php if($result){ ?>
								<td align="center">
									<span style="color: blue;">Enrolled <a href="?action=edit_enroll&acc_id=<?php echo $value['acc_id'] ?>&subject_id=<?php echo $_GET['subject_id'] ?>"><img src="../img/edit.png" height="10" width="10" title="Edit"></a></span>
								</td>
								<?php }else{ ?>
								<td align="center">
									<form method="POST">
										<input type="hidden" name="acc_id" value="<?php echo $value['acc_id'] ?>">
										<input type="hidden" name="subject_id" value="<?php echo $_GET['subject_id'] ?>">
										<button name="enroll"><span style="color: red;">Enroll</span></button>
									</form>
								</td>
								<?php } ?>
							</tr>
							<?php } ?>
						</table>
					</div>
<!-- EDIT ENROLLMENT SECTION -->
		<?php }elseif(!empty($_GET['action']) && $_GET['action'] == 'edit_enroll'){ ?>
			<?php $edit_enroll = get_enroll_edit($connect, $_GET['acc_id'], $_GET['subject_id']); ?><br><br><br><br>
			<div id="form_edit_enroll">
				<h3><img src="../img/edit.png" height="25" width="25"> Edit Enrolled Student <a href="?action=subjects"><img src="../img/close.png" height="20" width="20" style="float: right;margin-right: 10px;" title="Close"></a></h3>
				<hr><br>
					<?php foreach($edit_enroll as $value_edit_enroll){ ?>
					<table cellspacing="10">
						<tr>
							<td align="right"><b>Student Name:</b></td>
							<td><?php echo $value_edit_enroll['acc_fname'].' '.$value_edit_enroll['acc_mname'].' '.$value_edit_enroll['acc_lname'] ?></td>
						</tr>
						<tr>
							<td align="right"><b>Enrolled Subject: </b></td>
							<td><?php echo $value_edit_enroll['subject_code'] ?> (<?php echo $value_edit_enroll['subject_name'] ?>)</td>
						</tr>
					</table>
					<br><hr><br>
					<form method="POST">
						<input type="hidden" name="subject_id" value="<?php echo $value_edit_enroll['subject_id'] ?>">
						<input type="hidden" name="acc_id" value="<?php echo $value_edit_enroll['acc_id'] ?>">
						<button name="drop_student">Drop Student</button>
					</form>
					<?php } ?>
				
			</div>
<!-- ACCOUNTS SECTION -->
		<?php }elseif(!empty($_GET['action']) && $_GET['action'] == 'accounts'){ ?>
			<?php 
				if(isset($_POST['search_account_btn'])){
					if(empty($_POST['search_account'])){
						$accounts = get_accounts($connect); 
					}else{
						$accounts = get_accounts_search($connect, $_POST['search_account']);
					}
				}else{
					$accounts = get_accounts($connect); 
				}
				
			?>
			<br><br>
			<div id="container_accounts">
				<h2>
					<img src="../img/students.png" height="30" width="30" style="float: left;margin-right: 10px;">
					Accounts
					<a href="home.php"><img src="../img/close.png" height="31" width="31" style="float: right;" title="Close"></a>
				</h2>
				<hr>
				<a href="home.php?action=add_account" style="color: black;text-decoration: none;border: 1px solid black;padding: 5px 5px;">Add Account</a>

				<form method="POST" style="margin-top: -5px;margin-right: -20px;">
					<input type="text" name="search_account" placeholder="Search....">
					<button name="search_account_btn" style="float: right;margin-right: 20px;"><img src="../img/search.png" height="16" width="16"></button>
				</form>
				<br><hr><br>
				<table cellspacing="0" cellpadding="10" border="1" width="100%">
					<tr style="background: gray;">
						<th>Account Name</th>
						<th>Account Email</th>
						<th>Account Username</th>
						<th>Account Password</th>
						<th>Account Type</th>
						<th width="15%">Action</th>
					</tr>
					<?php if(empty($accounts)){ ?>
					<tr>
						<td colspan="6" align="center"><span style="color: red;">*** NO ACCOUNT FOUND ***</span></td>
					</tr>
					<?php }else{ ?>
					<?php foreach($accounts as $value_accounts){ ?>
					<tr>
						<td><?php echo $value_accounts['acc_fname'].' '.$value_accounts['acc_mname'].' '.$value_accounts['acc_lname'] ?></td>
						<td><?php echo $value_accounts['acc_email'] ?></td>
						<td><?php echo $value_accounts['acc_username'] ?></td>
						<td><?php echo $value_accounts['acc_password'] ?></td>
						<td><?php echo $value_accounts['acc_type'] ?></td>
						<td>
							<a href="?acc_id=<?php echo $value_accounts['acc_id'] ?>&action=edit_student" id="edit_student">Edit</a>
							<a href="?acc_id=<?php echo $value_accounts['acc_id'] ?>&action=delete_student" id="delete_student">Delete</a>
						</td>
					</tr>
					<?php } ?>
					<?php } ?>
				</table>
			</div>
<!-- ADD ACCOUNT SECTION -->
		<?php }elseif(!empty($_GET['action']) && $_GET['action'] == 'add_account'){ ?>
			<?php 
				if(isset($_POST['add_account'])){ 
					if($_POST['acc_password'] == $_POST['acc_password2']){
						$result = add_account($connect, $_POST['acc_fname'], $_POST['acc_mname'], $_POST['acc_lname'], $_POST['acc_email'], $_POST['acc_username'], $_POST['acc_password'], $_POST['acc_type']);
						if($result){
							$message_add_account = 'Account has been registered';
						}else{
							$message_add_account = 'Account already exist';
						}
					}else{
						$message_add_account = 'Password do not match.';
					}
					
				}
			?>
			<br><br><br><br>
				<?php if(!empty($message_add_account)){ ?>
					<?php if($message_add_account == 'Account has been registered'){ ?>
						<div id="success_message_home" style="width: 58%;text-indent: 10px;margin: 0 auto;border-radius: 0px;"><?php echo $message_add_account; ?>
							<span style="float: right;margin-right: 10px;">
								<a href="home.php?action=accounts" style="text-decoration: none;color: black;">&times;</a>
							</span>
						</div>
					<?php }else{ ?>
						<div id="error_message_home" style="width: 58%;text-indent: 10px;margin: 0 auto;border-radius: 0px;"><?php echo $message_add_account; ?>
							<span style="float: right;margin-right: 10px;">
								<a href="home.php?action=add_account" style="text-decoration: none;color: black;">&times;</a>
							</span>
						</div>
					<?php } ?>
				<?php } ?>
			<div id="form_add_account">
				<h2><img src="../img/add_user.png" height="31" width="31" style="float: left;margin-right: 10px;"> Add Account Form <a href="home.php?action=accounts"><img src="../img/close.png" height="31" width="31" style="float: right;" title="Close"></a></h2><hr><br>
				<form method="POST">
					<input type="text" name="acc_fname" placeholder="Firstname" required>
					<input type="text" name="acc_mname" placeholder="Middlename" required>
					<input type="text" name="acc_lname" placeholder="Lastname" required>
					<input type="email" name="acc_email" placeholder="Email Address" required>
					<span>
					<input type="text" name="acc_username" placeholder="Username" required>
					<input type="password" name="acc_password" placeholder="Password" required>
					<input type="password" name="acc_password2" placeholder="Confirm Password" required>
					<select name="acc_type">
						<option value="Administrator">Administrator</option>
						<option value="Instructor">Instructor</option>
						<option value="Student">Student</option>
					</select>
					</span><br><br><hr><br>
					<button name="add_account">Add Account</button>
				</form>
			</div>
<!-- MY ACCOUNT SECTION -->
		<?php }elseif(!empty($_GET['action']) && $_GET['action'] == 'settings'){ ?>
			<br><br>
			<div id="settings">
				<center><h1>&#8226; &#8226; <?php echo $_SESSION['acc_type'] ?> &#8226; &#8226;</h1></center>
				<hr>
				<table cellspacing="30" style="margin-left: 50px;">
					<tr>
						<td align="right"><b>Email:</b></td>
						<td><?php echo $_SESSION['acc_email'] ?> 
							<?php if($_SESSION['acc_type'] == 'Administrator'){ ?>
							<a href="home.php?acc_id=<?php echo $_SESSION['acc_id'] ?>&action=edit_student"><img src="../img/edit.png" height="12" width="12" title="Edit">
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td align="right"><b>Username:</b></td>
						<td><?php echo $_SESSION['acc_username'] ?> 
							<?php if($_SESSION['acc_type'] == 'Administrator'){ ?>
								<a href="home.php?acc_id=<?php echo $_SESSION['acc_id'] ?>&action=edit_student"><img src="../img/edit.png" height="12" width="12" title="Edit">
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td align="right"><b>Password:</b></td>
						<td>************ <a href="home.php?action=edit_account"><img src="../img/edit.png" height="12" width="12" title="Edit"></a></td>
					</tr>
				</table>
				<img src="../img/default-img.png" height="150" width="150" style="margin-top: -170px;float: right;margin-right: 80px;"><br><hr><br><br>
				<a href="home.php?logout=true" style="text-decoration: none;color: black;font-size: 18pt;"><span><img src="../img/sign-out.png" height="30" width="30" style="float: left;margin-right: 10px;margin-left: 100px;"> Logout</span></a>
				<a href="home.php?" style="text-decoration: none;color: black;font-size: 18pt;"><span style="float: right;margin-left: 10px;margin-right: 150px;line-height: 35px;"> Home <img src="../img/home.png" height="30" width="30" style="float: left;margin-right: 10px;"></span></a>
			</div>
<!-- CHANGE PASSWORD SECTION -->
		<?php }elseif(!empty($_GET['action']) && $_GET['action'] == 'edit_account'){ ?>

			<?php 
				if(isset($_POST['save_change_password'])){ 
					if($_POST['new_password'] == $_POST['confirm_new_password']){
						change_password($connect, $_SESSION['acc_id'], $_POST['new_password']);
						$message_settings = 'Change password was successful.';
						$_SESSION['acc_password'] = $_POST['new_password'];
					}else{
						$message_settings = 'New and Confirm New Password do not match';
					}
				}
			?>
			<br><br><br>
			<?php if(!empty($message_settings)){ ?>
				<?php if($message_settings == 'Change password was successful.'){ ?>
					<div id="success_message_home" style="margin: 0 auto;width: 37.9%;border-radius: 0px;text-indent: 10px;"><?php echo $message_settings; ?>
						<span style="float: right;margin-right: 10px;">
							<a href="?action=edit_account" style="text-decoration: none;color: black;">&times;</a>
						</span>
					</div>
				<?php }else{ ?>
					<div id="error_message_home" style="margin: 0 auto;width: 37.9%;border-radius: 0px;text-indent: 10px;"><?php echo $message_settings; ?>
						<span style="float: right;margin-right: 10px;">
							<a href="?action=edit_account" style="text-decoration: none;color: black;">&times;</a>
						</span>
					</div>
				<?php } ?>
			<?php } ?>
			<div id="change_password">
				<h3><img src="../img/padlock.png" height="30" width="30" style="float: left;margin-right: 5px;margin-top: -10px;">Change Password 
					<a href="home.php?action=settings">
						<img src="../img/close.png" height="25" width="25" style="float: right;margin-top: -10px;" title="Close">
					</a></h3><hr>
				<table cellspacing="10" width="100%">
					<tr>
						<td align="right"><b>Current Password:</b></td>
						<td><?php echo $_SESSION['acc_password'] ?></td>
					</tr>
					<tr>
						<td align="right"><b>New Password: </b></td>
						<td>
						<form method="POST">
							<input type="hidden" name="acc_id" value="<?php echo $_SESSION['acc_id'] ?>">
							<input type="hidden" name="acc_password" value="<?php echo $_SESSION['acc_password'] ?>">
							<input type="password" name="new_password" placeholder="New Password">
						</td>
					</tr>
					<tr>
						<td align="right"><b>Confirm New Password: </b></td>
						<td align="left">
							<input type="hidden" name="acc_id" value="<?php echo $_SESSION['acc_id'] ?>">
							<input type="hidden" name="acc_password" value="<?php echo $_SESSION['acc_password'] ?>">
							<input type="password" name="confirm_new_password" placeholder="Confirm New Password">
						</td>
					</tr>
				</table><hr>
						<button name="save_change_password">Save</button>
						</form>
			</div>
<!-- ATTENDANCE SECTION (STUDENT VIEW) SECTION -->
		<?php }elseif(!empty($_GET['action']) && $_GET['action'] == 'my_attendance'){ ?>
			<?php if(isset($_POST['search_subject'])){ ?>
				<?php if($_POST['subjects'] == 'none'){
					$message = 'Please select subject.';
					$attendance = get_my_attendance($connect);
				}else{ 
					
					$attendance = get_my_attendance_by_subject($connect, $_POST['subjects']);
					foreach($attendance as $value){
						$message_attendance_sheet = 'Attendance for the subject '.'<b>'.$value['subject_code'].' ('.$value['subject_name'].')</b>';
					}
				} ?>
			<?php }elseif(isset($_POST['search_date_attendance'])){ ?>
				<?php if($_POST['date_attendance'] == 'none'){ 
					$message = 'Please select date.';
					$attendance = get_my_attendance($connect);
				}else{ 
					 $attendance = get_my_attendance_by_date_attendance($connect, $_POST['date_attendance']); 
					 $message_attendance_sheet = 'Attendance for the date '.'<b>'.$_POST['date_attendance'].'</b>';
				 } ?>
			<?php }else{ ?>
				<?php $attendance = get_my_attendance($connect); ?>
				<?php $message_attendance_sheet = 'Attendance for all subjects and dates.'; ?>
			<?php } ?>
			
			<?php $subjects = get_subjects($connect); ?>
			<?php $attendance_sheet_date = get_attendance_sheet_date($connect); ?>
			<br><br>
			<?php if(!empty($message)){ ?>
				<div id="error_message_home" style="width: 61.5%;text-indent: 10px;margin: 0 auto;border-radius: 0px;"><?php echo $message; ?>
							<span style="float: right;margin-right: 10px;">
								<a href="home.php?action=my_attendance" style="text-decoration: none;color: black;">&times;</a>
							</span>
						</div>
			<?php } ?>
			<div id="attendance_sheet">
				<h1><img src="../img/attendance.png" height="30" width="30" style="float: left;margin-right: 10px;"> Attendance Sheet <a href="home.php"><img src="../img/close.png" height="30" width="30" style="float: right;" title="Close"></a></h1><hr>
				<form method="POST" style="float: left;margin-top: -7px;">
					<select name="subjects">
						<option value="none">Search by subjects</option>
						<?php foreach($subjects as $value_subjects){ ?>
						<option value="<?php echo $value_subjects['subject_id'] ?>"><?php echo $value_subjects['subject_code'] ?></option>
						<?php } ?>
					</select>
					<button name="search_subject" style="cursor: pointer;float: right;margin-top: 10px;"><img src="../img/search.png" height="15" width="15"></button>
				</form>
				<form method="POST" style="float: right;margin-top: -7px;">
					<select name="date_attendance">
						<option value="none">Search by date</option>
						<?php foreach($attendance_sheet_date as $value_subjects){ ?>
						<option value="<?php echo $value_subjects['date_attendance'] ?>"><?php echo $value_subjects['date_attendance'] ?></option>
						<?php } ?>
					</select>
					<button name="search_date_attendance" style="cursor: pointer;float: right;margin-top: 10px;"><img src="../img/search.png" height="15" width="15"></button>
				</form>
				<br><br><hr>
				<?php if(!empty($message_attendance_sheet)){ ?>
					<span style="color: red;font-size: 10pt;">* <?php echo $message_attendance_sheet; ?></span>
				<?php } ?>
				<br><br>
				<table cellspacing="0" cellpadding="10" border="1" width="100%">
					<tr style="background: gray;">
						<th>Student Name</th>
						<th width="35%">Remarks</th>
					</tr>
					<?php if(empty($attendance)){ ?>
						<tr>
							<td colspan="2" align="center"><span style="color: red;font-size: 12pt">*** YOUR INSTRUCTOR DIDN'T UPDATED YOUR REMARKS YET ***</span></td>
						</tr>
					<?php }else{ ?>
						<?php foreach($attendance as $value_attendance){ ?>
							<tr>
								<td><?php echo $value_attendance['acc_fname'].' '.$value_attendance['acc_mname'].' '.$value_attendance['acc_lname'] ?></td>
								<td align="center">
									<?php if($value_attendance['remarks'] == 'Present'){$color = 'blue';}else{$color='red';} ?>
									<span style="color: <?php echo $color; ?>"><?php echo $value_attendance['remarks'] ?> as of <?php echo $value_attendance['date_attendance'] ?></span>
								</td>
							</tr>
						<?php } ?>
					<?php } ?>
				</table>
			</div>
		<?php } ?>
</body>
</html>
	<script type="text/javascript" src="../js/js_home.js"></script>
<?php 
	}else{
		header('Location: ../index.php');
	} 
?>