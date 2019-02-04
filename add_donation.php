<?php

require_once('operations/sessions.php');
require_once('operations/DBconfig.php');
require_once('operations/validations.php');

$response = null;

if (isset($_POST['save_all'])) {
    if ($_SERVER['REQUEST_METHOD']=='POST') {
        if (!empty($_POST['mobile']) &&
            !empty($_POST['donation_amount']) &&
            !empty($_POST['donation_period']) &&
            !empty($_POST['payment_mode']) ) {

                $data = array();
                $data['mobile']=mysqli_real_escape_string($conn,$_POST['mobile']);
                $data['donation_amount']=mysqli_real_escape_string($conn,$_POST['donation_amount']);
                $data['donation_period']=mysqli_real_escape_string($conn,$_POST['donation_period']);
                $data['payment_mode']=mysqli_real_escape_string($conn,$_POST['payment_mode']);

                if($data['payment_mode']!="cash") {
                  if(!empty($_POST['transaction_id']) and preg_match("/^\d{5}$/", $_POST['transaction_id'])) {
                    $data['transaction_id']=mysqli_real_escape_string($conn,$_POST['transaction_id']);
                  } else {
                     $response="Enter Correct "+ ucfirst($data['payment_mode']) +" Transcation No.";
                  }
                } else {
                    $data['transaction_id']='NA';
                } 

                $_SESSION['data'] = $data;
                unset($data);

                //pass the data through validations
                if(validateMobileNumber($_SESSION['data']['mobile'])) {
                  if(preg_match("/^\d{2,8}$/",$_SESSION['data']['donation_amount'])) {
                    if(preg_match("/^[a-z-]*$/",$_SESSION['data']['donation_period'])) {
                      if(preg_match("/^[a-z ]*$/",$_SESSION['data']['payment_mode'])) {

                          $sql = "INSERT INTO `donation`(`don_id`, `don_amount`, `don_period`, `payment_mode`, `transaction_id`, `on_date`, `received_by`) VALUES('".$_SESSION['data']['mobile']."','".$_SESSION['data']['donation_amount']."','".$_SESSION['data']['donation_period']."','".$_SESSION['data']['payment_mode']."','".$_SESSION['data']['transaction_id']."','".date('Y-m-d')."','".$_SESSION['userData']['name']."')";

                          $query=mysqli_query($conn,$sql);
                                      
                          if ($query) {
                            $response='Donation Details has been added Successfully';
                            // send thanks mail for donation
                            if(include_once 'thanks_mail.php') {
                              $sql='SELECT first_name,last_name,email FROM `donors` WHERE mobile='.$_SESSION['data']['mobile'];
                              $query=mysqli_query($conn,$sql);
                              $details=mysqli_fetch_assoc($query);
                              $arr=array();
                              $arr['email']=$details['email'];
                              $arr['name']=$details['first_name'].' '.$details['last_name'];
                              $arr['amount']=$_SESSION['data']['donation_amount'];
                              sendThanks($arr);
                              unset($arr);
                              unset($details);
                              unset($_SESSION['data']);
                            }
                              
                          } else {
                                if(mysqli_errno($conn)==1062)
                                  $response = "Oops! It seems you have already added these details.";
                                else
                                  $response=mysqli_error($conn);
                          }

                      } else {
                          unset($_SESSION['data']['payment_mode']);
                          $response="Select payment mode from list";
                      }
                    } else {
                        unset($_SESSION['data']['donation_period']);
                        $response="Select donation period from list";
                    }
                  } else {
                      unset($_SESSION['data']['donation_amount']);
                      $response="Enter valid donation amount";
                  }  
                } else {
                    unset($_SESSION['data']['mobile']);
                    $response="Enter valid mobile number";
                } 
        } else {
          $response="Enter All required fields";
        }
    }
  } 
?>


<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Donation Details</title>
  
  <link rel='stylesheet prefetch' href='css/bootstrap/bootstrap.min.css'>
  <link rel="stylesheet" type="text/css" href="jquery-ui/jquery-ui.min.css">

  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/sidebar.css">

  <script src='js/jquery.js'></script>
  <script type="text/javascript" src="jquery-ui/jquery-ui.js"></script>
  <script type="text/javascript" src="js/validate.js"></script>
  
</head>

<body>
<div class="container header">
  <?php include_once 'header.php'; ?>
