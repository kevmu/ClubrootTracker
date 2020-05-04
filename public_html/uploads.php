<?php
	require('db.php');
	include("auth.php");
    
    $message = "";
    $result = "";
    
	$user_id = $_SESSION['user_id'];
    $email = $_SESSION['email'];
    $firstname = $_SESSION['firstname'];
    $lastname = $_SESSION['lastname'];
    $academictitle = $_SESSION['academictitle'];
    
    $name = $firstname . ' ' . $lastname;
    
    if($academictitle != 'None'){
        $name = $academictitle . ' ' . $name;
    }

    

	function isCSV( $file ){
		$csv_mime_types = [ 
			'text/csv',
			'text/plain',
			'application/csv',
			'text/comma-separated-values',
			'application/excel',
			'application/vnd.ms-excel',
			'application/vnd.msexcel',
			'text/anytext',
			'application/octet-stream',
			'application/txt',
		];
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$mime_type = finfo_file( $finfo, $file );

		return in_array( $mime_type, $csv_mime_types );
	}

    $uploads_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads';

    if (!file_exists($uploads_dir)) {
        //        echo "The file $uploads_dir exists";
        mkdir($uploads_dir);
    }
    
    $time = time();
    
    $output_dir = $_SERVER['DOCUMENT_ROOT'] . '/output_dir';
	
    if (!file_exists($output_dir)) {
        //        echo "The file $output_dir xists";
        mkdir($output_dir);
    }
    
    $current_output_dir = $output_dir . '/' . 'output_dir' . '_' . $user_id . '_' . $time;
    if (!file_exists($current_output_dir)) {
        //        echo "The file $output_dir exists";
        mkdir($current_output_dir);
    }
    
    $info = pathinfo($_FILES['file']['name']);
	
    $ext = $info['extension']; // get the extension of the file
    $filename = $_FILES['file']['name'];
    $target_filepath = $uploads_dir . '/' . $user_id . '-' . $time . '-' . $filename;
    
    //echo '<br>' . $target_path;
    //echo '<br>' . $_FILES['file']['tmp_name'];
    move_uploaded_file($_FILES['file']['tmp_name'], $target_filepath);
    //echo '<br>' . $target_filepath . '<br>';
	
	
    //echo '<br>' . isCSV($target_filepath) . '<br>';
    /*if(isCSV($target_filepath)){
		
        echo "The file $filename has been uploaded sucessfully!" . "<br>";
		echo "Running generate_geojson.py on input file....." . "<br>";
		*/
		
		$generateGeojsonCommand = escapeshellcmd('/usr/bin/python3.5' . ' ' . $_SERVER['DOCUMENT_ROOT'] . '/' . "scripts/generate_geojson.py -i $target_filepath -u $user_id -o $current_output_dir");
		//echo "<br>" . $generateGeojsonCommand;
		$output = shell_exec($generateGeojsonCommand);
		//echo $output;
		
		echo "<div class='form-group'>
		<h3>ClubrootTracker File Upload Successful!</h3>
		<br>
		<p>$name,</p>
		<br>
		<p>You uploaded the file $filename to the ClubrootTracker sucessfully!</p>
		<br>
		<p>Click <a href='/clubroot/index.php'>here</a> to view your data on the ClubrootTracker! or Click here to <a href='ClubrootTrackerUpload.php'>upload </a>another clubroot survey!</p>
		</div>";
		
    /*}else{
		unlink($target_filepath);
		echo "Invalid CSV Format";
		die;
	}*/
    
    
    
    

    ?>
