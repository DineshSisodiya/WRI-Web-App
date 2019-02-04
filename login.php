<?php
session_start();

if (!empty($_SESSION['session_life']) and !empty($_SESSION['loggedInUser'])) {
  if ($_SESSION['loggedInUser'] == md5(session_id()) and time() < $_SESSION['session_life']) {
      $_SESSION['session_life']=time()+60; //increase session life by 1min
      header('Location: search.php');
  }
}

require_once('operations/DBconfig.php');
$response = null;

function setAndMailOTP($name,$email) {
            global $conn;
            $response = array();
            $response['success'] = false; 
           // generate 6 digit otp
            $otp = rand(111111,999999);
            
            $sql = "UPDATE `admin`SET `otp`='".$otp."' WHERE `email`='".$email."'";
            $query = mysqli_query($conn,$sql);
            if(!$query) {
              $response['error']=mysqli_error($conn);
            } else {

              // sent otp to user mail id
              $subject = "OTP code for login at WRI";  
              $message = "
                <html>
                <head>
                <title>OTP Code </title>
                </head>
                <body>
                <h4 style='color:#000;font-weight:300;'>Hello ".$name.",<br> Use this OTP code <b style='border:1px soild #000;background:#F0F0F0;padding:5px;border-radius:7px;'>".$otp."</b> to login at We Are Indians Ngo Website. <br>Please don't share OTP with anyone. Sharing OTP may result unwanted actions.</h4>
                </body>
                </html>
                ";
                
                // Always set content-type when sending HTML email
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                
                // More headers
                $headers .= 'From: <no-reply@weareindians.org>' . "\r\n";
               
               $mail_sent = mail($_POST['email'],$subject,$message,$headers);
               if($mail_sent) {
                  $response['success'] = true;
               } else {
                  $response['error'] = "facing problem to send otp<br>is your email id active?";
               }
          }
    return $response;
}

if(isset($_POST['back_to_login'])) {
  unset($_SESSION['otp_sent']);
}

if (isset($_POST['login'])) {
  if (!empty($_POST['email']) and !empty($_POST['password'])) {
    // validate input
    if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) and filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) and !preg_match('/.*[#|\'"=*].*/', $_POST['email']) and preg_match('/^[a-zA-Z0-9*_@$.]{4,30}$/', $_POST['password'])) {

      $_POST['email']=mysqli_real_escape_string($conn,$_POST['email']);
      $_POST['password']=mysqli_real_escape_string($conn,$_POST['password']);
      
      $sql = "SELECT `name` FROM `admin` WHERE `email`='".$_POST['email']."' AND `password`='".md5($_POST['password'])."'";
      $query = mysqli_query($conn,$sql);
      // fetch user details
      $result = mysqli_fetch_assoc($query);
      $row = mysqli_num_rows($query); 
          if($row==1) {
                // generate 6 digit otp
                $otpResponse = setAndMailOTP($result['name'],$_POST['email']);
                if($otpResponse['success']) {
                  $arr = array();
                  $arr['name']=$result['name'];
                  $arr['email']=$_POST['email'];
                  $_SESSION['userData']=$arr;
                  $_SESSION['otp_sent']="yes";
                  unset($arr);
                } else {
                   $response=$otpResponse['error'];
                } 
          } else {
            $response = "Wrong Email or Password";
          }
    } else {
      $response = "Wrong Email or Password";
    }
  } else {
    $response = "Enter both Email and Password";
  }

} else if(isset($_POST['submit']) and !empty($_SESSION['otp_sent'])) {
  if(!empty($_POST['otp']) and preg_match('/^\d{6}$/', $_POST['otp'])) {

      $sql = "SELECT `name` FROM `admin` WHERE `email`='".$_SESSION['userData']['email']."' AND `otp`='".$_POST['otp']."'";
      $query = mysqli_query($conn,$sql);
      $row = mysqli_num_rows($query); 
      if($row==1) {
         $sql = "UPDATE `admin`SET `otp`='null' WHERE `email`='".$_SESSION['userData']['email']."'";
         if($query = mysqli_query($conn,$sql)) {
            $_SESSION['loggedInUser']=md5(session_id());
            $_SESSION['session_life']=time()+600;
            unset($_SESSION['otp_sent']);
            header('Location: search.php');
         } else {
           $response=mysqli_error($conn);
         }
      } else {
        $response = "Either OTP is wrong or it is Expired<br>Login again to get fresh OTP";
      }
  } else {
    $response = "Correct OTP required to login";
  }
}


