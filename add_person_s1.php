<?php

require_once('operations/sessions.php');
require_once('operations/DBconfig.php');
require_once('operations/validations.php');

$response = null;

$_SESSION['step1']=null;
$_SESSION['step2']=null;
$_SESSION['step3']=null;

if (isset($_POST['next_s1'])) {
     if ($_SERVER['REQUEST_METHOD']=='POST') {
        if (!empty($_POST['fname']) &&
            !empty($_POST['lname']) &&
            !empty($_POST['dob']) &&
            !empty($_POST['area']) && 
            !empty($_POST['tehsil']) &&
            !empty($_POST['district']) &&
            !empty($_POST['state']) &&
            !empty($_POST['country']) ) {

                $data = array();
                $data['fname']=mysqli_real_escape_string($conn,$_POST['fname']);
                $data['lname']=mysqli_real_escape_string($conn,$_POST['lname']);
                $data['dob']=mysqli_real_escape_string($conn,$_POST['dob']);
                $data['area']=mysqli_real_escape_string($conn,$_POST['area']);
                $data['tehsil']=mysqli_real_escape_string($conn,$_POST['tehsil']);
                $data['district']=mysqli_real_escape_string($conn,$_POST['district']);
                $data['state']=mysqli_real_escape_string($conn,$_POST['state']);
                $data['country']=mysqli_real_escape_string($conn,$_POST['country']);
                $_SESSION['step1'] = $data;
                unset($data); 
              
                //pass the data through validations
                if(validateInput( $_SESSION['step1']['fname'])) {
                  if(validateInput( $_SESSION['step1']['lname'])) {
                    if(validateDOB( $_SESSION['step1']['dob'],15)) {
                        if(validateInput( $_SESSION['step1']['tehsil']) and strlen($_SESSION['step1']['tehsil']) > 3) {
                          if(validateInput( $_SESSION['step1']['district']) and strlen($_SESSION['step1']['district']) > 3) {
                              if(validateInput( $_SESSION['step1']['state']) and strlen($_SESSION['step1']['state']) > 3) {
                                if(validateInput( $_SESSION['step1']['country']) and strlen($_SESSION['step1']['country']) > 3) {
                                    if (validateInput( $_SESSION['step1']['area']) and strlen($_SESSION['step1']['area']) > 5) {
                                      
                                      // step 1 completed
                                      header("Location: add_person_s2.php");

                                    } else {
                                      unset($_SESSION['step1']['area']);
                                      $response="no special characters are allowed";
                                    }

                                } else {
                                  unset($_SESSION['step1']['country']);
                                  $response="enter a valid country name";
                                }
                              } else {
                                 unset($_SESSION['step1']['state']);
                                 $response="enter a valid state name";
                              }
                          } else {
                              unset($_SESSION['step1']['district']);
                              $response="enter a valid district name";
                          }
                        } else {
                            unset($_SESSION['step1']['tehsil']);
                            $response="enter a valid tehsil name";
                        }
                    } else {
                        unset($_SESSION['step1']['dob']);
                        $response="enter a valid date of birth";
                    }
                  } else {
                      unset($_SESSION['step1']['lname']);
                      $response="enter a valid last name";
                  }
                } else {
                    unset($_SESSION['step1']['fname']);
                    $response="enter a valid first name";
                }

        } else {
          $response="All fields are required";
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
  <title>Add Donor Step-1</title>
  
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
          <h2 class="title">Step 1 : Add Personal Details</h2><hr>
          <form class="update-detail-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" accept="charset UTF-8">
            <div class="form-group">
              <label class="control-label">First Name</label>
              <input name="fname" class="form-control" <?php if(isset($_SESSION['step1']['fname'])){ echo 'value='.$_SESSION['step1']['fname']; } ?> placeholder="First Name" type="text" required>
            </div>
            <div class="form-group">
              <label class="control-label">Last Name</label>
              <input name="lname" class="form-control" <?php if(isset($_SESSION['step1']['lname'])){ echo 'value="'.$_SESSION['step1']['lname'].'"'; } ?> placeholder="Last Name" type="text" required>
            </div>
            <div class="form-group">
              <link rel="stylesheet" href="jquery-ui/jquery-ui-theme.css">
              <script type="text/javascript">
                $(document).ready(function(){
                    $("input[name=dob]").datepicker({  
                                          maxDate: new Date(), 
                                          dateFormat: 'yy-mm-dd',
                                          changeMonth: true,
                                          changeYear: true,
                                          //yearRange: '-20y' 
                                        }).val();
                });
              </script>
              <label class="control-label">D.O.B.</label>
              <input style="background-color: #fff;" readonly name="dob" class="form-control" <?php if(isset($_SESSION['step1']['dob'])){ echo 'value="'.$_SESSION['step1']['dob'].'"';} ?> type="text" placeholder="Date of Birth (minium age 15)" required>
            </div>
            <div class="form-group">
              <label class="control-label">Address</label>
              <input name="area" class="form-control"  <?php if(isset($_SESSION['step1']['area'])){ echo 'value="'.$_SESSION['step1']['area'].'"'; } ?> placeholder="Area/Street" type="text" required>
              <input name="tehsil" class="form-control" <?php if(isset($_SESSION['step1']['tehsil'])){ echo 'value="'.$_SESSION['step1']['tehsil'].'"'; } ?> placeholder="Tehsil" type="text" required>
              <input name="district" class="form-control" <?php if(isset($_SESSION['step1']['district'])){ echo 'value="'.$_SESSION['step1']['district'].'"';} ?> placeholder="District" type="text" required>
              <input name="state" class="form-control" <?php if(isset($_SESSION['step1']['state'])){ echo 'value="'.$_SESSION['step1']['state'].'"'; } ?> placeholder="State" type="text" required>
              <input name="country" class="form-control" <?php if(isset($_SESSION['step1']['country'])){ echo 'value="'.$_SESSION['step1']['country'].'"';} ?> placeholder="country" type="text" required>
            </div>
            

            <div class="row">
              <div class="pull-right" style="margin-right: 15px;">
                <button type="submit" name="next_s1" class="btn btn-primary btn-block btn-flat">NEXT•></button>
              </div>
              <div class="pull-right" style="margin-right: 15px;">
                <button type="reset" class="btn btn-primary btn-block btn-flat">RESET</button>
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