<?php
    
    require('db.php');

    session_start();
    
    // If form submitted, insert values into the database.
    $message = "";
    $email = "";
    $password = "";
    if (isset($_POST['email'])){
		
        // removes backslashes
        $email = stripslashes($_REQUEST['email']);
        //escapes special characters in a string
        $email = pg_escape_string($con,$email);
        
        $password = stripslashes($_REQUEST['password']);
        $password = pg_escape_string($con,$password);
        
        //Checking is user existing in the database or not
        $query = "SELECT * FROM users WHERE email='$email'
        and password='" . md5($password) . "'";
        
        $result = pg_query($con,$query) or die(pg_last_error());
        $rows = pg_num_rows($result);
        
        echo "$result";
        if($rows == 1){
            
            
            $row = pg_fetch_row($result);
            
			$id = $row[0];
			$_SESSION['id'] = $id;
			
			$user_id = $row[1];
			$_SESSION['user_id'] = $user_id;
			
			$email = $row[2];
			$_SESSION['email'] = $email;
			
            $firstname = $row[3];
            $_SESSION['firstname'] = $firstname;
            
            $lastname = $row[4];
            $_SESSION['lastname'] = $lastname;
            
            $academictitle = $row[5];
            $_SESSION['academictitle'] = $academictitle;
            
            $institution = $row[6];
			$_SESSION['institution'] = $institution;
            
			$country = $row[7];
            $_SESSION['country'] = $country;
            
            // Redirect user to index.php
            header("Location: dashboard.php");
        }
        else{
            $message = "Username/password is incorrect";
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
                        <h1 class="page-header">LOGIN PORTAL</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-12">
						<div id="login_panel" class="panel panel-default">
							<div class="panel-body">
<?php

    if(!(isset($_POST['email']))){
?>
            <div class="form-group">
				<form action="" method="post" name="login">
				<label>Email Address</label>
				<input class="form-control" type="text" name="email" required />
				<br>
				
				<label>Password</label>
				<input class="form-control" type="password" name="password" required />
				<br>
				<input class="btn btn-primary active" type="submit" name="submit"  value="LOGIN"/>
				</form>
				<br>
				<p>Not registered yet? <a href='registration.php'>Register Here</a></p>
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
