<?php

require_once('operations/sessions.php');
require_once('operations/DBconfig.php');
require_once('operations/validations.php');

$response = null;

if (isset($_POST['save'])) {
     if ($_SERVER['REQUEST_METHOD']=='POST') {
        if (!empty($_POST['donor_mobile']) &&
            !empty($_POST['first_name']) &&
            !empty($_POST['last_name']) &&
            !empty($_POST['dob']) &&
            !empty($_POST['blood_group']) && 
            !empty($_POST['relation']) ) {

                $data = array();
                $data['donor_mobile']=mysqli_real_escape_string($conn,$_POST['donor_mobile']);
                $data['first_name']=mysqli_real_escape_string($conn,$_POST['first_name']);
                $data['last_name']=mysqli_real_escape_string($conn,$_POST['last_name']);
                $data['dob']=mysqli_real_escape_string($conn,$_POST['dob']);
                $data['blood_group']=mysqli_real_escape_string($conn,$_POST['blood_group']);
                $data['relation']=mysqli_real_escape_string($conn,$_POST['relation']);

                if(!empty($_POST['can_donate_blood']) and ($_POST['can_donate_blood']=="yes") )
                  $data['can_donate_blood']=mysqli_real_escape_string($conn,$_POST['can_donate_blood']);
                else 
                  $data['can_donate_blood']="no"; 

                if(!empty($_POST['is_volunteer']) and ($_POST['is_volunteer']=="yes") )
                  $data['is_volunteer']=mysqli_real_escape_string($conn,$_POST['is_volunteer']);
                else 
                  $data['is_volunteer']="no";                
                if(!empty($_POST['mobile']) and validateMobileNumber($_POST['mobile'])) {
                  $data['mobile']=mysqli_real_escape_string($conn,$_POST['mobile']);
                } else {
                  $data['mobile']='NA';
                }

                $_SESSION['fmember'] = $data;
                unset($data); 

                //pass the data through validations
                if(validateMobileNumber( $_SESSION['fmember']['donor_mobile'])) {
                  if(preg_match('/^[a-zA-Z ]{2,100}$/' ,$_SESSION['fmember']['first_name']) and preg_match('/^[a-zA-Z ]{2,100}$/' ,$_SESSION['fmember']['last_name'])) {
                    if(validateBloodGroup( $_SESSION['fmember']['blood_group'])) {
                      if(validateDOB($_SESSION['fmember']['dob'])) {
                          if(preg_match('/^[a-z]{3,20}$/', $_SESSION['fmember']['relation'])) {
                             // insert into database
                             $sql="SELECT `first_name`, `last_name` FROM `donors` WHERE `mobile`='".$_SESSION['fmember']['donor_mobile']."'";
                             $query=mysqli_query($conn,$sql);
                             $result = mysqli_fetch_assoc($query);
                             $num_rows=mysqli_num_rows($query);
                             
                             if ($num_rows==1) {
                                $sql = "INSERT INTO `family`(`don_mobile`, `first_name`, `last_name`, `dob`, `mobile`, `blood_group`,`can_donate_blood`, `relation`, `is_volunteer`) VALUES('".$_SESSION['fmember']['donor_mobile']."','".$_SESSION['fmember']['first_name']."','".$_SESSION['fmember']['last_name']."','".$_SESSION['fmember']['dob']."','".$_SESSION['fmember']['mobile']."','".$_SESSION['fmember']['blood_group']."','".$_SESSION['fmember']['can_donate_blood']."','".$_SESSION['fmember']['relation']."','".$_SESSION['fmember']['is_volunteer']."')";
                                
                                $query=mysqli_query($conn,$sql);
                                if ($query) {
                                  $response=$_SESSION['fmember']['first_name']." is added as ".$_SESSION['fmember']['relation']." of ".$result['first_name']." ".$result['last_name'];
                                  unset($_POST);
                                  unset($_SESSION['fmember']);
                                } else {
                                  if(mysqli_errno($conn)==1062)
                                    $response="Oops! ".$_SESSION['fmember']['first_name']." seems to be already Registered. Please Add New Member";
                                  else
                                    $response=mysqli_error($conn);
                                }
                             } else {
                                unset($_SESSION['fmember']['donor_mobile']);
                                $response="Donor Mobile No. is not registered";
                             }
                          } else {
                              unset($_SESSION['fmember']['relation']);
                              $response="select a relation from actual list";
                          }
                      } else {
                          unset($_SESSION['fmember']['dob']);
                          $response="select a valid date of birth";
                      }
                    } else {
                        unset($_SESSION['fmember']['blood_group']);
                        $response="select a blood_group from actual list";
                    }
                  } else {
                      unset($_SESSION['fmember']['first_name']);
                      unset($_SESSION['fmember']['last_name']);
                      $response="enter a valid name";
                  }
                } else {
                      unset($_SESSION['fmember']['donor_mobile']);
                      $response="Donor Mobile No. is not valid";
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
  <title>Add Family Memebrs</title> 
  
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
          <h2 class="title">Add Family Members</h2><hr>
          <form id="add_family" class="update-detail-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" accept="charset UTF-8">
            <div class="form-group" id="mobile_verify">
              <label class="control-label">Donor Mobile Number</label>
              <input name="donor_mobile" id="donor_mobile" class="form-control" <?php if(isset($_SESSION['fmember']['donor_mobile'])){ echo 'value='.$_SESSION['fmember']['donor_mobile']; } else if(isset($_GET['don_id'])){ echo 'value='.$_GET['don_id']; }  ?> placeholder="Already Registered Mobile No." type="text" required>
              <input type="hidden" id="responseSet" value="">
            </div>
            <div class="form-group">
              <label class="control-label">First Name</label>
              <input name="first_name" class="form-control" <?php if(isset($_SESSION['fmember']['first_name'])){ echo 'value='.$_SESSION['fmember']['first_name']; } ?> placeholder="Member First Name" type="text" required>
            </div>
            <div class="form-group">
              <label class="control-label">Last Name</label>
              <input name="last_name" class="form-control" <?php if(isset($_SESSION['fmember']['last_name'])){ echo 'value='.$_SESSION['fmember']['last_name']; } ?> placeholder="Member Last Name" type="text" required>
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
              <input style="background-color: #fff;" name="dob" class="form-control" <?php if(isset($_SESSION['fmember']['dob'])){ echo 'value="'.$_SESSION['fmember']['dob'].'"';} ?> type="text" placeholder="Date of Birth" readonly required>
            </div>
            <div class="form-group">
              <label class="control-label">Mobile</label>
              <input name="mobile" class="form-control" <?php if(isset($_SESSION['fmember']['mobile'])){ echo 'value="'.$_SESSION['fmember']['mobile'].'"'; } ?> placeholder="Mobile/Whatsapp No. ( optional )" type="number">
            </div>
            <div class="form-group">
              <label class="control-label">Blood Group</label>
              <select name="blood_group" class="form-control">
                <option value="A+" <?php if(isset($_SESSION['fmember']['blood_group'])&&$_SESSION['fmember']['blood_group']=="A+") echo "selected"?> > A+</option>
                <option value="A-" <?php if(isset($_SESSION['fmember']['blood_group'])&&$_SESSION['fmember']['blood_group']=="A-") echo "selected"?> > A-</option>
                <option value="B+" <?php if(isset($_SESSION['fmember']['blood_group'])&&$_SESSION['fmember']['blood_group']=="B+") echo "selected"?> > B+</option>
                <option value="B-" <?php if(isset($_SESSION['fmember']['blood_group'])&&$_SESSION['fmember']['blood_group']=="B-") echo "selected"?> > B-</option>
                <option value="O+" <?php if(isset($_SESSION['fmember']['blood_group'])&&$_SESSION['fmember']['blood_group']=="O+") echo "selected"?> > O+</option>
                <option value="O-" <?php if(isset($_SESSION['fmember']['blood_group'])&&$_SESSION['fmember']['blood_group']=="O-") echo "selected"?> > O-</option>
                <option value="AB+" <?php if(isset($_SESSION['fmember']['blood_group'])&&$_SESSION['fmember']['blood_group']=="AB+") echo "selected"?> > AB+</option>
                <option value="AB-" <?php if(isset($_SESSION['fmember']['blood_group'])&&$_SESSION['fmember']['blood_group']=="AB-") echo "selected"?> > AB-</option>
              </select>
            </div>
            <div class="form-group">
                <label class="control-label">Select Relation with Donor</label>
                <select name="relation" class="form-control">
                  <option value="brother" <?php if(isset($_SESSION['fmember']['relation'])&&$_SESSION['fmember']['relation']=="brother") echo "selected"?> >Brother</option>
                  <option value="sister" <?php if(isset($_SESSION['fmember']['relation'])&&$_SESSION['fmember']['relation']=="sister") echo "selected"?> >Sister</option>
                  <option value="son" <?php if(isset($_SESSION['fmember']['relation'])&&$_SESSION['fmember']['relation']=="son") echo "selected"?> >Son</option>
                  <option value="daughter" <?php if(isset($_SESSION['fmember']['relation'])&&$_SESSION['fmember']['relation']=="daughter") echo "selected"?> >Daughter</option>\
                  <option value="mother" <?php if(isset($_SESSION['fmember']['relation'])&&$_SESSION['fmember']['relation']=="mother") echo "selected"?> >Mother</option>
                  <option value="father" <?php if(isset($_SESSION['fmember']['relation'])&&$_SESSION['fmember']['relation']=="father") echo "selected"?> >Father</option>
                  <option value="spouse" <?php if(isset($_SESSION['fmember']['relation'])&&$_SESSION['fmember']['relation']=="spouse") echo "selected"?> >Spouse</option>
                </select>
            </div>
            <div class="form-group">
              <label class="control-label">Interested for Blood Donation sometime ?</label><br>
              <input name="can_donate_blood" class="" type="radio" value="yes" <?php if(isset($_SESSION['fmember']['can_donate_blood'])&&$_SESSION['fmember']['can_donate_blood']=="yes") echo "checked"?> ><label class="control-label"> Yes</label>
              <input name="can_donate_blood" class="" type="radio" value="no" <?php if(isset($_SESSION['fmember']['can_donate_blood'])&&$_SESSION['fmember']['can_donate_blood']=="no") echo "checked"?> ><label class="control-label"> No</label>
            </div>
            <div class="form-group">
              <label class="control-label">Interested for WRI Volunteership ?</label><br>
              <input name="is_volunteer" class="" type="radio" value="yes" <?php if(isset($_SESSION['fmember']['is_volunteer'])&&$_SESSION['fmember']['is_volunteer']=="yes") echo "checked"?> ><label class="control-label"> Yes</label>
              <input name="is_volunteer" class="" type="radio" value="no" <?php if(isset($_SESSION['fmember']['is_volunteer'])&&$_SESSION['fmember']['is_volunteer']=="no") echo "checked"?> ><label class="control-label"> No</label>
            </div>
            <div class="row">
              <div class="pull-right" style="margin-right: 15px;">
                <button type="submit" name="save" class="btn btn-primary btn-block btn-flat">Save</button>
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

<?php mysqli_close($conn); ?>