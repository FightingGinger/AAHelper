<!DOCTYPE html>
<html>
<?php 
	session_start();

	//open database
	require_once 'includes/db_connect.php';
	require_once 'includes/functions.php';

	if (!logged_in()) {
  		header("Location: index.php");
	}

	$studentMajor = $_SESSION['major'];
	if ($studentMajor == 'CS') {
		$studentMajor = "Computer Science";
	}
	elseif ($studentMajor == 'CIS') {
		$studentMajor = "Computer Information Systems";
	}

?>
<head>
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
<nav class="navbar navbar-default navbar-inverse">
  <div class="container-fluid"> 
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"></button>
      <a class="navbar-brand"> <?php echo $studentMajor; ?> </a>
    </div>
	  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	    <ul class="nav navbar-nav">
          <li> </li>
          <li> </li>
	    </ul>
	    <ul class="nav navbar-nav navbar-right">
	      <li><a>
	        <?php	
	        	echo $_SESSION['fname'];
	        	echo " ";
						echo $_SESSION['lname']; 
					?>
	      </a></li>
	      <li>&nbsp;</li>
        <li><a><?php echo $_SESSION['studentid']; ?></a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- the table -->
<div class="container">
  <div class="row">
    <div class="col-lg-12">
      <div class="jumbotron">
	  		<div class="panel panel-default">
			    <table class="table table-condensed table-bordered">
				  	<tr>
					    <th>Class Name</th>
					    <th>Course ID</th>
					    <th>units</th>
					    <th>F</th>
					    <th>S</th>
					    <th><i class="fa fa-sun-o"></i></th>
					    <th>Course Grade</th>
				  	</tr>

					<?php

					$sql_coursemajor = "SELECT * 
															FROM courses JOIN major 
															ON courses.courseid = major.courseid 
															WHERE majorid = 'CS';";

					$sql_coursemajor = $connection->query($sql_coursemajor);
					//takes every courseid with correct major
					while ($row = $sql_coursemajor->fetch_array()) {
						// $currentCourse = $row['courseid'];
						// echo $currentCourse;
						?>
						<tr> 
							<td> <?php echo $row['classname']; ?> </td>
							<td> <?php echo $row['courseid']; ?> </td> 
							<td> <?php echo $row['units']; ?> </td> 
							<?php
								//break up terms
								$termnum = $row['term'];
								$fall = substr($termnum, 0,1);
								$spring = substr($termnum, 1,1);
								$summer = substr($termnum, 2,1);
							?> 
							<td <?php echo validate_term($fall); ?> ></td>
							<td <?php echo validate_term($spring); ?> ></td>
							<td <?php echo validate_term($summer); ?> ></td>
							<td> <?php echo "A"; ?> </td> 
						</tr> 
						<?php
					} ?>
					</table>
				</div>
				<center><a href="includes/logout.php">Logout</a></center>
	  	</div>
		</div>
  </div>
</div> 

<?php
  $connection->close();
?>

<!-- include footer -->
<center>
  <?php include 'footer.php'; ?>
</center>
</body>
</html>
