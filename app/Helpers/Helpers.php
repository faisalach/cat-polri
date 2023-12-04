<?php 

function global_settings($key)
{
	$settings 	= [
		"DIR_UPLOAD" 	=> "uploads",
		"PUBLIC_ROOT"	=> $_SERVER['DOCUMENT_ROOT']."/cat/public_html",
	];

	return !empty($settings[$key]) ? $settings[$key] : "";
}

function choice_option($course_id)
{
	$course 	= \DB::table("courses")
	->where('courses.id',$course_id)
	->first();

	$length 		= !empty($course->number_of_choice) ? $course->number_of_choice : 5;
	$option_arr 	= [
		"a" => "A",
		"b" => "B",
		"c" => "C",
		"d" => "D",
		"e" => "E",
	];
	$return 	= [];
	$count 		= 0;
	foreach($option_arr as $key => $val){
		if ($count < $length) {
			$return[$key] = $val;
		}
		$count++;
	}
	return $return;
}

function get_course_sub_categpry($course_category_id)
{
	if ($course_category_id != 1 && $course_category_id != 2) {
		return [];
	}
	$sub_categpries 	= \DB::table("course_sub_category")->where('course_category_id',$course_category_id)->get();
	return empty($sub_categpries[0]) ? [] : $sub_categpries;
}

function count_course($course_category_id)
{
	switch($course_category_id){
		case '1':
			return 0;
			break;
		case '2':
			return 0;
			break;
		case '3':
			return [10,50];
			break;
		default:
			return 0;
			break;
	}
}
function time_course($course_category_id)
{
	switch($course_category_id){
		case '1':
			return 0;
			break;
		case '2':
			return 0;
			break;
		case '3':
			return 1;
			break;
		default:
			return 0;
			break;
	}
}

function validate_upload_file($file_type,$filename,$filesize){
	$validation_error 	= false;
	$status 			= "";
	$message 			= "";

	switch($file_type){
		case 'image' :
			$max_filesize 		= 2 * 1024 * 1024;
			$allow_extension 	= ["jpeg","jpg","bmp","png"];
			break;
		default : 
			$max_filesize 		= 1 * 1024 * 1024;
			$allow_extension 	= ["jpeg","jpg","bmp","png","pdf","doc","docx","xls","xlsx"];
			break;
	}

	$pathinfo 	= pathinfo($filename);
	$extension 	= !empty($pathinfo["extension"]) ? $pathinfo["extension"] : "";

	if (!$validation_error) {
		if (!in_array($extension,$allow_extension)) {
			$status 			= "error";
			$message 			= "File wajib ber ekstensi ".implode(',',$allow_extension);
			$validation_error 	= true;
		}
	}

	if (!$validation_error) {
		if ($filesize > $max_filesize) {
			$status 			= "error";
			$message 			= "Ukuran file maksimal adalah " . convert_filesize($max_filesize);
			$validation_error 	= true;
		}
	}

	return [
		"error"		=> $validation_error,
		"status" 	=> $status,
		"message" 	=> $message,
	];
}

function convert_filesize($max_filesize)
{
	if ($max_filesize < 1024) {
		return $max_filesize . "B";
	}elseif ($max_filesize < (1024 * 1024)) {
		return ($max_filesize / 1024) . "KB";
	}elseif ($max_filesize < (1024 * 1024 * 1024)) {
		return ($max_filesize / 1024 / 1024) . "GB";
	}
}

