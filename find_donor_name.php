<?php 
// require_once('operations/sessions.php');
require_once('operations/DBconfig.php');
require_once('operations/validations.php');

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 120");

$data = array();
$data['success']=false;
$data['response']="null";

$input = json_decode(file_get_contents("php://input"));

if(!empty($input->mobile) and validateMobileNumber($input->mobile)) {
	
	$sql = "SELECT first_name, last_name FROM `donors` WHERE mobile=".$input->mobile;
	$query=mysqli_query($conn,$sql);
	if($query) {
		$num_rows=mysqli_num_rows($query);
		if($num_rows==1) {
			$result = mysqli_fetch_assoc($query);
			$data['success']=true;
			$data['response']=$result['first_name'].' '.$result['last_name'];
		} else {
			$data['response']="Not Found";
		}
	} else {
			$data['response']=mysqli_error($conn);
	}
} 

echo json_encode($data);
?>