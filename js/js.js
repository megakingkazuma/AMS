var menu = document.getElementById('menu');
var form = document.getElementById('login_form');
var close_login_form = document.getElementById('close_login_form');
var ams = document.getElementById('ams');
var student_record_btn = document.getElementById('student_record');
var container1 = document.getElementById('container1');
var container_student_record = document.getElementById('container_student_record');

menu.addEventListener('click', show_form);
close_login_form.addEventListener('click', close_form);
student_record_btn.addEventListener('click', show_container_student_record);

function show_form(){
	form.style.display='block';
	ams.style.display='none';
}
function close_form(){
	form.style.display='none';
	ams.style.display='block';
}
function show_container_student_record(){
	container1.style.display='none';
	container_student_record.style.display='block';
}