<?php 

require_once('operations/sessions.php');
require_once('operations/DBconfig.php');

$response = null;

$sql  = 'SELECT d.first_name, d.last_name, d.mobile, d.whatsapp, dn.don_amount, dn.don_period, dn.payment_mode, dn.transaction_id, dn.on_date, dn.received_by, d.area, d.tehsil, d.district, d.state, d.country FROM donors d INNER JOIN `donation` dn ON d.mobile=dn.don_id LIMIT 0,1000';
if($query = mysqli_query($conn,$sql)) {
	$data=ucwords("First Name"."\t"."Last Name"."\t"."Mobile"."\t"."Whatsapp"."\t"."Donation(Rs.)"."\t"."Period"."\t"."Payment Mode"."\t"."Transaction Id"."\t"."Date"."\t"."Received By"."\t"."Colony"."\t"."Tehsil"."\t"."District"."\t"."State"."\t"."Country"."\n");

	while ($row=mysqli_fetch_assoc($query)) {
		$line = null;
		foreach ($row as $value) {
			$line .= '"' . $value . '"' . "\t";
		}
		$data .= trim($line)."\n";
	}

	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=donors_report.xls");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo $data;
	// echo '<script>window.setTimeout(function(){ window.location.href = "overview.php"; } , 2000);</script>';
} else {
	echo '<b style="text-align:center;">Error : '.mysqli_error($conn).'</b>';
}


?>