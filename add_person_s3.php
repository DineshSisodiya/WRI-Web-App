<?php

require_once('operations/sessions.php');
require_once('operations/DBconfig.php');
require_once('operations/validations.php');

$response = null;

if (empty($_SESSION['step1'])) {
  header("Location:add_person_s1.php");
} else if(empty($_SESSION['step2'])) {
  header("Location:add_person_s2.php");
}
if (isset($_POST['save_all'])) {
    if ($_SERVER['REQUEST_METHOD']=='POST') {
        if (!empty($_POST['donation_amount']) &&
            !empty($_POST['donation_period']) &&
            !empty($_POST['payment_mode']) &&
            !empty($_POST['be_volunteer']) ) {

                $data = array();
                $data['donation_amount']=mysqli_real_escape_string($conn,$_POST['donation_amount']);
                $data['donation_period']=mysqli_real_escape_string($conn,$_POST['donation_period']);
                $data['payment_mode']=mysqli_real_escape_string($conn,$_POST['payment_mode']);
                $data['be_volunteer']=mysqli_real_escape_string($conn,$_POST['be_volunteer']); 

                if($data['payment_mode']!="cash") {
                  if(!empty($_POST['transaction_id']) and preg_match("/^\d{5}$/", $_POST['transaction_id'])) {
                    $data['transaction_id']=mysqli_real_escape_string($conn,$_POST['transaction_id']);
                  } else {
                     $response="Enter Correct "+ ucfirst($data['payment_mode']) +" Transcation No.";
                  }
                } else {
                    $data['transaction_id']='NA';
                } 

                $_SESSION['step3'] = $data;
                unset($data);

                //pass the data through validations
                if(preg_match("/^\d{2,8}$/",$_SESSION['step3']['donation_amount'])) {
                  if(preg_match("/^[a-z-]*$/",$_SESSION['step3']['donation_period'])) {
                    if(preg_match("/^[a-z ]*$/",$_SESSION['step3']['payment_mode'])) {
                        if($_SESSION['step3']['be_volunteer']=="yes" or $_SESSION['step3']['be_volunteer']=="no") {
                                     
                                     // insert into database
                                     $sql1="INSERT INTO `donors`(`mobile`, `first_name`, `last_name`, `dob`, `whatsapp`, `email`, `blood_group`, `can_donate_blood`, `area`, `tehsil`, `district`, `state`, `country`, `joined_on`, `joined_by_name`, `joined_by_mail`, `is_volunteer`) VALUES ('".$_SESSION['step2']['mobile']."','".$_SESSION['step1']['fname']."','".$_SESSION['step1']['lname']."','".$_SESSION['step1']['dob']."','".$_SESSION['step2']['whatsapp']."','".$_SESSION['step2']['email']."','".$_SESSION['step2']['blood_group']."','".$_SESSION['step2']['can_donate_blood']."','".$_SESSION['step1']['area']."','".$_SESSION['step1']['tehsil']."','".$_SESSION['step1']['district']."','".$_SESSION['step1']['state']."','".$_SESSION['step1']['country']."','".date('Y-m-d')."','".$_SESSION['userData']['name']."','".$_SESSION['userData']['email']."','".$_SESSION['step3']['be_volunteer']."')";
                                    $sql2 = "INSERT INTO `donation`(`don_id`, `don_amount`, `don_period`, `payment_mode`, `transaction_id`, `on_date`, `received_by`) VALUES('".$_SESSION['step2']['mobile']."','".$_SESSION['step3']['donation_amount']."','".$_SESSION['step3']['donation_period']."','".$_SESSION['step3']['payment_mode']."','".$_SESSION['step3']['transaction_id']."','".date('Y-m-d')."','".$_SESSION['userData']['name']."')";
                                    
                                    $query1=mysqli_query($conn,$sql1);
                                    
                                    if ($query1) {
                                      $query2=mysqli_query($conn,$sql2);

                                      if ($query2) {
                                        $response='redirecting in 10 sec... to <a href="add_family.php?id='.$_SESSION['step2']['mobile'].'">Add Family Details</a> <br>Donar details succesfully added.<script>window.setTimeout(function(){ window.location.href = "add_family.php?don_id='.$_SESSION['step2']['mobile'].'"; }, 10000);</script>';
                                        // send thanks mail for donation
                                        if(include_once 'thanks_mail.php') {
                                            $arr=array();
                                            $arr['email']=$_SESSION['step2']['email'];
                                            $arr['name']=$_SESSION['step1']['fname'].' '.$_SESSION['step1']['lname'];
                                            $arr['amount']=$_SESSION['step3']['donation_amount'];
                                            if(sendThanks($arr))
                                              echo "ok sent";
                                            else
                                              echo "not sent";
                                            unset($arr);
                                        }

                                        //delete all step data from session
                                        unset($_SESSION['step2']);
                                        unset($_SESSION['step1']);
                                        unset($_SESSION['step3']);

                                      } else {
                                        $sql = "DELETE FROM `donors` WHERE `mobile`=".$_SESSION['step2']['mobile'];
                                        $query = mysqli_query($conn,$sql);
                                        $response=mysqli_error($conn);
                                      }
                                        
                                    } else {
                                       $response=mysqli_error($conn);
                                    }
      
                        } else {
                            unset($_SESSION['step3']['be_volunteer']);
                            $response="Choose Yes/No to be Volunteer";
                        }
                    } else {
                        unset($_SESSION['step3']['payment_mode']);
                        $response="select payment mode from list";
                    }
                  } else {
                      unset($_SESSION['step3']['donation_period']);
                      $response="select donation period from list";
                  }
                } else {
                    unset($_SESSION['step3']['donation_amount']);
                    $response="enter valid donation amount";
                }    
        } else {
          $response="Enter all required fields";
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
  <title>Adding Donor Final Step</title>
  
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
          <h2 class="title">Step 3 : Add Donation Details</h2><hr>
          <form class="update-detail-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" accept="charset UTF-8">

            <div class="form-group">
              <label class="control-label">Donation Amount</label>
              <input name="donation_amount" class="form-control"  placeholder="500 Rupees" value="<?php if(isset($_SESSION['step3']['donation_amount'])){ echo $_SESSION['step3']['donation_amount']; } ?>" type="number" min="0" required>
            </div>

            <div class="form-group">
              <label class="control-label">Donation Period</label>
              <select name="donation_period" class="form-control">
                <option value="quarterly" <?php if(isset($_SESSION['step3']['donation_period'])&&$_SESSION['step3']['donation_period']=="quarterly") echo "selected"?> >Quarterly ~ 100 Rs. </option>
                <option value="half-yearly" <?php if(isset($_SESSION['step3']['donation_period'])&&$_SESSION['step3']['donation_period']=="half yearly") echo "selected"?> >Half Yearly ~ 200 Rs. </option>
                <option value="yearly" <?php if(isset($_SESSION['step3']['donation_period'])&&$_SESSION['step3']['donation_period']=="yearly") echo "selected"?> >Yearly  ~ 365 Rs. </option>
                <option value="two-year" <?php if(isset($_SESSION['step3']['donation_period'])&&$_SESSION['step3']['donation_period']=="two year") echo "selected"?> >Two Year ~ 750 Rs. </option>\
                <option value="three-year" <?php if(isset($_SESSION['step3']['donation_period'])&&$_SESSION['step3']['donation_period']=="three year") echo "selected"?> >Three Year ~ 1100 Rs. </option>
                <option value="five-year" <?php if(isset($_SESSION['step3']['donation_period'])&&$_SESSION['step3']['donation_period']=="five year") echo "selected"?> >Five Year  ~ 2100 Rs. </option>
                <option value="without-plan" <?php if(isset($_SESSION['step3']['donation_period'])&&$_SESSION['step3']['donation_period']=="without plan") echo "selected"?> >Without Plan ~ Be Generous</option>
              </select>
            </div>

            <div class="form-group" id="payment_mode">
              <label class="control-label">Payment mode</label><br>
              <select id="payment_mode_op" name="payment_mode" class="form-control">
                <option value="cash" <?php if(isset($_SESSION['step3']['payment_mode'])&&$_SESSION['step3']['payment_mode']=="cash") echo "selected"?> >Cash</option>
                <option value="check" <?php if(isset($_SESSION['step3']['payment_mode'])&&$_SESSION['step3']['payment_mode']=="check") echo "selected"?> >Check</option>
                <option value="paytm" <?php if(isset($_SESSION['step3']['payment_mode'])&&$_SESSION['step3']['payment_mode']=="paytm") echo "selected"?> >Paytm</option>
                <option value="phonpe" <?php if(isset($_SESSION['step3']['payment_mode'])&&$_SESSION['step3']['payment_mode']=="phonpe") echo "selected"?> >Phonpe</option>
                <option value="tez" <?php if(isset($_SESSION['step3']['payment_mode'])&&$_SESSION['step3']['payment_mode']=="tez") echo "selected"?> >Tez/G-Pay</option>
                <option value="bhim upi" <?php if(isset($_SESSION['step3']['payment_mode'])&&$_SESSION['step3']['payment_mode']=="bhim upi") echo "selected"?> >BHIM UPI</option>
              </select>
            </div>

            <div class="form-group">
              <label class="control-label">Being Voluteer is great job. wanna be ?</label><br>
              <input name="be_volunteer" class="" type="radio" value="yes" required <?php if(isset($_SESSION['step3']['be_volunteer'])&&$_SESSION['step3']['be_volunteer']=="yes") echo "checked"?> ><label class="control-label"> Yes</label>
              <input name="be_volunteer" class="" type="radio" value="no" required <?php if(isset($_SESSION['step3']['be_volunteer'])&&$_SESSION['step3']['be_volunteer']=="no") echo "checked"?> ><label class="control-label"> No</label>
            </div>

            
            <div class="row">
              <div class="pull-right" style="margin-right: 15px;">
                <button name="save_all" type="submit" class="btn btn-primary btn-block btn-flat">SAVE ALL</button>
              </div>
              <div class="pull-right" style="margin-right: 15px;">
                 <a href="add_person_s2.php"><button type="button" class="btn btn-primary btn-block btn-flat"><•BACK</button></a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>