<?php

require_once('operations/sessions.php');
require_once('operations/DBconfig.php');

$response = null;

$data = array();

$sql = "SELECT COUNT(mobile) as total_donors FROM donors";
$query1 = mysqli_query($conn,$sql);

$sql = "SELECT d.don_amount as highest_donation, d.received_by as receiver FROM donation d WHERE d.don_amount >= (SELECT MAX(don_amount) FROM donation)";
$query2 = mysqli_query($conn,$sql);

$sql = "SELECT SUM(don_amount) AS total_donation, COUNT(don_id) AS no_of_donation FROM donation";
$query3 = mysqli_query($conn,$sql);

$sql = "SELECT d.received_by AS receiver, MAX(d.no_of_donation) FROM (SELECT received_by, COUNT(*) as no_of_donation FROM donation GROUP BY received_by) d";
$query4 = mysqli_query($conn,$sql);

?>

<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Overview Details</title>
  
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
          <h2 class="title">Overview of all the Donors Information</h2><hr>
          <hr>
          <?php 
            if (!empty($response)) {
              die('<div class="form-response">'. $response .'</div>');
            }
          ?>
          
              <table align="center" class="overview-details">
                <tr>
                  <td>Total No. of Donors</td>
                  <td>
                    <?php $data=mysqli_fetch_assoc($query1); 
                          echo  $data['total_donors']; ?>
                  </td>
                </tr>
                <tr>
                  <td>Total No. of Donations</td>
                  <td>
                    <?php $data=mysqli_fetch_assoc($query3); 
                          echo  $data['no_of_donation']; ?>
                  </td>
                </tr>
                <tr>
                  <td>Total Donation Received</td>
                  <td>
                    <?php echo $data['total_donation'].' Rs.'; ?>
                  </td>
                </tr>
        <?php
          $data=mysqli_fetch_assoc($query2);
           echo '<tr>
                  <td>Highest Donation Taken</td>
                  <td>'.$data['highest_donation'].' Rs.</td>
                </tr>
                <tr>
                  <td>Highest Donation Receiver</td>
                  <td style="padding:5px 0;text-align:center;">';  
                    echo '<span style="padding:10px 12px;">',$data['receiver'],'</span>';
                    while ($data=mysqli_fetch_assoc($query2)) {    
                     echo '<hr><span style="padding:10px 12px;">',$data['receiver'],'</span>';
                    }
           echo   '</td>
                </tr>';
        ?>
                <tr>
                  <td>Max. No. of Donations Receiver</td>
                  <td>
                    <?php $data=mysqli_fetch_assoc($query4); 
                          echo $data['receiver']; ?>
                  </td>
                </tr>
              </table>
              <div style="text-align:center;margin:35px auto;">
                Click Here to >> <a href="export_report.php" title="Please Use only when Required">Export Full Donation Report</a>
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