</div>
<div class="container-fluid">
  <div class="row" style="height:100%;">
    <div class="menu_section">

        <!-- sidebar menu -->
          <?php include_once 'sidebar.php'; ?>
        <!-- sidebar menu -->

      <div class="footer">
        © <?php echo date('Y'); ?> | WE ARE INDIANS
      </div>
    </div>

    <div class="data_section">
      <div class="content-box">
        <div class="profile-data">
          <?php 
            if (!empty($response)) {
              echo '<div class="form-response">'. $response .'</div>';
            }
          ?>
          <h2 class="title">Add Donation Details</h2><hr>
          <form class="update-detail-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" accept="charset UTF-8">

            <div class="form-group" id="mobile_verify">
              <label class="control-label">Donor Mobile No.</label>
              <input name="mobile" class="form-control" id="donor_mobile"  placeholder="Already Registered Mobile No." value="<?php if(isset($_SESSION['data']['mobile'])){ echo $_SESSION['data']['mobile']; } ?>" type="number" min="0" required>
              <input type="hidden" id="responseSet" value="">
            </div>

            <div class="form-group">
              <label class="control-label">Donation Amount</label>
              <input name="donation_amount" class="form-control"  placeholder="500 Rupees" value="<?php if(isset($_SESSION['data']['donation_amount'])){ echo $_SESSION['data']['donation_amount']; } ?>" type="number" min="0" required>
            </div>

            <div class="form-group">
              <label class="control-label">Donation Period</label>
              <select name="donation_period" class="form-control">
                <option value="quarterly" <?php if(isset($_SESSION['data']['donation_period'])&&$_SESSION['data']['donation_period']=="quarterly") echo "selected"?> >Quarterly ~ 100 Rs. </option>
                <option value="half-yearly" <?php if(isset($_SESSION['data']['donation_period'])&&$_SESSION['data']['donation_period']=="half yearly") echo "selected"?> >Half Yearly ~ 200 Rs. </option>
                <option value="yearly" <?php if(isset($_SESSION['data']['donation_period'])&&$_SESSION['data']['donation_period']=="yearly") echo "selected"?> >Yearly  ~ 365 Rs. </option>
                <option value="two-year" <?php if(isset($_SESSION['data']['donation_period'])&&$_SESSION['data']['donation_period']=="two year") echo "selected"?> >Two Year ~ 750 Rs. </option>\
                <option value="three-year" <?php if(isset($_SESSION['data']['donation_period'])&&$_SESSION['data']['donation_period']=="three year") echo "selected"?> >Three Year ~ 1100 Rs. </option>
                <option value="five-year" <?php if(isset($_SESSION['data']['donation_period'])&&$_SESSION['data']['donation_period']=="five year") echo "selected"?> >Five Year  ~ 2100 Rs. </option>
                <option value="without-plan" <?php if(isset($_SESSION['data']['donation_period'])&&$_SESSION['data']['donation_period']=="without plan") echo "selected"?> >Without Plan ~ Be Generous</option>
              </select>
            </div>

            <div class="form-group" id="payment_mode">
              <label class="control-label">Payment mode</label><br>
              <select id="payment_mode_op" name="payment_mode" class="form-control">
                <option value="cash" <?php if(isset($_SESSION['data']['payment_mode'])&&$_SESSION['data']['payment_mode']=="cash") echo "selected"?> >Cash</option>
                <option value="check" <?php if(isset($_SESSION['data']['payment_mode'])&&$_SESSION['data']['payment_mode']=="check") echo "selected"?> >Check</option>
                <option value="paytm" <?php if(isset($_SESSION['data']['payment_mode'])&&$_SESSION['data']['payment_mode']=="paytm") echo "selected"?> >Paytm</option>
                <option value="phonpe" <?php if(isset($_SESSION['data']['payment_mode'])&&$_SESSION['data']['payment_mode']=="phonpe") echo "selected"?> >Phonpe</option>
                <option value="tez" <?php if(isset($_SESSION['data']['payment_mode'])&&$_SESSION['data']['payment_mode']=="tez") echo "selected"?> >Tez/G-Pay</option>
                <option value="bhim upi" <?php if(isset($_SESSION['data']['payment_mode'])&&$_SESSION['data']['payment_mode']=="bhim upi") echo "selected"?> >BHIM UPI</option>
              </select>
            </div>
            
            <div class="row">
              <div class="pull-right" style="margin-right: 15px;">
                <button name="save_all" type="submit" class="btn btn-primary btn-block btn-flat">SAVE ALL</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="js/main.js"></script>
<script type="text/javascript" src="js/find_donor_name.js"></script>
</body>
</html>
        