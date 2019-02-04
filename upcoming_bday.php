<?php

require_once('operations/sessions.php');
require_once('operations/DBconfig.php');

$response = null;

// table selection
$table = null;
if (isset($_GET['p'])) {
   $table=($_GET['p']=="family")?"family":"donors";
} else {
  $table="donors";
}

if($table=="donors") {
  $sql = "select d.first_name, d.last_name, d.dob, d.mobile, d.whatsapp, d.joined_by_name, d.is_volunteer, dn.don_amount as amount, dn.payment_mode as mode, max(dn.on_date) as on_date from donors d inner join donation dn on d.mobile = dn.don_id where to_days(date_add(d.dob, interval year(current_date) -year(d.dob) YEAR ) ) - to_days(current_date) between 0 and 7  group by d.mobile order by d.dob";
} else {
  $sql = "select `first_name`, `last_name`, `dob`, `mobile`, `is_volunteer` from `family` where to_days(date_add(dob, interval year(current_date) -year(dob) YEAR ) ) - to_days(current_date) between 0 and 7 order by `dob` asc";
}


$resultLimit=9;
$totalPage=0;
$num_rows=0;
$results=null;
$results = mysqli_query($conn, $sql);

if($results) {
  $num_rows=mysqli_num_rows($results);
  $totalPage=ceil($num_rows/$resultLimit);
} else {
  $response=mysqli_error();
}

$page=1;
if (isset($_GET['page']) and preg_match('/^\d*$/', $_GET['page']) and $totalPage >= $_GET['page'] and $_GET['page'] > 0) {
   $page=$_GET['page'];
} else {
  $page=1;
}

$resultsOffset=($page*$resultLimit)-$resultLimit;

$sql.=" LIMIT ".$resultsOffset.",".$resultLimit;

$results=null;
$results = mysqli_query($conn, $sql);
if(!$results) {
  $response=mysqli_error($conn);
}

if ($num_rows==0) {
  $response='There is no Upcoming Bday within one week';
}

?>

<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Upcoming bday of persons</title>
  
  <link rel='stylesheet prefetch' href='css/bootstrap/bootstrap.min.css'>
  <link rel="stylesheet" type="text/css" href="jquery-ui/jquery-ui.min.css">

  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet prefetch" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

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
          <h2 class="title">Upcoming B'day of Persons</h2><hr>
          <div style="height: 50px;padding: 8px 15px;background: #f5f5f5;">
            <a href="upcoming_bday.php?p=donors" class="btn <?php if($table=="donors"){echo 'btn-primary '; }else { echo 'btn-default '; } ?> pull-left">Donors</a>
            <a href="upcoming_bday.php?p=family" style="text-align:right" class="btn <?php if($table=="family"){ echo 'btn-primary '; } else {echo 'btn-default '; } ?>pull-right">Family Members</a>
          </div>
          <hr>
          <?php 
            if (!empty($response)) {
              die('<div class="form-response">'. $response .'</div>');
            }
          ?>
            <div class="icon-description">
              Meaning of Symbols used here<br>
              <i class="glyphicon glyphicon-user"> ⇢ Person</i>
              <i class="glyphicon glyphicon-link"> ⇢ Volunteer</i>
              <i class="glyphicon fas fa-birthday-cake"> ⇢ B'day</i>
              <i class="glyphicon fas fa-donate"> ⇢ Donation</i>
            </div>
            <div style="text-align: center;margin: 22px;color: #433;"">Showing Bday of Donor's<?php if($table=="family") { echo " family Members"; } ?></div>
          <div id="bday-list" style="padding:20px;">
            <?php
              while ($person = mysqli_fetch_assoc($results)) {
                $name=ucfirst($person["first_name"]).' '.$person["last_name"];
                $dob = getdate(strtotime($person['dob']));
                $bday = $dob['mday'].', '.$dob['month'];
                $is_volunteer = ($person['is_volunteer']=="yes")?'<i class="glyphicon glyphicon-link icon"></i>: Yes<i class="glyphicon glyphicon-ok-sign icon"></i>':'<i class="glyphicon glyphicon-link icon"></i>: No<i class="glyphicon glyphicon-remove-sign icon"></i>';
                if($table=="donors") {
                  $donation = '<hr>
                            <i class="fas fa-donate icon"></i>: 
                            <table style="margin-left: 46px;margin-top: -22px;">
                              <tbody> 
                                <tr><td>'.$person['amount'].' Rs</td></tr>
                                <tr style="font-size: 13px;"><td>by '.ucfirst($person['mode']).'</td></tr>
                                <tr style="font-size: 13px;"><td>on '.$person['on_date'].'</td></tr>
                              </tbody>
                             </table>';
                }

                $out = '<div class="card">
                          <div class="card-data">
                            <i class="glyphicon glyphicon-user icon"></i>: <b style="font-weight:600">'.$name.'</b>
                            <hr>
                            '.$is_volunteer.'
                            <hr>
                            <i class="glyphicon fas fa-birthday-cake icon"></i>: <span>'.$bday.'</span>
                            <hr>
                            <i class="glyphicon glyphicon-earphone icon"></i>: <span>'.$person["mobile"].'</span>
                            ';

                if(!empty($donation)) {
                    $out .= $donation;
                }
                $out .=   '</div>
                        </div>';
                // output results
                echo $out;
              }
            ?>
            <div style="width: 80%;text-align: center;padding: 15px 0;"> 
                <ul class="pagination">
                  <?php
                   if($totalPage>1) {
                       for($i=1; $i<=$totalPage; $i++) { ?>
                          <li <?php if($i==$page) { echo  "class='active'"; } ?> ><a href="upcoming_bday.php?p=<?php echo $table; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
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