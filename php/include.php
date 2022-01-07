<?php  
	session_start();
	include_once 'connection.php';

	function login($connect, $username, $password){
			$statement_login = $connect->prepare('SELECT * FROM accounts WHERE acc_username = :username AND acc_password = :password');
			$statement_login->execute([':username'=>$username, ':password'=>$password]);
			if ($statement_login->rowCount() > 0) {
				while ($row = $statement_login->fetch(PDO:: FETCH_ASSOC)) {
					if (!empty($row)) {
						$_SESSION['acc_id'] = $row['acc_id'];
						$_SESSION['acc_fname'] = $row['acc_fname'];
						$_SESSION['acc_mname'] = $row['acc_mname'];
						$_SESSION['acc_lname'] = $row['acc_lname'];
						$_SESSION['acc_email'] = $row['acc_email'];
						$_SESSION['acc_username'] = $row['acc_username'];
						$_SESSION['acc_password'] = $row['acc_password'];
						$_SESSION['acc_type'] = $row['acc_type'];
						$_SESSION['logged_in'] = true;
						return true;
					}else{
						return false;
					}
				}
			}
	}
	function logout(){
		session_unset();
		session_destroy();
	}
	function add_student($connect, $fname, $mname, $lname, $email, $username, $password, $type){
		$statement_add_student=$connect->prepare('INSERT INTO accounts VALUES(NULL, :fname, :mname, :lname, :email, :username, :password, :type);');
        $statement_add_student->execute([':fname'=>$fname, ':mname'=>$mname, ':lname'=>$lname, ':email'=>$email, ':username'=>$username, ':password'=>$password, ':type'=>$type]);
        if($statement_add_student){
        	return true;
        }else{
        	return false;
        }
	}
	function get_students($connect){
		$result = [];

	    $statement = $connect->query('SELECT * FROM accounts WHERE acc_type = "Student"');
	    foreach ($statement as $student) {
	        $result[] = $student;
	    }
	    return $result;
	}
	function get_students_search_student_attendance($connect, $search){
		$result = [];

	    $statement = $connect->prepare('SELECT * FROM accounts WHERE acc_type = "Student" AND acc_fname LIKE :search OR acc_mname LIKE :search OR acc_lname LIKE :search OR acc_username LIKE :search OR acc_email LIKE :search');
	    $statement->execute([':search'=>$search]);
	    foreach ($statement as $student) {
	        $result[] = $student;
	    }
	    return $result;
	}
	function get_subjects($connect){
		$result = [];

	    $statement = $connect->query('SELECT * FROM subjects');
	    foreach ($statement as $subjects) {
	        $result[] = $subjects;
	    }
	    return $result;
	}
	function get_subjects_search($connect, $search){
		$result = [];

	    $statement = $connect->prepare('SELECT * FROM subjects WHERE subject_code LIKE :search OR subject_name LIKE :search');
	    $statement->execute([':search'=>$search]);
	    foreach ($statement as $subjects) {
	        $result[] = $subjects;
	    }
	    return $result;
	}
	function get_students_subject($connect, $subject_id){
		$result = [];

	    $statement = $connect->prepare('SELECT * FROM acc_subject NATURAL JOIN accounts WHERE acc_subject.subject_id = :subject_id AND accounts.acc_id = acc_subject.acc_id');
	    $statement->execute([':subject_id'=>$subject_id]);
	    foreach ($statement as $accounts) {
	        $result[] = $accounts;
	    }
	    return $result;
	}
	function get_students_edit($connect, $acc_id){
		$result = [];

	    $statement = $connect->prepare('SELECT * FROM accounts WHERE acc_id = :acc_id');
	    $statement->execute([':acc_id'=>$acc_id]);
	    foreach ($statement as $student) {
	        $result[] = $student;
	    }
	    return $result;
	}
	function edit_student($connect, $id, $fname, $mname, $lname, $email, $username, $password, $type){
		$statement = $connect->prepare('UPDATE accounts SET acc_fname = :fname, acc_mname = :mname, acc_lname = :lname, acc_email = :email, acc_username = :username, acc_password = :password, acc_type = :type WHERE acc_id = :id');
		$statement->execute([':id'=>$id, ':fname'=>$fname, ':mname'=>$mname, ':lname'=>$lname, ':email'=>$email, ':username'=>$username, ':password'=>$password, ':type'=>$type]);
	}	
	function delete_student($connect, $id){
	    $statement = $connect->prepare('DELETE FROM accounts WHERE acc_id = :id');
	    $statement->execute([':id'=>$id]);	
	}
	function add_subject($connect, $subject_code, $subject_name){
		$statement = $connect->prepare('SELECT * FROM subjects WHERE subject_code = :subject_code OR subject_name = :subject_name');
	    $statement->execute([':subject_code'=>$subject_code, ':subject_name'=>$subject_name]);
	    if ($statement->rowCount() > 0) {
	    	return false;
	    }else{
	    	$statement_add_subject=$connect->prepare('INSERT INTO subjects VALUES(NULL, :subject_code, :subject_name);');
        	$statement_add_subject->execute([':subject_code'=>$subject_code, ':subject_name'=>$subject_name]);
        	return true;
	    }
		
	}
	function enroll_student($connect, $acc_id, $subject_id){
		$statement=$connect->prepare('INSERT INTO acc_subject VALUES(:acc_id, :subject_id);');
        $statement->execute([':acc_id'=>$acc_id, ':subject_id'=>$subject_id]);
	}
	function check_student_enroll($connect, $acc_id, $subject_id){
		$statement = $connect->prepare('SELECT * FROM acc_subject WHERE acc_id = :acc_id AND subject_id = :subject_id');
	    $statement->execute([':acc_id'=>$acc_id, ':subject_id'=>$subject_id]);
	    if ($statement->rowCount() > 0) {
	    	return true;
	    }else{
	    	return false;
	    }
	}
	function get_enroll_edit($connect, $acc_id, $subject_id){
		$result = [];

	    $statement = $connect->prepare('SELECT * FROM acc_subject NATURAL JOIN accounts NATURAL JOIN subjects WHERE acc_subject.acc_id = :acc_id AND acc_subject.subject_id = :subject_id AND accounts.acc_id = acc_subject.acc_id AND subjects.subject_id = acc_subject.subject_id');
	    $statement->execute([':acc_id'=>$acc_id, ':subject_id'=>$subject_id]);
	    foreach ($statement as $enroll) {
	        $result[] = $enroll;
	    }
	    return $result;
	}
	function drop_student($connect, $acc_id, $subject_id){
		$statement=$connect->prepare('DELETE FROM acc_subject WHERE acc_id = :acc_id AND subject_id = :subject_id;');
        $statement->execute([':subject_id'=>$subject_id, ':acc_id'=>$acc_id]);
	}
	function attendance_present($connect, $subject_id, $acc_id, $attendance_date){
		$statement=$connect->prepare('INSERT INTO attendance VALUES(:subject_id, :acc_id, "Present", :attendance_date);');
        $statement->execute([':subject_id'=>$subject_id, ':acc_id'=>$acc_id, ':attendance_date'=>$attendance_date]);
	}
	function attendance_absent($connect, $subject_id, $acc_id, $attendance_date){
		$statement=$connect->prepare('INSERT INTO attendance VALUES(:subject_id, :acc_id, "Absent", :attendance_date);');
        $statement->execute([':subject_id'=>$subject_id, ':acc_id'=>$acc_id, ':attendance_date'=>$attendance_date]);
	}
	function edit_attendance_present($connect, $subject_id, $acc_id, $attendance_date){
		$statement=$connect->prepare('UPDATE attendance SET remarks = "Present" WHERE subject_id = :subject_id AND acc_id = :acc_id AND date_attendance = :attendance_date');
		$statement->execute([':subject_id'=>$subject_id, ':acc_id'=>$acc_id, ':attendance_date'=>$attendance_date]);
	}
	function edit_attendance_absent($connect, $subject_id, $acc_id, $attendance_date){
		$statement=$connect->prepare('UPDATE attendance SET remarks = "Absent" WHERE subject_id = :subject_id AND acc_id = :acc_id AND date_attendance = :attendance_date');
		$statement->execute([':subject_id'=>$subject_id, ':acc_id'=>$acc_id, ':attendance_date'=>$attendance_date]);
	}
	function clear_remarks($connect, $subject_id, $acc_id, $attendance_date){
		$statement=$connect->prepare('DELETE FROM attendance WHERE subject_id = :subject_id AND acc_id = :acc_id AND date_attendance = :attendance_date');
		$statement->execute([':subject_id'=>$subject_id, ':acc_id'=>$acc_id, ':attendance_date'=>$attendance_date]);
	}
	function check_attendance($connect, $acc_id, $subject){
		$result = [];
		$date_attendance = date('M d, Y');
	    $statement = $connect->prepare('SELECT * FROM attendance WHERE acc_id = :acc_id AND subject_id = :subject AND date_attendance = :date_attendance');
	    $statement->execute([':acc_id'=>$acc_id, ':subject'=>$subject, ':date_attendance'=>$date_attendance]);
	    foreach ($statement as $student) {
	        $result[] = $student;
	    }
	    return $result;
	}
	function get_attendance($connect){
		$result = [];

	    $statement = $connect->query('SELECT * FROM attendance NATURAL JOIN subjects NATURAL JOIN accounts WHERE accounts.acc_id = attendance.acc_id AND subjects.subject_id = attendance.subject_id');
	    foreach ($statement as $subject) {
	        $result[] = $subject;
	    }
	    return $result;
	}
	function get_my_attendance($connect){
		$result = [];
	    $statement = $connect->prepare('SELECT * FROM attendance NATURAL JOIN subjects NATURAL JOIN accounts WHERE accounts.acc_id = attendance.acc_id AND subjects.subject_id = attendance.subject_id AND attendance.acc_id = :id');
	    $statement->execute([':id'=>$_SESSION['acc_id']]);
	    foreach ($statement as $subject) {
	        $result[] = $subject;
	    }
	    return $result;
	}
	function get_attendance_by_subject($connect, $subject_id){
		$result = [];

	    $statement = $connect->prepare('SELECT * FROM attendance NATURAL JOIN accounts NATURAL JOIN subjects WHERE attendance.acc_id = accounts.acc_id AND attendance.subject_id = subjects.subject_id AND attendance.acc_id = accounts.acc_id AND  subject_id LIKE :subject_id');
	    $statement->execute([':subject_id'=>$subject_id]);
	    foreach ($statement as $subject) {
	        $result[] = $subject;
	    }
	    return $result;
	}
	function get_my_attendance_by_subject($connect, $subject_id){
		$result = [];

	    $statement = $connect->prepare('SELECT * FROM attendance NATURAL JOIN accounts NATURAL JOIN subjects WHERE attendance.acc_id = accounts.acc_id AND attendance.subject_id = subjects.subject_id AND attendance.acc_id = accounts.acc_id AND  subject_id LIKE :subject_id AND attendance.acc_id = :id');
	    $statement->execute([':subject_id'=>$subject_id, ':id'=>$_SESSION['acc_id']]);
	    foreach ($statement as $subject) {
	        $result[] = $subject;
	    }
	    return $result;
	}
	function get_attendance_by_date_attendance($connect, $date_attendance){
		$result = [];

	    $statement = $connect->prepare('SELECT * FROM attendance NATURAL JOIN accounts NATURAL JOIN subjects WHERE attendance.acc_id = accounts.acc_id AND attendance.subject_id = subjects.subject_id AND date_attendance LIKE :date_attendance');
	    $statement->execute([':date_attendance'=>$date_attendance]);
	    foreach ($statement as $date_attendance) {
	        $result[] = $date_attendance;
	    }
	    return $result;
	}
	function get_my_attendance_by_date_attendance($connect, $date_attendance){
		$result = [];

	    $statement = $connect->prepare('SELECT * FROM attendance NATURAL JOIN accounts NATURAL JOIN subjects WHERE attendance.acc_id = accounts.acc_id AND attendance.subject_id = subjects.subject_id AND date_attendance LIKE :date_attendance AND attendance.acc_id = :id');
	    $statement->execute([':date_attendance'=>$date_attendance, ':id'=>$_SESSION['acc_id']]);
	    foreach ($statement as $date_attendance) {
	        $result[] = $date_attendance;
	    }
	    return $result;
	}
	function get_attendance_sheet_date($connect){
		$result = [];

	    $statement = $connect->query('SELECT DISTINCT(attendance.date_attendance) FROM attendance;');
	    foreach ($statement as $subject) {
	        $result[] = $subject;
	    }
	    return $result;
	}
	function get_subjects_edit($connect, $subject_id){
		$result = [];

	    $statement = $connect->prepare('SELECT * FROM subjects WHERE subject_id = :subject_id');
	    $statement->execute([':subject_id'=>$subject_id]);
	    foreach ($statement as $subject) {
	        $result[] = $subject;
	    }
	    return $result;
	}
	function save_subject($connect, $subject_id, $subject_code, $subject_name){
		$statement = $connect->prepare('UPDATE subjects SET subject_code = :subject_code, subject_name = :subject_name WHERE subject_id = :subject_id');
		$statement->execute([':subject_id'=>$subject_id, ':subject_code'=>$subject_code, ':subject_name'=>$subject_name]);
	}
	function get_attendance_edit($connect, $subject_id, $acc_id, $date_attendance){
		$result = [];

	    $statement = $connect->prepare('SELECT * FROM attendance NATURAL JOIN accounts NATURAL JOIN subjects WHERE accounts.acc_id = attendance.acc_id AND subjects.subject_id = attendance.subject_id AND attendance.subject_id = :subject_id AND attendance.acc_id = :acc_id AND attendance.date_attendance = :date_attendance');
	    $statement->execute([':subject_id'=>$subject_id, ':acc_id'=>$acc_id, ':date_attendance'=>$date_attendance]);
	    foreach ($statement as $attendance) {
	        $result[] = $attendance;
	    }
	    return $result;
	}
	function get_accounts($connect){
		$result = [];
		$acc_id = $_SESSION['acc_id'];
	    $statement = $connect->prepare('SELECT * FROM accounts WHERE acc_id != :acc_id');
	    $statement->execute([':acc_id'=>$acc_id]);
	    foreach ($statement as $accounts) {
	        $result[] = $accounts;
	    }
	    return $result;
	}
	function get_accounts_search($connect, $search_account){
		$result = [];

	    $statement = $connect->prepare('SELECT * FROM accounts WHERE acc_fname LIKE :search_account OR acc_mname LIKE :search_account OR acc_lname LIKE :search_account OR acc_email LIKE :search_account OR acc_username LIKE :search_account OR acc_type LIKE :search_account;');
	    $statement->execute([':search_account'=>$search_account]);
	    foreach ($statement as $accounts) {
	        $result[] = $accounts;
	    }
	    return $result;
	} 
	function add_account($connect, $acc_fname, $acc_mname, $acc_lname, $acc_email, $acc_username, $acc_password, $acc_type){
		$statement1 = $connect->prepare('SELECT * FROM accounts WHERE acc_fname = :acc_fname OR acc_email = :acc_email OR acc_username = :acc_username OR acc_password = :acc_password');
	    $statement1->execute([':acc_fname'=>$acc_fname, ':acc_email'=>$acc_email, ':acc_username'=>$acc_username, ':acc_password'=>$acc_password]);
	    if ($statement1->rowCount() > 0) {
	    	return false;
	    }else{
	    	$statement2=$connect->prepare('INSERT INTO accounts VALUES(NULL, :acc_fname, :acc_mname, :acc_lname, :acc_email, :acc_username, :acc_password, :acc_type);');
        	$statement2->execute([':acc_fname'=>$acc_fname, ':acc_mname'=>$acc_mname, ':acc_lname'=>$acc_lname, ':acc_email'=>$acc_email, ':acc_username'=>$acc_username, ':acc_password'=>$acc_password, ':acc_type'=>$acc_type]);
        	return true;
	    }
	}
	function change_password($connect, $acc_id, $new_password){
		$statement=$connect->prepare('UPDATE accounts SET acc_password = :new_password WHERE acc_id = :acc_id;');
        $statement->execute([':new_password'=>$new_password, ':acc_id'=>$acc_id]);
	}
?>