function count_score($numerator,$denominator)
{
	if ($denominator == 0) {
		return 0;
	}
	return ($numerator / $denominator ) * 100;
}
function linear_regression( $x, $y, $type = 'slope' ) {

    $n     = count($x);     // number of items in the array
    $x_sum = array_sum($x); // sum of all X values
    $y_sum = array_sum($y); // sum of all Y values

    $xx_sum = 0;
    $xy_sum = 0;

    for($i = 0; $i < $n; $i++) {
    	$xy_sum += ( $x[$i]*$y[$i] );
    	$xx_sum += ( $x[$i]*$x[$i] );
    }

    // Slope
    $slope = ( ( $n * $xy_sum ) - ( $x_sum * $y_sum ) ) / ( ( $n * $xx_sum ) - ( $x_sum * $x_sum ) );

    // calculate intercept
    $intercept = ( $y_sum - ( $slope * $x_sum ) ) / $n;

    if ($type == 'slope') {
	    $absolute 	= 1 - $slope;
		if ($absolute < 0) {
			$absolute = $absolute * -1;
		}
		return 100 - ($absolute * 40);
    }
    return array( 
    	'slope'     => $slope,
    	'intercept' => $intercept,
    );
}
function score_convert($type, $score)
{
	if ($type == "alphabet") {
		if ($score <= 20) {
			return "KS";
		}elseif ($score > 20 && $score <= 40) {
			return "K";
		}elseif ($score > 40 && $score <= 60) {
			return "C";
		}elseif ($score > 60 && $score <= 80) {
			return "CB";
		}elseif ($score > 80) {
			return "B";
		}
	}elseif($type == "numeric"){
		if ($score == "KS") {
			return -70;
		}elseif ($score == "S") {
			return 1;
		}elseif ($score == "C") {
			return 2;
		}elseif ($score == "CB") {
			return 3;
		}elseif ($score == "B") {
			return 4;
		}
	}elseif($type == "final_score"){
		if ($score < 61) {
			return "TMS";
		}elseif ($score >= 61) {
			return "MS";
		}
	}
}
function count_score_final($course_category_id,$course_sub_category_id,$score)
{
	// Kecerdasan
	if ($course_category_id == 1 && $course_sub_category_id == 1) {
		// Verbal
		return $score * 1.25;
	}elseif ($course_category_id == 1 && $course_sub_category_id == 2) {
		// Praktis
		return $score * 2.5;
	}elseif ($course_category_id == 1 && $course_sub_category_id == 3) {
		// Logis
		return $score * 1.25;
	}
	// Kepribadian
	elseif ($course_category_id == 2 && $course_sub_category_id == 4) {
		// Stabilitas Emosi
		return $score * 2.5;
	}elseif ($course_category_id == 2 && $course_sub_category_id == 5) {
		// Pro Sosial
		return $score * 2.5;
	}elseif ($course_category_id == 2 && $course_sub_category_id == 6) {
		// Kepercayaan Diri
		return $score * 1.25;
	}elseif ($course_category_id == 2 && $course_sub_category_id == 7) {
		// Penyesuaian Diri
		return $score * 1.25;
	}elseif ($course_category_id == 2 && $course_sub_category_id == 8) {
		// Motivasi Berprestasi
		return $score * 1.25;
	}elseif ($course_category_id == 2 && $course_sub_category_id == 9) {
		// Pengambilan Keputusan
		return $score * 1.25;
	}elseif ($course_category_id == 2 && $course_sub_category_id == 10) {
		// Loyalitas
		return $score * 1.25;
	}elseif ($course_category_id == 2 && $course_sub_category_id == 11) {
		// Kerjasama
		return $score * 2.5;
	}
	// Kecermatan
	elseif ($course_category_id == 3 && $course_sub_category_id == 12) {
		// Kecepatan
		return $score * 2.5;
	}elseif ($course_category_id == 3 && $course_sub_category_id == 13) {
		// Ketelitian
		return $score * 1.25;
	}elseif ($course_category_id == 3 && $course_sub_category_id == 14) {
		// Ketahanan
		return $score * 2.5;
	}
	// Akademik
	else{
		switch($course_category_id){
			case 4:
				// Pengetahuan Umum
				return $score * 0.3;
				break;
			case 5:
				// Wawasan Kebangsaan
				return $score * 0.2;
				break;
			case 6:
				// Matematika
				return $score * 0.2;
				break;
			case 7:
				// Bahasa Indonesia
				return $score * 0.3;
				break;
			case 8:
				// Bahasa Inggris
				return $score * 0.3;
				break;
		}
	}

	return false;
}
function restructure_question_for_columns($questions)
{
	$new_questions 	= [];
	foreach($questions as $questions_obj){
		$question_arr 			= json_decode($questions_obj->question) ? json_decode($questions_obj->question) : [];
		$questions_obj->question_arr = $question_arr;

		$number 	= $questions_obj->number - 1;
		$column 	= floor($number / count_course(3)[1]);
		$row 		= $number - ($column * count_course(3)[1]) ;
		$new_questions[$column][$row] 	= $questions_obj;
	}
	return $new_questions;
}
function isAnswered($course_id,$user_id,$course_package_id)
{
	$course_classes = DB::table('course_classes')
	->where('course_id',$course_id)
	->first();
	// cek apa course ini hanya untuk kelas tertentu
	if (!empty($course_classes)) {
		// cek apa user ini masuk dalam kelas tersebut
		$course_classes = DB::table('course_classes')
		->join("users","users.class_id","=","course_classes.class_id")
		->where('course_id',$course_id)
		->where('users.id',$user_id)
		->first();
		if (empty($course_classes)) {
			return "skip";
		}
	}
	$check 	= DB::table('scores')->where('course_package_id',$course_package_id)->where('course_id',$course_id)->where('user_id',$user_id)->first();
	return !empty($check->id) ? $check->id : 0;
}
function isFinishTest($course_type_id,$user_id,$course_package_id)
{
	$courses 	= DB::table('courses')->select('courses.id')
	->join('course_to_packages','course_to_packages.course_id','=','courses.id')
	->where('course_to_packages.course_package_id',$course_package_id)
	->where('course_to_packages.course_type_id',$course_type_id)
	->get();
	foreach($courses as $course ){
		if ($course_type_id == 1) {
			if (!isAnswered($course->id,$user_id,$course_package_id)) {
				return false;
			}
		}else{
			if ($isAnswered = isAnswered($course->id,$user_id,$course_package_id)) {	
				if ($isAnswered != "skip") {
					return true;
				}
			}
		}
	}
	if ($course_type_id == 1) {
		return true;
	}else{
		return false;
	}
}
function update_final_score($course_type_id,$user_id,$course_package_id)
{
	if (isFinishTest($course_type_id,$user_id,$course_package_id)) {
		$answer_model 	= new \App\Models\AnswerModel();
		$score 	= $answer_model->getScores($user_id,$course_type_id,$course_package_id);
		$final_score 	= $score["final_score"];
		if ($course_type_id == 1) {
            DB::table("users")->where('id',$user_id)->update(["final_score_psi" => $final_score]);
        }else{
            DB::table("users")->where('id',$user_id)->update(["final_score_aka" => $final_score]);
        }
	}
}
function get_base64_file($base64_string)
{
	$explode 	= explode("base64,",$base64_string);
	return !empty($explode[1]) ? $explode[1] : '';
}
function getSizeFile($base64_string)
{
	$length = strlen(get_base64_file($base64_string));
	return $length;
}