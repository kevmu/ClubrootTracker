<?php
    require('db.php');
    
    session_start();
    
    $message = "";
    $result = "";

    $email = "";
    $firstname = "";
    $lastname = "";
    $academictitle = "";
    $name = "";
	$termsandconditions = ""; 
	$length_of_string = 10;
    $user_id = substr(bin2hex(random_bytes($length_of_string)), 0, $length_of_string);
  
    //echo $con . " " . "TESTING\n";
    // If form submitted, insert values into the database.
    if (isset($_REQUEST['email'])){
        
		$user_id = pg_escape_string($con,$user_id);
		
		// removes backslashes
        $email = stripslashes($_REQUEST['email']);
        $email = pg_escape_string($con,$email);
        
        $firstname = stripslashes($_REQUEST['firstname']);
        //escapes special characters in a string
        $firstname = pg_escape_string($con,$firstname);
        
        $lastname = stripslashes($_REQUEST['lastname']);
        //escapes special characters in a string
        $lastname = pg_escape_string($con,$lastname);
        
        $academictitle = stripslashes($_REQUEST['academictitle']);
        $academictitle = pg_escape_string($con,$academictitle);
        
        $institution = stripslashes($_REQUEST['institution']);
        $institution = pg_escape_string($con,$institution);
        
        $country = stripslashes($_REQUEST['country']);
        $country = pg_escape_string($con,$country);
        
        $password = stripslashes($_REQUEST['password']);
        $password = pg_escape_string($con,$password);
        
        $confirm = stripslashes($_REQUEST['confirm']);
        $confirm = pg_escape_string($con,$confirm);
        
        $termsandconditions = stripslashes($_REQUEST['termsandconditions']);
        $termsandconditions = pg_escape_string($con,$termsandconditions);
		
        $trn_date = date("Y-m-d H:i:s");
        //Checking is user existing in the database or not
        $account_query = "SELECT * FROM users WHERE email='$email'";
        $account_result = pg_query($con,$account_query) or die(pg_last_error());
        $rows = pg_num_rows($account_result);
        
        if($rows == 1){
            $message = "An account exists that is registered to the following email address.  $email. Please register using another email address.";
            $email = "";
        }else{
        
            if($password == $confirm){
                $query = "INSERT into users (user_id, email, firstname, lastname, academictitle, institution, country, password, termsandconditions, trn_date)
                VALUES ('$user_id', '$email', '$firstname', '$lastname', '$academictitle', '$institution', '$country', '".md5($password)."', '$termsandconditions', '$trn_date')";
                $result = pg_query($con,$query);
                
            }else{
                $message = "Passwords do not match!";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">

<title>ClubrootTracker: a leaflet.js map tool for tracking Plasmodiophora brassicae (Clubroot)</title>

<!-- Bootstrap Core CSS -->
<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<!-- DataTables CSS -->
<link href="vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">

<!-- DataTables Responsive CSS -->
<link href="vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">

<!-- Custom CSS -->
<link href="dist/css/sb-admin-2.css" rel="stylesheet">

<!-- Custom Fonts -->
<link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>

<div id="wrapper">

<?php
	   include 'nav_menu.php';
?>
<div id="page-wrapper">
<div class="row">
<div class="col-lg-12">
<h1 class="page-header">REGISTRATION PORTAL</h1>
</div>
<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
<div class="col-lg-12">
<div id="registration_panel" class="panel panel-default">
<div class="panel-body">
<?php

if($result){
    
    $name = $firstname . ' ' . $lastname;
    
    if($academictitle != 'None'){
        $name = $academictitle . ' ' . $name;
    }
    
    $email_body = '$name, \n\nWelcome to ClubrootTracker! \n\nYour registration request has been recieved and confirmed! \n\nYour username is' . ' ' . $email . '.' . ' ' . 'Please login at http://www.cpnclassiphyr.ca/login.php. \n\nIf you require assistance please contact us using the following email address help.cpnclassiphyr@gmail.com. \n\nPlease do not reply to this email. \n\nCheers, \n\nThe ClubrootTracker curator';
    
    //$automailerCommand = 'echo "' . $email_body . '" | mail -s "ClubrootTracker Registration Confirmation"' . ' ' . $email;
    
    //echo $automailerCommand;
    //$output = shell_exec($automailerCommand);
    /*
    echo "<div class='form-group'>
    <h3>You have been registered successfully!</h3>
    <br>
    
    <p>Welcome $name!</p>
    <br>
    <p>Confirmation of your registration has been sent to the following email address;</p>
    <br>
    <p>$email</p>
    <br>
    <p>Click here to <a href='login.php'>Login</a></p>
    </div>";
	*/
	echo "<div class='form-group'>
    <h3>You have been registered successfully!</h3>
    <br>
	<p>Welcome $name!</p>
	<br>
    <p>Welcome to ClubrootTracker! Your registration request has been recieved and confirmed!</p>
	<p>Your username is $email. Click here to <a href='login.php'>Login</a></p>
	<p>If you require assistance please contact us using the following email address help.clubroottracker@gmail.com</p>
    <br>
	<p>Cheers,</p>
	<br>
	<p>The ClubrootTracker curator</p>
	
    </div>";
	
}
else{

?>

<div class="form-group">
<form name="registration" action="" method="post">
<label>Email Address<font color="red">*</font></label>
<input class="form-control" type="email" name="email" value="
<?php
    echo $email;
?>
" required />
<br>
<label>First Name<font color="red">*</font></label>
<input class="form-control" type="text" name="firstname" value="
<?php
    echo $firstname;
?>
" required />
<br>
<label>Last Name<font color="red">*</font></label>
<input class="form-control" type="text" name="lastname" value="
<?php
    echo $lastname;
    ?>
" required />
<br>

<label>Academic Title</label>
<select class="form-control" name="academictitle">
<option value="None"></option>
<option value="Prof. Dr.">Prof. Dr.</option>
<option value="Dr.">Dr.</option>
<option value="Prof.">Prof.</option>
</select>

<br>
<label>Institution</label>
<input class="form-control" type="text" name="institution" />

<br>
<label>Location</label>
<select class="form-control" name="country">
option value="AF">Afghanistan</option><option value="AL">Albania</option><option value="DZ">Algeria</option><option value="VI">Amer.Virgin Is.</option><option value="AD">Andorra</option><option value="AO">Angola</option><option value="AI">Anguilla</option><option value="AQ">Antarctica</option><option value="AG">Antigua/Barbuda</option><option value="AR">Argentina</option><option value="AM">Armenia</option><option value="AW">Aruba</option><option value="AU">Australia</option><option value="AT">Austria</option><option value="AZ">Azerbaijan</option><option value="BS">Bahamas</option><option value="BH">Bahrain</option><option value="BD">Bangladesh</option><option value="BB">Barbados</option><option value="BY">Belarus</option><option value="BE">Belgium</option><option value="BZ">Belize</option><option value="BJ">Benin</option><option value="BM">Bermuda</option><option value="BT">Bhutan</option><option value="BO">Bolivia</option><option value="BA">Bosnia-Herz.</option><option value="BW">Botswana</option><option value="BV">Bouvet Island</option><option value="BR">Brazil</option><option value="IO">Brit.Ind.Oc.Ter</option><option value="VG">Brit.Virgin Is.</option><option value="BN">Brunei</option><option value="BG">Bulgaria</option><option value="BF">Burkina-Faso</option><option value="BI">Burundi</option><option value="KH">Cambodia</option><option value="CM">Cameroon</option><option value="CA" selected>Canada</option><option value="CV">Cape Verde</option><option value="KY">Cayman Islands</option><option value="CF">Central Afr.Rep</option><option value="E3">Central America and Caribbean</option><option value="TD">Chad</option><option value="CL">Chile</option><option value="CN">China</option><option value="CX">Christmas Is.</option><option value="CC">Coconut Islands</option><option value="CO">Colombia</option><option value="KM">Comoros</option><option value="CD">Congo</option><option value="CG">Congo</option><option value="CK">Cook Islands</option><option value="CR">Costa Rica</option><option value="HR">Croatia</option><option value="CU">Cuba</option><option value="CY">Cyprus</option><option value="CZ">Czech Republic</option><option value="DK">Denmark</option><option value="DJ">Djibouti</option><option value="DM">Dominica</option><option value="DO">Dominican Rep.</option><option value="AN">Dutch Antilles</option><option value="TP">East Timor</option><option value="EC">Ecuador</option><option value="EG">Egypt</option><option value="SV">El Salvador</option><option value="GQ">Equatorial Guin</option><option value="ER">Eritrea</option><option value="EE">Estonia</option><option value="ET">Ethiopia</option><option value="FK">Falkland Islnds</option><option value="FO">Faroe Islands</option><option value="FJ">Fiji</option><option value="FI">Finland</option><option value="FR">France</option><option value="PF">Frenc.Polynesia</option><option value="GF">French Guayana</option><option value="TF">French S.Territ</option><option value="GA">Gabon</option><option value="GM">Gambia</option><option value="GE">Georgia</option><option value="DE">Germany</option><option value="GH">Ghana</option><option value="GI">Gibraltar</option><option value="GR">Greece</option><option value="GL">Greenland</option><option value="GD">Grenada</option><option value="GP">Guadeloupe</option><option value="GU">Guam</option><option value="GT">Guatemala</option><option value="GN">Guinea</option><option value="GW">Guinea-Bissau</option><option value="GY">Guyana</option><option value="HT">Haiti</option><option value="HM">Heard/McDon.Isl</option><option value="HN">Honduras</option><option value="HK">Hong Kong</option><option value="HU">Hungary</option><option value="IS">Iceland</option><option value="IN">India</option><option value="ID">Indonesia</option><option value="IR">Iran</option><option value="IQ">Iraq</option><option value="IE">Ireland</option><option value="IL">Israel</option><option value="IT">Italy</option><option value="CI">Ivory Coast</option><option value="JM">Jamaica</option><option value="JP">Japan</option><option value="JO">Jordan</option><option value="KZ">Kazakhstan</option><option value="KE">Kenya</option><option value="KI">Kiribati</option><option value="KR">Korea (South)</option><option value="KW">Kuwait</option><option value="KG">Kyrgyzstan</option><option value="LA">Laos</option><option value="LV">Latvia</option><option value="LB">Lebanon</option><option value="LS">Lesotho</option><option value="LR">Liberia</option><option value="LY">Libya</option><option value="LI">Liechtenstein</option><option value="LT">Lithuania</option><option value="LU">Luxembourg</option><option value="MO">Macau</option><option value="MK">Macedonia</option><option value="MG">Madagascar</option><option value="MW">Malawi</option><option value="MY">Malaysia</option><option value="MV">Maldives</option><option value="ML">Mali</option><option value="MT">Malta</option><option value="MH">Marshall Islnds</option><option value="MQ">Martinique</option><option value="MR">Mauretania</option><option value="MU">Mauritius</option><option value="YT">Mayotte</option><option value="MX">Mexico</option><option value="FM">Micronesia</option><option value="UM">Minor Outl.Isl.</option><option value="MD">Moldavia</option><option value="MC">Monaco</option><option value="MN">Mongolia</option><option value="MS">Montserrat</option><option value="MA">Morocco</option><option value="MZ">Mozambique</option><option value="MM">Myanmar</option><option value="MP">N.Mariana Islnd</option><option value="NA">Namibia</option><option value="NR">Nauru</option><option value="NP">Nepal</option><option value="NL">Netherlands</option><option value="NC">New Caledonia</option><option value="NZ">New Zealand</option><option value="NI">Nicaragua</option><option value="NE">Niger</option><option value="NG">Nigeria</option><option value="NU">Niue Islands</option><option value="NF">Norfolk Island</option><option value="KP">North Korea</option><option value="NO">Norway</option><option value="OM">Oman</option><option value="OTH">Other Countries</option><option value="E2">Other South American Countries</option><option value="PK">Pakistan</option><option value="PW">Palau</option><option value="PA">Panama</option><option value="PG">Pap. New Guinea</option><option value="PY">Paraguay</option><option value="PE">Peru</option><option value="PH">Philippines</option><option value="PN">Pitcairn Islnds</option><option value="PL">Poland</option><option value="PT">Portugal</option><option value="PR">Puerto Rico</option><option value="QA">Qatar</option><option value="RE">Reunion</option><option value="RO">Romania</option><option value="RU">Russian Fed.</option><option value="RW">Rwanda</option><option value="ST">S.Tome,Principe</option><option value="AS">Samoa, American</option><option value="SM">San Marino</option><option value="SA">Saudi Arabia</option><option value="SN">Senegal</option><option value="RS">Serbia</option><option value="SC">Seychelles</option><option value="SL">Sierra Leone</option><option value="SG">Singapore</option><option value="SK">Slovakia</option><option value="SI">Slovenia</option><option value="SB">Solomon Islands</option><option value="SO">Somalia</option><option value="ZA">South Africa</option><option value="ES">Spain</option><option value="LK">Sri Lanka</option><option value="KN">St Kitts&amp;Nevis</option><option value="SH">St. Helena</option><option value="LC">St. Lucia</option><option value="VC">St. Vincent</option><option value="PM">St.Pier,Miquel.</option><option value="GS">Sth Sandwich Is</option><option value="SD">Sudan</option><option value="SR">Suriname</option><option value="SJ">Svalbard</option><option value="SZ">Swaziland</option><option value="SE">Sweden</option><option value="CH">Switzerland</option><option value="SY">Syria</option><option value="TW">Taiwan</option><option value="TJ">Tajikistan</option><option value="TZ">Tanzania</option><option value="TH">Thailand</option><option value="TG">Togo</option><option value="TK">Tokelau Islands</option><option value="TO">Tonga</option><option value="TT">Trinidad,Tobago</option><option value="TN">Tunisia</option><option value="TR">Turkey</option><option value="TM">Turkmenistan</option><option value="TC">Turksh Caicosin</option><option value="TV">Tuvalu</option><option value="AE">UAE</option><option value="UG">Uganda</option><option value="UA">Ukraine</option><option value="GB">United Kingdom</option><option value="UY">Uruguay</option><option value="US">USA</option><option value="UZ">Uzbekistan</option><option value="VU">Vanuatu</option><option value="VA">Vatican City</option><option value="VE">Venezuela</option><option value="VN">Vietnam</option><option value="WF">Wallis, Futuna</option><option value="EH">West Sahara</option><option value="WS">Western Samoa</option><option value="YE">Yemen</option><option value="YU">Yugoslavia</option><option value="ZM">Zambia</option><option value="ZW">Zimbabwe</option>
</select>

<br>
<label>Password<font color="red">*</font></label>
<input class="form-control" type="password" name="password" required />

<br>
<label>Confirm Password<font color="red">*</font></label>
<input class="form-control" type="password" name="confirm" required />

<br>
<input type="checkbox" name="termsandconditions" value="Agree" required >
<label for="termsandconditions">I agree to the ClubrootTracker <a href="#" data-toggle="modal" data-target="#termsandconditions-modal">Terms and Conditions</a><font color="red">*</font></label>
  
<br>
<?php
echo $message;
?>
<br>
<input class="btn btn-primary active" type="submit" name="submit" value="REGISTER" />
</form>
</div>
<?php
}
?>
</div>
</div>
</div>
<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<div>
<div id="termsandconditions-modal" class="modal fade" role="dialog">
<div class="modal-dialog modal-lg">

<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title">ClubrootTracker Terms and Conditions Agreement</h4>
</div>
<div class="modal-body">
<p> I declare that all data uploaded to the ClubrootTracker using this account has been given the proper permissions to share the data publicly. I confirm that any confidential data or data under a non-disclosure agreement (NDA) will not be uploaded to the ClubrootTracker from any commercial, industrial or independent producers that have not given prior consent to do so. I give ClubrootTracker permission to attach my credentials to each data point uploaded through this account so that I can be referenced as a contributor and contacted by email about any questions or concerns. I acknowledge that the ClubrootTracker is not responsible for any data uploaded without permission and hereby agree to all the terms and conditions of ClubrootTracker.</p>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close Window</button>
</div>
</div>
</div>

</div>

<!-- jQuery -->
<script src="vendor/jquery/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="vendor/metisMenu/metisMenu.min.js"></script>
<!-- Custom Theme JavaScript -->
<script src="dist/js/sb-admin-2.js"></script>

<!-- DataTables JavaScript -->
<script src="vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="vendor/datatables-responsive/dataTables.responsive.js"></script>
<script type="text/javascript">
$(document).ready(function() {
$('#dataTables-example').DataTable({
responsive: true
});
});
</script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

</body>

<?php
    include 'footer.php';
?>
</html>



