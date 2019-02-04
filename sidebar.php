

<div class="left-sidebar-menu">
               <div class="user-data">
                  <img class="user-dp" src="images/logo.png" alt="WRI Logo">
                  <b class="user-name">We Are Indians</b>
                  <span class="user-pos">@NGO Admin</span>
               </div>
               <ul class="sidebar-menu-items">
                  <li><a <?php if(basename($_SERVER['PHP_SELF'])=="search.php") echo 'class="active-link"'; ?> href="search.php">Search</a></li>
                  <li><a <?php if(basename($_SERVER['PHP_SELF'])=="overview.php") echo 'class="active-link"'; ?> href="overview.php">Overview</a></li>
                  <li><a <?php if(preg_match('/^add_person_s[123]{1}.php$/',basename($_SERVER['PHP_SELF']))) echo 'class="active-link"'; ?> href="add_person_s1.php">Add Donor</a></li>
                  <li><a <?php if(basename($_SERVER['PHP_SELF'])=="add_family.php") echo 'class="active-link"'; ?> href="add_family.php">Add Family</a></li>
                  <li><a <?php if(basename($_SERVER['PHP_SELF'])=="add_donation.php") echo 'class="active-link"'; ?> href="add_donation.php">Add Donation</a></li>
                  <li><a <?php if(basename($_SERVER['PHP_SELF'])=="upcoming_bday.php") echo 'class="active-link"'; ?> href="upcoming_bday.php">New B'day</a></li>
                  <li><a href="logout.php">Logout</a></li>
               </ul>
</div>