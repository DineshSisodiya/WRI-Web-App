<?php

require_once('operations/sessions.php');
require_once('operations/DBconfig.php');
require_once('operations/validations.php');

$page=1;//default page number
$resultLimit=4;
$total_page=array('donors'=>0,'family'=>0);
$total_results=array();

$response = null;
$donors_data = null;
$family_data = null;

$query_str = null;
$query_type = null;

$show_donors = false;
$show_family = false;

if (isset($_GET['search'])) {
     if ($_SERVER['REQUEST_METHOD']=='GET') {
        if (!empty($_GET['query_str']) &&
            !empty($_GET['query_type']) ) {

            $query_str=mysqli_real_escape_string($conn,trim($_GET['query_str']));
            $query_type=mysqli_real_escape_string($conn,trim($_GET['query_type']));

            // $sqld = "SELECT first_name, last_name, dob, mobile, whatsapp, email, blood_group, can_donate_blood, is_volunteer, joined_by_name, joined_by_mail, active, area, tehsil, district, state FROM donors ";
            $sqld = "SELECT d.first_name, d.last_name, d.dob, d.mobile, d.whatsapp, d.email, d.blood_group, d.can_donate_blood, d.is_volunteer, d.joined_by_name, d.joined_by_mail, d.active, d.area, d.tehsil, d.district, d.state, dn.don_amount as amount, dn.payment_mode as mode, dn.don_period as period, max(dn.on_date) as on_date FROM donors d inner join donation dn on d.mobile = dn.don_id ";
            $sqlf = "SELECT f.don_mobile as dmobile, f.first_name, f.last_name, f.dob, f.mobile, f.blood_group, f.can_donate_blood, f.relation, f.is_volunteer, d.first_name as dfname, d.last_name as dlname, d.joined_by_name, d.joined_by_mail, d.active, d.area, d.tehsil, d.district, d.state FROM `family` f INNER JOIN `donors` d ON f.don_mobile=d.mobile ";

            $response = "No details found matching with your Query <br>".$query_type." is ".$query_str."<br>You can Search other query.";

            if (preg_match('/^[a-zA-Z_]{2,30}$/', $query_type)) {
              $exp_msg="Oops! Nothing here like you have asked me to search. Please ask me to search something else related to select the list.";
              try {
                if($query_type=='mobile') {
                  if(preg_match('/^[0-9]{10}$/', $query_str)) {
                      $response=null;
                      $sqld .= "WHERE mobile='".$query_str."'";
                      $sqlf .= "AND f.mobile='".$query_str."'";
                  } else {
                    throw new Exception($exp_msg, 1);
                  } 
                } else if ($query_type=='first_name') {
                  if (preg_match('/^[a-zA-Z]{2,100}$/', $query_str)) {
                      $response=null;
                      $sqld .= "WHERE LOWER(first_name) LIKE LOWER('".$query_str."%')";
                      $sqlf .= "AND LOWER(f.first_name) LIKE LOWER('".$query_str."%')";
                  } else {
                    throw new Exception($exp_msg, 1);
                  } 
                } else if($query_type=='area') {
                  if (preg_match('/^[a-zA-Z0-9 ]{2,150}$/', $query_str)) {
                      $response=null;
                      $sqld .= "WHERE LOWER(area) LIKE LOWER('%".$query_str."%')";
                      $sqlf .= "AND LOWER(d.area) LIKE LOWER('%".$query_str."%')";
                  } else {
                    throw new Exception($exp_msg, 1);
                  } 
                } else if ($query_type=='tehsil') {
                  if (preg_match('/^[a-zA-Z]{2,100}$/', $query_str)) {
                      $response=null;
                      $sqld .= "WHERE LOWER(tehsil) LIKE LOWER('".$query_str."%')";
                      $sqlf .= "AND LOWER(d.tehsil) LIKE LOWER('".$query_str."%')";
                  } else {
                    throw new Exception($exp_msg, 1);
                  }
                } else if($query_type=='district') {
                  if(preg_match('/^[a-zA-Z]{2,150}$/', $query_str)) {
                      $response=null;
                      $sqld .= "WHERE LOWER(district) LIKE LOWER('".$query_str."%')";
                      $sqlf .= "AND LOWER(d.district) LIKE LOWER('".$query_str."%')";
                  } else {
                    throw new Exception($exp_msg, 1);
                  }
                } else if ($query_type=='state') {
                  if(preg_match('/^[a-zA-Z]{2,150}$/', $query_str)) { 
                      $response=null;
                      $sqld .= "WHERE LOWER(state) LIKE LOWER('".$query_str."%')";
                      $sqlf .= "AND LOWER(d.state) LIKE LOWER('".$query_str."%')";
                  } else {
                    throw new Exception($exp_msg, 1);
                  }
                } else if($query_type=='blood_group') {
                  if(preg_match("/^([ABO][+-])|([A][B][+-])|([abo][+-])|([a][b][+-])$/",$query_str)) { 
                      $response=null;
                      $sqld .= "WHERE LOWER(blood_group)=LOWER('".$query_str."')";
                      $sqlf .= "AND LOWER(f.blood_group)=LOWER('".$query_str."')";
                  } else {
                    throw new Exception($exp_msg, 1);
                  }
                }

                $sqld .= ' group by d.mobile order by d.mobile asc';
                $sqlf .= ' order by `don_mobile` asc';

                $donors_data = mysqli_query($conn,$sqld);

                if($donors_data) {
                  $num_rows=mysqli_num_rows($donors_data);
                  $total_results['donors'] = $num_rows;
                  $total_page['donors']=ceil($num_rows/$resultLimit);

                  if ($total_results['donors']>0) {
                    $show_donors = true;
                  }

                  if (isset($_GET['page']) and preg_match('/^\d*$/', $_GET['page']) and $_GET['page'] > 0){
                     $page=$_GET['page'];
                  } else {
                    $page=1;
                  }
                  
                  $resultsOffset=($page*$resultLimit)-$resultLimit;
                  if($resultsOffset<0)
                      $resultsOffset=0;
                  if($resultsOffset<$total_results['donors']) {
                    $show_donors = true;
                    $sqld .=" LIMIT ".$resultsOffset.",".$resultLimit;
                  } else {
                    $show_donors = false;
                  }

                  $family_data = mysqli_query($conn,$sqlf);

                  if ($family_data) {
                    $num_rows=mysqli_num_rows($family_data);
                    $total_results['family'] = $num_rows;
                    $total_page['family']=ceil($num_rows/$resultLimit);

                    if($total_results['family']>0) {
                      $show_family=true;
                    }

                    $resultsOffset=($page*$resultLimit)-$resultLimit;

                    if($resultsOffset<0)
                      $resultsOffset=0;
                    if($resultsOffset<$total_results['family']) {
                      $show_family = true;
                      $sqlf .=" LIMIT ".$resultsOffset.",".$resultLimit;
                    } else {
                      $show_family = false;
                    }

                    if($total_results['donors']==0 and $total_results['family']==0) {
                      $show_donors = false;
                      $show_family = false;
                      throw new Exception($exp_msg, 1);
                    }

                    if ($show_donors) {
                      $donors_data = mysqli_query($conn,$sqld);
                      if (!$donors_data) {
                        throw new Exception(mysqli_error(), 1);
                      }
                    }
                    if ($show_family) {
                        $family_data = mysqli_query($conn,$sqlf);
                        if (!$family_data) {
                          throw new Exception(mysqli_error(), 1);
                        }
                    }
                    
                  } else {
                    throw new Exception(mysqli_error(), 1);
                  }

                } else {
                  throw new Exception(mysqli_error(), 1);
                }
              } catch(Exception $exp) {
                $response = $exp->getMessage();
              }
            } else {
              $response = "No details found matching with your Query <br>".$query_type." is ".$query_str;
            }
        } else {
          $response = "Please Enter Search Query and Select an option";
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
  <title>Search Page</title>
  
  <link rel='stylesheet prefetch' href='css/bootstrap/bootstrap.min.css'>
  <link rel="stylesheet" type="text/css" href="jquery-ui/jquery-ui.min.css">

  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/search.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

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
          <h2 class="title">Search Your Query</h2><hr>
          <!-- <form class="update-detail-form">
          </form> -->
          <div class="search-form-container">
            <form class="form-inline search-form" action="/WRI/search.php" method="GET" accept="charset UTF-8">
              <div class="form-group search-form-left">
                <input type="text" class="form-control" id="query" placeholder="Enter Search Query" name="query_str" style="margin-left: 5px;" autofocus required="">
              </div>
              <div class="form-group search-form-right"> 
                  <select class="form-control" name="query_type" required>
                    <option value="mobile">as Mobile</option>
                    <option value="first_name">as First Name</option>
                    <option value="area">as Area</option>
                    <option value="tehsil">as Tehsil</option>
                    <option value="district">as District</option>
                    <option value="state">as State</option>
                    <option value="blood_group">as Blood Group</option>
                  </select>
                  <button type="submit" name="search" value="1" class="btn btn-default">Search</button>
              </div>
            </form>
          </div>
          <hr>
          <div class="search-results">
            <?php 
              if (!empty($response)) {
                echo '<div class="form-response">'. $response .'</div>';
              }
              if ($show_family or $show_donors) {
            ?>
                <div class="icon-description">
                  Meaning of Symbols used here<br>
                  <i class="glyphicon glyphicon-user"> ⇢ Person</i>
                  <i class="glyphicon glyphicon-link"> ⇢ Volunteer</i>
                  <i class="glyphicon glyphicon-tint"> ⇢ Blood</i>
                  <i class="glyphicon glyphicon-log-in"> ⇢ Id Active</i>
                  <i class="glyphicon fas fa-donate"> ⇢ Donation</i>
                  <i class="glyphicon glyphicon-info-sign"> ⇢ Relation</i>
                </div>

            <?php 
              } else if(empty($response)) {
                echo '<br><h4 style="text-align:center;">Please enter the query you would like to search.</h4><br><h5 style="text-align:center;">Then your results will be displayed here.</h5>';
              }//end of upper if else

              if($show_donors) {
                echo '<span style="color: #555;display: block;margin-top: 15px;">'. $total_results['donors'] .' Donors Profile Found</span><hr>'; 

                while ($person = mysqli_fetch_assoc($donors_data)) {
                  $yes = '<i class="glyphicon glyphicon-ok-sign green"></i>';
                  $no = '<i class="glyphicon glyphicon-remove-sign red"></i>';
                  $name=ucfirst($person["first_name"]).' '.$person["last_name"];
                  $dob = getdate(strtotime($person['dob']));
                  $bday = $dob['mday'].', '.$dob['month'].', '.$dob['year'];

                  $is_volunteer = ($person['is_volunteer']=="yes")?'<i class="glyphicon glyphicon-link"></i>: Yes'.$yes : '<i class="glyphicon glyphicon-link"></i></i>: No'.$no;
                  $is_active = ($person['active']=="yes")?'<i class="glyphicon glyphicon-log-in"></i>: Yes'.$yes:'<i class="glyphicon glyphicon-log-in"></i>: No'.$no;
                  $can_donate_blood = ($person['can_donate_blood']=="yes")?$yes:$no; 

                  $out = '<div class="profile-card">
                            <div class="card-data">
                              <i class="glyphicon glyphicon-user"></i>: <b style="font-weight:600">'.$name.'</b><hr>
                              <div class="attr-bg">
                                '.$is_volunteer.'
                              </div>
                              <div class="attr-bg" style="background:#eae8e8">
                                '.$is_active.'
                              </div>
                              <div class="attr-bg">
                                <i class="glyphicon glyphicon-tint"></i>: '.$person['blood_group'].$can_donate_blood.'
                              </div>
                              <hr>
                              <i class="glyphicon fas fa-birthday-cake"></i> : <span>'.$bday.'</span><hr>
                              <i class="glyphicon glyphicon-earphone"></i>: <span>'.$person['mobile'].'</span>
                              <i class="glyphicon fab fa-whatsapp" style="font-size: large;font-weight: 600;"></i>: <span>'.$person['whatsapp'].'</span><hr>
                              <i class="glyphicon glyphicon-envelope"></i>: <span>'.$person['email'].'</span><hr>
                              <i class="glyphicon glyphicon-home"></i>: <span class="align-right">'.$person['area'].'</span><hr>
                              <i class="glyphicon glyphicon-map-marker"></i>: <span>'.$person['district'].', '.$person['state'].'</span><hr>
                              <i class="glyphicon fas fa-donate"></i>: 
                                <table style="margin-left: 46px;margin-top: -22px;font-size: 14px;font-weight: 20;">
                                  <tbody> 
                                    <tr><td>'.$person['amount'].' Rs by '.ucfirst($person['mode']).'</td></tr>
                                    <tr style="font-size: 13px;"><td>on '.$person['on_date'].'</td></tr>
                                    <tr style="font-size: 13px;"><td>Subscription : '.ucfirst($person['period']).'</td></tr>
                                  </tbody>
                                 </table><hr>
                              <span>Joined By<i class="glyphicon glyphicon-user"></i>: </span><span>'.$person['joined_by_name'].'</span><hr>
                              <span>Joined By<i class="glyphicon glyphicon-envelope"></i>: </span><span>'.$person['joined_by_mail'].'</span>
                            </div>
                          </div>';
                  echo $out;
                }
              }


              if($show_family) {
                echo '<span style="color: #555;display: block;margin-top: 15px;">'. $total_results['family'] .' Members Profile Found</span><hr>';

                while ($person = mysqli_fetch_assoc($family_data)) {
                  $yes = '<i class="glyphicon glyphicon-ok-sign green"></i>';
                  $no = '<i class="glyphicon glyphicon-remove-sign red"></i>';
                  $name=ucfirst($person["first_name"]).' '.$person["last_name"];
                  $dob = getdate(strtotime($person['dob']));
                  $bday = $dob['mday'].', '.$dob['month'].', '.$dob['year'];

                  $is_volunteer = ($person['is_volunteer']=="yes")?'<i class="glyphicon glyphicon-link"></i>: Yes'.$yes : '<i class="glyphicon glyphicon-link"></i>: No'.$no;
                  $is_active = ($person['active']=="yes")?'<i class="glyphicon glyphicon-log-in"></i>: Yes'.$yes:'<i class="glyphicon glyphicon-log-in"></i>: No'.$no;
                  $can_donate_blood = ($person['can_donate_blood']=="yes")?$yes:$no; 

                  $out = '<div class="profile-card">
                            <div class="card-data">
                              <i class="glyphicon glyphicon-user"></i>: <b style="font-weight:600">'.$name.'</b><hr>
                              <div class="attr-bg">
                                '.$is_volunteer.'
                              </div>
                              <div class="attr-bg" style="background:#eae8e8">
                                '.$is_active.'
                              </div>
                              <div class="attr-bg">
                                <i class="glyphicon glyphicon-tint"></i>: '.$person['blood_group'].$can_donate_blood.'
                              </div>
                              <hr>
                              <i class="glyphicon fas fa-birthday-cake"></i>: <span>'.$bday.'</span><hr>
                              <i class="glyphicon glyphicon-earphone"></i>: <span>'.$person['mobile'].'</span><hr>
                              <i class="glyphicon glyphicon-home"></i>: <span class="align-right">'.$person['area'].'</span><hr>
                              <i class="glyphicon glyphicon-map-marker"></i>: <span>'.$person['district'].', '.$person['state'].'</span><hr>
                              <i class="glyphicon glyphicon-info-sign"></i>: <span">'.ucfirst($person['relation']).' of '.ucfirst($person['dfname']).' '.$person['dlname'].'</span><br>
                              <i class="glyphicon glyphicon-earphone"></i>: <span>'.$person['dmobile'].'</span><hr>
                              <span>Joined By<i class="glyphicon glyphicon-user"></i>: </span><span>'.$person['joined_by_name'].'</span><hr>
                              <span>Joined By<i class="glyphicon glyphicon-envelope"></i>: </span><span>'.$person['joined_by_mail'].'</span>
                            </div>
                          </div>';
                  echo $out;
                }
              }
            ?>

            <div style="width: 80%;text-align: center;padding: 15px 0;"> 
                <ul class="pagination">
                  <?php
                   $pages = ($total_page['donors']>$total_page['family'])?$total_page['donors']:$total_page['family'];
                   if($pages>1) {
                       for($i=1; $i<=$pages; $i++) { 
                        $url = 'search.php?page='.$i.'&query_str='.urlencode($query_str).'&query_type='.urlencode($query_type).'&search=1';
                  ?>
                          <li <?php if($i==$page) { echo  "class='active'"; } ?> ><a href="<?php echo $url; ?>"><?php echo $i; ?></a></li>
                 <?php
                       }
                    }  
                 ?>
                </ul>    
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>