<?php
function validateInput($inp) {
  if(!preg_match("/^[a-zA-Z0-9 ]*$/",$inp)) {
    return 0;
  }
  return 1;
}

function validateDOB($inp, $min_age=1) {
  if(preg_match("/^(19[0-9]{2}|2[0-9]{3})[-]([0][1-9]|[1][0-2])[-]([0-2][1-9]|[3][0-1])$/", $inp)) {
    $dob = strtotime($inp);
    $today = strtotime(date("Y-m-d"));
    if($dob > $today) {
      // date greater than today will be invalid
      return false;
    }
    $dob=date_create(date('Y-m-d', $dob));
    $today=date_create(date("Y-m-d"));

    $age_diff=date_diff($today,$dob);
    if( $age_diff->y >=$min_age ) {
      return true;
    }
  }
  return false;
}

function validateMobileNumber($inp) {
  if (preg_match("/^([1-9][0-9]{9})$/", $inp))
    return true;
  return false;
}

function validateEmail($inp)
{
  if(filter_var($inp, FILTER_VALIDATE_EMAIL))
    return true;
  return false;
}

function validateBloodGroup($inp)
{
  if(preg_match("/^([ABO][+-])|[A][B][+-]$/",$inp))
    return true;
  return false;
}

?>