?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WRI Admin Login</title>
  
  <link rel='stylesheet prefetch' href='css/bootstrap/bootstrap.css'>

  <link rel="stylesheet" href="css/style.css">
</head>

<body class="login-page">

<div class="container-fluid">
  <div class="row" style="position: fixed;height:100%;width: 100%;">
    <div class="col-sm-4 col-xs-12 bootstrap-cols left_section">
      <div class="login-box">
        <img class="user-dp" src="images/default_login_dp.png" alt="User Avatar">

        <?php 

          if(empty($_SESSION['otp_sent'])) {
            if(isset($response))
              echo '<p class="login-box-msg login-form-response">'.$response.'</p>';
            else
              echo '<p class="login-box-msg login-form-response">Login to get inside access</p>';
        ?>
          <form style="z-index: 1 !important;" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" accept="charset UTF-8" class="signin-page-form">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
              <input name="email" class="form-control"  placeholder="Email Address" type="email" required>
            </div>
            <div class="input-group">
               <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
              <input name="password" class="form-control" placeholder="Password" type="password" required>
            </div>
            <div class="row">
              <div class="pull-left">
                <div class="checkbox">
                  <label>
                      <a href="#" onclick="reminder()">Forgot your password ?</a><br>
                  </label>
                </div>
              </div>
              <div class="pull-right" style="margin-right: 15px;">
                <button type="submit" name="login" class="btn btn-primary btn-block btn-flat signin-btn">Log In</button>
              </div>
            </div>
          </form>
        <?php
          }
          else {
            if(isset($response))
              echo '<p class="login-box-msg login-form-response">'.$response.'</p>';
            else 
              echo '<p class="login-box-msg login-form-response" style="color:#000;">Please check your Emails.<br> OTP code has been sent to your mail</p>';
        ?>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" accept="charset UTF-8" class="signin-page-form">
              <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input name="otp" class="form-control"  placeholder="Enter OTP code" type="number" min="0">
              </div>
              <div class="row">
                <div class="pull-right" style="margin-right: 15px;">
                  <button type="submit" name="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
                </div>
                <div class="pull-right" style="margin-right: 15px;">
                  <button type="submit" name="back_to_login" class="btn btn-primary btn-block btn-flat"><•Back</button>
                </div>
              </div>
            </form>
        <?php
          }
        ?>

      </div>

      <div class="footer">
        © <?php echo date('Y'); ?> | WE ARE INDIANS
      </div>
    </div>
    <div class="col-sm-8 col-xs-12 text-center bootstrap-cols right_section">
      <div class="about-info">
        <h3>WE ARE INDIANS NGO<hr></h3>
        <p>
        “Love is not patronizing and charity isn't about pity, it's about love. Charity and love are the same -- with charity you give love, so do not just give money but also reach out your hand instead.” <b><i>― Mother Teresa</i></b><br><br>
        “The purpose of life is not to be happy. It is to be useful, to be honorable, to be compassionate, to have it make some difference that you have lived and lived well.” <b><i>― Ralph Waldo Emerson</i></b><br><br>
        “No one has ever become poor by giving.” <b><i>― Anne Frank</i></b>
        </p>
      </div>
    </div>
  </div>
</div>

  <script src='js/jquery.js'></script>
</body>
</html>


<?php mysqli_close($conn); ?>