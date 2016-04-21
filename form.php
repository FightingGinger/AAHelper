<!DOCTYPE html>
<html lang="en">
<?php 
	session_start();

	//open database
	require_once 'includes/db_connect.php';
	require_once 'includes/functions.php';

	if (!logged_in()) {
  		header("Location: index.php");
	}

	//write_to_file($_SESSION, "Session Variables");

	$studentMajor = $_SESSION['major'];
	if ($studentMajor == 'CS') {
		$studentMajor = "Computer Science";
	}
	elseif ($studentMajor == 'CIS') {
		$studentMajor = "Computer Information Systems";
	}

	//write_to_file($studentMajor, "\$studentMajor");

?>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Form</title>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-select.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <![endif]-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script src="js/custom.js"></script>
	<script src="js/bootstrap-select.js"></script>

	<style type="text/css">
		select {
		   appearance: none;
		   -webkit-appearance: none;
		   -moz-appearance: none;
		   text-indent: 1px;
		   text-overflow: '';
		   background: url("custom image") no-repeat right center;
		}
		select::-ms-expand {
		    display: none;
		}

		.test{
		  width: 20px;
		  height: 20px;
		}
		table.table-bordered td.t1{
		    width: 30px; 
		    height: 30px;
		}
		div.d1{
		  width: 30px;
		  height: 30px;
		}
		th, td{
		  text-align: center;
		}
	</style>
</head>
<body background="img/sunset.jpg">
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
	      <li><a class="text-primary">
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
			    <table class="table-bordered">
			    <tr> <!-- Set table headers -->
			    <?php $startyear = $_SESSION['startyear']; ?>
				    <th colspan="3"></th>
				    <?php for($x = 0; $x < 5; $x++){ ?>
			    	<th colspan="3"><center><?php echo $startyear + $x ?></center></th>
			    	<?php } ?>
				    <th></th>
				</tr>
				  	<tr>
					    <th>Class Name</th>
					    <th>Course ID</th>
					    <th>units</th>
					    <?php for($x = 0; $x < 5; $x++){ ?>
						    <th>F</th>
						    <th>S</th>
						    <th><i class="fa fa-sun-o"></i></th>
					    <?php } ?>
					    <th>Course Grade</th>
				  	</tr>

				  	<!-- Fill out form body -->
					<?php

					$sql_coursemajor = "SELECT * 
						FROM courses JOIN major 
						ON courses.courseid = major.courseid 
						WHERE majorid = ?;";

					$sql_coursemajor = $connection->prepare($sql_coursemajor);
					$sql_coursemajor->bind_param('s', $_SESSION['major']);
					$sql_coursemajor->execute();

					$result = $sql_coursemajor->get_result();

					//takes every courseid with correct major

					$takenspace = null;
					
					while ($row = $result->fetch_array()) {

						//check if course has been taken
						$grade = "";
						$courseid = $row['courseid'];
						$findCourseTakenSql = " SELECT * 
												FROM ( 
													SELECT courseid, grade, termtaken, status 
													FROM studentcourse JOIN student 
													ON studentcourse.studentid = student.studentid 
													where student.studentid = ?
												) as courseHistory 
												where courseid = ?";
						
						$courseTakenStmt = $connection->prepare($findCourseTakenSql);
						$courseTakenStmt->bind_param('ss', $_SESSION['studentid'], $courseid);
						$courseTakenStmt->execute();

						$courseTaken = $courseTakenStmt->get_result();

						if($taken = $courseTaken->fetch_assoc()){
							//write_to_file($taken, "\$taken");
							$year = substr($taken['termtaken'], 0,4);
							$term = substr($taken['termtaken'], 4,1);
							//years off start date (0 = start year)
							$remain = $year - $_SESSION['startyear'];
							$remain = $remain * 3;

							$termpos = 0;
							if ($term == 7){
								// 7 represents Fall
								// Fall is in position 1
								$termpos = 1;
							} elseif ($term == 1){
								// 1 Represents Spring
								// Spring is in position 2
								$termpos = 2;
							} elseif ($term == 4){
								// 4 represents Summer
								// Summer is in position 3
								$termpos = 3;
							}

							//spaces form base (3)
							$takenspace = $remain + $termpos;

							$grade = $taken['grade'];
						}

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

							$currentTerm = "fall";
							for ($i=1; $i <= 15; $i++) { 
								switch ($currentTerm) {
									case "fall":
										if (validate_term($fall, $takenspace, $i) == "taken") {
											?><td>
												<select class="selectpicker" data-width="100%" title=" " name="cs220button1">
													<option title="C" selected="selected">Completed</option>
													<option title="IP">In Progress</option>
													<option title="P">Planned</option>
													<option title="F">Failed</option>
												</select>
											</td> <?php
										} elseif (validate_term($fall, $takenspace, $i) == "available") {
											echo "<td>";
												echo "<select class='selectpicker' data-width='100%' title=' '>";
													echo "<option title='C'>Completed</option>";
													echo "<option title='IP'>In Progress</option>";
													echo "<option title='P'>Planned</option>";
													echo "<option title='F'>Failed</option>";
												echo "</select>";
											echo "</td>";
										} else {
											echo "<td style='background-color:black;'></td>";
										}
										break;
									case "spring":
										if (validate_term($spring, $takenspace, $i) == "taken") {
											echo "<td>";
												echo "<select class='selectpicker' data-width='100%' title=' '>";
													echo "<option title='C' selected='selected'>Completed</option>";
													echo "<option title='IP'>In Progress</option>";
													echo "<option title='P'>Planned</option>";
													echo "<option title='F'>Failed</option>";
												echo "</select>";
											echo "</td>";
										} elseif (validate_term($spring, $takenspace, $i) == "available") {
											echo "<td>";
												echo "<select class='selectpicker' data-width='100%' title=' '>";
													echo "<option title='C'>Completed</option>";
													echo "<option title='IP'>In Progress</option>";
													echo "<option title='P'>Planned</option>";
													echo "<option title='F'>Failed</option>";
												echo "</select>";
											echo "</td>";
										} else {
											echo "<td style='background-color:black;'></td>";
										}
										break;
									case "summer":
										if (validate_term($summer, $takenspace, $i) == "taken") {
											echo "<td>";
												echo "<select class='selectpicker' data-width='100%' title=' '>";
													echo "<option title='C' selected='selected'>Completed</option>";
													echo "<option title='IP'>In Progress</option>";
													echo "<option title='P'>Planned</option>";
													echo "<option title='F'>Failed</option>";
												echo "</select>";
											echo "</td>";
										} elseif (validate_term($summer, $takenspace, $i) == "available") {
											echo "<td>";
												echo "<select class='selectpicker' data-width='100%' title=' '>";
													echo "<option title='C'>Completed</option>";
													echo "<option title='IP'>In Progress</option>";
													echo "<option title='P'>Planned</option>";
													echo "<option title='F'>Failed</option>";
												echo "</select>";
											echo "</td>";
										} else {
											echo "<td style='background-color:black;'></td>";
										}
										break;
								}

								// Cycle through the terms
								if ($currentTerm == "fall") {
									$currentTerm = "spring";
								} elseif ($currentTerm == "spring") {
									$currentTerm = "summer";
								} elseif ($currentTerm == "summer") {
									$currentTerm = "fall";
								}
							}

							$takenspace = null; ?>
							<!-- end replication -->

							<td><input type="text" name="cs" size="2" value="<?php echo $grade; ?>"></td> 
						</tr> 
						<?php
					} // End of row, loop through again until end of table! ?>
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
