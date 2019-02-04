<?php
function sendThanks($data) {
	$subject = "Expressing gratitude for your donation";  
    $message = "<html>
                <head>
                <title>OTP Code </title>
                </head>
                <body style='padding:10px;color:#000 !important;'>
                <div style='text-align:center;width:100%;height:130px;background:#fff;margin-bottom:20px;border-bottom:1px solid #eee'>
				   <img style='display:block;margin:0 auto;width:145px;height:87px;' src='".$_SERVER['HTTP_HOST']."/WRI/images/logo2.png'>
				  <b style='font-size:16px;''>WE ARE INDIANS NGO</b>
				</div>
                To: ".$data['email']."<br>
				From : yogeshprabhuindians@gmail.com<br>
				Date : ".date('d-m-Y')."<br>
				Sub : Expressing gratitude for your donation<br><br>

				Dear ".$data['name'].",<br>
				I am sincerely thankful to you on behalf of <b>WE ARE INDIANS NGO</b> for donating us ".$data['amount']." Rs. to bring a change in the lives of people who needs help. Your contribution in this regard is highly appreciated.<br>
				With the help of people like you, <b>WE ARE INDIANS NGO</b> TEAM has been able to provide the
				better living conditions for people and good education and training to many children.<br><br>
				
				If you have any suggestions also for improvement, please let us know. We will definitely implement the same.<br><br>

				I, once again, would like to thank you from the bottom of my heart for your generosity. You have
				brought a smile on thousands of faces, and we are grateful to you for the same.<br><br>
				Best wishes,<br>
				Yogesh Prabhu Indian<br>
				Founder WE ARE INDIANS NGO, Jaipur<br>
                +91-9887777877 | yogeshprabhuindians@gmail.com<br>
				<br><br>
				<div style='text-align:center;font-size:13px;'>Your Donation has been received from our volunteer ".$_SESSION['userData']['name']."</div>
                </body>
                </html>
                ";
                
    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                
    // More headers
    $headers .= 'From: <no-reply@weareindians.org>' . "\r\n" .
       			'Reply-To: yogeshprabhuindians@gmail.com' . "\r\n";           
    $mail_sent = mail($data['email'],$subject,$message,$headers);
    if($mail_sent) {
      return true;
    } else {
      return false;
    }	
}
?>