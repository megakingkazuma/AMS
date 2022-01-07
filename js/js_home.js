var student_record_btn = document.getElementById('student_record');
var container1 = document.getElementById('container1');
var container_student_record = document.getElementById('container_student_record');
var close_container_student_record = document.getElementById('close_container_student_record');
var add_student = document.getElementById('add_student');
var add_student_form = document.getElementById('add_student_form');
var close_add_student_form = document.getElementById('close_add_student_form');
var close_edit_student_form = document.getElementById('close_edit_student_form');
var edit_student_form = document.getElementById('edit_student_form');

student_record_btn.addEventListener('click', show_container_student_record);
close_container_student_record.addEventListener('click', close_student_record);
add_student.addEventListener('click', add_student_btn);
close_add_student_form.addEventListener('click', close_student_form_add);
close_edit_student_form.addEventListener('click', close_student_form_edit);

function show_container_student_record(){
	container1.style.display='none';
	container_student_record.style.display='block';
}
function close_student_record(){
	container1.style.display='block';
	container_student_record.style.display='none';
}
function add_student_btn(){
	container1.style.display='none';
	container_student_record.style.display='none';
	add_student_form.style.display='block';
}
function close_student_form_add(){
	container1.style.display='none';
	container_student_record.style.display='block';
	add_student_form.style.display='none';
}
function close_student_form_edit(){
	container1.style.display='none';
	container_student_record.style.display='block';
	add_student_form.style.display='none';
	edit_student_form.style.display='none';
}