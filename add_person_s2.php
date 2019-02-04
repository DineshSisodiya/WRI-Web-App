<?php 

require_once('operations/sessions.php');
require_once('operations/DBconfig.php');
require_once('operations/validations.php');

$response = null;

if (empty($_SESSION['step1'])) {
  $_SESSION['step2']=null;
  $_SESSION['step3']=null;
  header("Location:add_person_s1.php");
}

if (isset($_POST['next_s2'])) {

      if ($_SERVER['REQUEST_METHOD']=='POST') {
        if (!empty($_POST['mobile']) &&
            !empty($_POST['whatsapp']) &&
            !empty($_POST['email']) &&
            !empty($_POST['blood_group']) && 
            !empty($_POST['can_donate_blood']) ) {

                $data = array();
                $data['whatsapp']=mysqli_real_escape_string($conn,$_POST['whatsapp']);                    
                $data['email']=mysqli_real_escape_string($conn,$_POST['email']);
                $data['blood_group']=mysqli_real_escape_string($conn,$_POST['blood_group']);
                $data['can_donate_blood']=mysqli_real_escape_string($conn,$_POST['can_donate_blood']);
                $data['mobile']=mysqli_real_escape_string($conn,$_POST['mobile']);
                $_SESSION['step2'] = $data;
                unset($data); 
                
                //pass the data through validations
                if(validateMobileNumber($_SESSION['step2']['mobile'])) {
                  if(validateMobileNumber($_SESSION['step2']['whatsapp'])) {
                    if(validateEmail($_SESSION['step2']['email'])) {
                        if(validateBloodGroup($_SESSION['step2']['blood_group'])) {
                          if($_SESSION['step2']['can_donate_blood']=="yes" or $_SESSION['step2']['can_donate_blood']=="no") {

                             $sql = "SELECT `mobile` FROM donors WHERE `mobile`=".$_SESSION['step2']['mobile'];
                              $query=mysqli_query($conn,$sql);
                              $num_rows=mysqli_num_rows($query);
                              if ($num_rows==0) {
                                    
                                   header("Location: add_person_s3.php");

                              } else {
                                  unset($_SESSION['step2']['mobile']);
                                  $response="Mobile No. Already Registered, Choose New";
                              }
                          } else {
                              unset($_SESSION['step2']['can_donate_blood']);
                              $response="choose yes/no for blood donation";
                          }
                        } else {
                            unset($_SESSION['step2']['blood_group']);
                            $response="select a valid blood_group";
                        }
                    } else {
                        unset($_SESSION['step2']['email']);
                        $response="enter a valid email";
                    }
                  } else {
                      unset($_SESSION['step2']['whatsapp']);
                      $response="enter a valid whatsapp number";
                  }
                } else {
                    unset($_SESSION['step2']['mobile']);
                    $response="enter a valid mobile number";
                }

                
        } else {
          echo '<script>alert("all fields are required");</script>';
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
  <title>Adding Donor Step-2</title>
  
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
          <h2 class="title">Step 2 : Add Contact & Blood Details</h2><hr>
          <form class="update-detail-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" accept="charset UTF-8">

            <div class="form-group">
              <label class="control-label">Mobile</label>
              <input name="mobile" class="form-control"  placeholder="Mobile" value="<?php if(isset($_SESSION['step2']['mobile'])){ echo $_SESSION['step2']['mobile']; } ?>" type="text" required>
            </div>
            <div class="form-group">
              <label class="control-label">Whatsapp</label>
              <input name="whatsapp" class="form-control"  placeholder="Whatsapp" value="<?php if(isset($_SESSION['step2']['whatsapp'])){ echo $_SESSION['step2']['whatsapp']; } ?>" type="text" required>
              <input type="checkbox" name="pickWtspNmbr"><span>Same as Mobile</span>
            </div>
            <div class="form-group">
              <label class="control-label">Email</label>
              <input name="email" class="form-control"  placeholder="Email" value="<?php if(isset($_SESSION['step2']['email'])){ echo $_SESSION['step2']['email']; } ?>" type="email" required>
            </div>

            <div class="form-group">
              <label class="control-label">Blood Group</label>
              <select name="blood_group" class="form-control">
                <option value="A+" <?php if(isset($_SESSION['step2']['blood_group'])&&$_SESSION['step2']['blood_group']=="A+") echo "selected"?> > A+</option>
                <option value="A-" <?php if(isset($_SESSION['step2']['blood_group'])&&$_SESSION['step2']['blood_group']=="A-") echo "selected"?> > A-</option>
                <option value="B+" <?php if(isset($_SESSION['step2']['blood_group'])&&$_SESSION['step2']['blood_group']=="B+") echo "selected"?> > B+</option>
                <option value="B-" <?php if(isset($_SESSION['step2']['blood_group'])&&$_SESSION['step2']['blood_group']=="B-") echo "selected"?> > B-</option>
                <option value="O+" <?php if(isset($_SESSION['step2']['blood_group'])&&$_SESSION['step2']['blood_group']=="O+") echo "selected"?> > O+</option>
                <option value="O-" <?php if(isset($_SESSION['step2']['blood_group'])&&$_SESSION['step2']['blood_group']=="O-") echo "selected"?> > O-</option>
                <option value="AB+" <?php if(isset($_SESSION['step2']['blood_group'])&&$_SESSION['step2']['blood_group']=="AB+") echo "selected"?> > AB+</option>
                <option value="AB-" <?php if(isset($_SESSION['step2']['blood_group'])&&$_SESSION['step2']['blood_group']=="AB-") echo "selected"?> > AB-</option>
              </select>
            </div>
            <div class="form-group">
              <label class="control-label">Would you save a life with your blood ?</label><br>
              <input name="can_donate_blood" class="" type="radio" value="yes" required <?php if(isset($_SESSION['step2']['can_donate_blood'])&&$_SESSION['step2']['can_donate_blood']=="yes") echo "checked"?> ><label class="control-label"> Yes</label>
              <input name="can_donate_blood" class="" type="radio" value="no" required <?php if(isset($_SESSION['step2']['can_donate_blood'])&&$_SESSION['step2']['can_donate_blood']=="no") echo "checked"?> ><label class="control-label"> No</label>
            </div>
            <br>
            <div class="row">
              <div class="pull-right" style="margin-right: 15px;">
                <button type="submit" name="next_s2" class="btn btn-primary btn-block btn-flat">NEXT•></button>
              </div>
              <div class="pull-right" style="margin-right: 15px;">
                <button type="reset" class="btn btn-primary btn-block btn-flat">RESET</button>
              </div>
              <div class="pull-right" style="margin-right: 15px;">
                <a href="add_person_s1.php"><button type="button" class="btn btn-primary btn-block btn-flat"><•BACK</button></a>
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