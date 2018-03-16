<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Praktinis darbas</title>
    <link href="images/icon.png" rel="shortcut icon">

	<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/timer.js"></script>
	
	<!-- Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
	
	<!-- Custom styles -->
    <link rel="stylesheet" href="css/styles.css">
  </head>
  
  <?php
  $servername = "sql301.epizy.com";
  $username = "epiz_21046348";
  $password = "puslapis";
  $dbname = "epiz_21046348_bj";

  // Prisijungimas prie duombazės
  $conn = new mysqli($servername, $username, $password, $dbname);
  
  function rand_sha1($length) {
    $max = ceil($length / 40);
    $random = '';
    for ($i = 0; $i < $max; $i ++) {
	  $random .= sha1(microtime(true).mt_rand(10000,90000));
    }
    return substr($random, 0, $length);
  }
  
  function deleteMessage($first, $second) {
	$servername = "sql301.epizy.com";
    $username = "epiz_21046348";
    $password = "puslapis";
    $dbname = "epiz_21046348_bj";
	$conn = new mysqli($servername, $username, $password, $dbname);
    $del = "DELETE FROM `messages` WHERE `userid`='$first' AND `userkey`='$second'";
    $conn->query($del);
    header('Location: index.php?d=ok');
	mysqli_close($conn);
  }
  
  function encryptIt($q) {
    $cryptKey = 'bJv7rGtIn5RB1xG03ofyCr';
    $qEncoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), $q, MCRYPT_MODE_CBC, md5(md5($cryptKey))));
    return($qEncoded);
  }

  function decryptIt($q) {
    $cryptKey = 'bJv7rGtIn5RB1xG03ofyCr';
    $qDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), base64_decode($q), MCRYPT_MODE_CBC, md5(md5($cryptKey))), "\0");
    return($qDecoded);
  }
  ?>
  
  <body>
	<nav class="navbar navbar-default navbar-fixed-top">
	  <h3><a class="navbar-brand" href="index.php">Slaptažodžių/informacijos šifravimo internetinis projektas</a></h3>
    </nav>
	<form method="POST" id="form" action="">
	<div class="container">
      <div class="row">
		<div class="col-md-offset-1 col-md-10 col-md-offset-1 main">
		  <?php
		  if (isset($_POST['encrypt'])) {
		  ?>
		    <div class="alert alert-info" role="alert">
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			  </button>
			  <center><span class="uzkoduota"><i class="fas fa-check"></i></span> Jūsų žinutė sėkmingai užkoduota!</center>
		    </div>
		  <?php
		  }
		  $id = encryptIt($_GET['id']);
		  $key = encryptIt($_GET['key']);
		  $sql = "SELECT * FROM `messages` WHERE `userid`='$id' AND `userkey`='$key'";
		  $result = $conn->query($sql);
		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  if ((strlen($_GET['id']) == 12) && (strlen($_GET['key']) == 32) && ($result->num_rows > 0)) {
			$date = new DateTime(date("Y-m-d H:i:s"));
			$message = decryptIt($row["message"]);
			$timetodelete = decryptIt($row["timetodelete"]);
			$datetimetodelete = $row["datetime"];
			if ($row["datetime"] == "0000-00-00 00:00:00") {
			  if ($timetodelete == "0.16666667") {
				$time = "+10 min";
			  }
			  elseif ($timetodelete == "0.5") {
				$time = "+30 min";
			  } else {
				$time = "+" . $timetodelete . "hour";
			  }
			  $date->modify($time);
			  $datetimetodelete = $date->format('Y-m-d H:i:s');
			  $sql = "UPDATE `messages` SET `datetime`='$datetimetodelete' WHERE `userid`='$id' AND `userkey`='$key'";
			  $conn->query($sql);
			}
			$timenow = strtotime(date("Y-m-d H:i:s"));
			$deletetime = strtotime($datetimetodelete);
			$difference = $deletetime - $timenow;
			if (($difference < 0) && ($datetimetodelete != "0000-00-00 00:00:00")) {
			  deleteMessage($id, $key);
			}
		  ?>
			<p class="help">Jūsų žinutė:</p>
		    <div class="form-group">
              <textarea rows="10" class="form-control" id="message-content" name="text" placeholder="Įveskite tekstą" required="true" readonly><?php echo $message; ?></textarea>
            </div>
			<p class="help">Laikas iki žinutės sunaikinimo:</p>
			<span class="hidden" id="delete-time"><?php echo $datetimetodelete; ?></span>
            <h4 id="count"><img id="loading" src="images/loading3.gif"></h4>
		  <?php
		    if ($_GET['del'] == $_GET['id']) {
			  ?>
			  <center><button type="submit" class="btn btn-primary" name="deletemessage"><i class="fas fa-trash-alt"></i> Sunaikinti žinutę</button></center>
			  <?php
			  if (isset($_POST['deletemessage'])) {
			    deleteMessage($id, $key);
			  }
		    }
		  } else {
			  if (($_GET['d'] == "ok") && (!isset($_POST['encrypt']))) {
		      ?>
				<div class="alert alert-info" role="alert">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				  </button>
				  <center><span class="sunaikinta"><i class="fas fa-trash-alt"></i></span> Jūsų žinutė sunaikinta!</center>
				</div>
		      <?php
			  }
		      ?>
			  <p class="help"><i class="far fa-envelope-open"></i> Žinutė užkodavimui:</p>
			  <div class="form-group">
				<textarea rows="10" class="form-control" id="message-content" name="text" placeholder="Įveskite tekstą" required="true"></textarea>
			  </div>
			  <p class="help"><i class="far fa-clock"></i> Laikas, kuriam praėjus, po žinutės perskaitymo, žinutė bus sunaikinta:</p>
			  <div class="form-group">
				<select name="time" class="form-control">
				  <option value = "0.16666667">10 min.</option>
				  <option value = "0.5">30 min.</option>
				  <option value = "1">1 val.</option>
				  <option value = "2">2 val.</option>
				  <option value = "4">4 val.</option>
				  <option value = "8">8 val.</option>
				  <option value = "12">12 val.</option>
				  <option value = "24">1 d.</option>
				  <option value = "48">2 d.</option>
				  <option value = "72">3 d.</option>
				  <option value = "168">7 d.</option>
				</select>
			  </div>
			  <center><button type="submit" class="btn btn-primary" name="encrypt"><i class="fas fa-lock"></i> Užkoduoti žinutę</button></center>
		  <?php
		  }
		  if (isset($_POST['encrypt'])) {
			$message = encryptIt($_POST['text']);
			$timetodelete = encryptIt($_POST['time']);
			$id = encryptIt(rand_sha1(12));
			$key = encryptIt(rand_sha1(32));
			$sql = "INSERT INTO `messages` (userid, userkey, message, timetodelete) VALUES ('$id', '$key', '$message', '$timetodelete')";
			$conn->query($sql);
		  ?>
			<hr>
			<p class="help">Sugeneruota nuoroda:</p>
			<div class="form-group">
			  <div class="input-group">
				<input type="text" onclick="this.select();" class="form-control input" value="http://rol.freecluster.eu/praktinis/index.php?id=<?php echo decryptIt($id); ?>&key=<?php echo decryptIt($key); ?>">
				<span id="copy" class="input-group-addon copy" ><span class="glyphicon glyphicon-copy"></span></span>
			  </div>
			</div>
			<p class="help">Nuoroda pašalinimui:</p>
			<div class="form-group">
			  <div class="input-group">
				<input type="text" onclick="this.select();" class="form-control input" value="http://rol.freecluster.eu/praktinis/index.php?id=<?php echo decryptIt($id); ?>&key=<?php echo decryptIt($key); ?>&del=<?php echo decryptIt($id); ?>">
				<span id="copy" class="input-group-addon copy" ><span class="glyphicon glyphicon-copy"></span></span>
			  </div>
			</div>
		<?php
		}
		mysqli_close($conn);
		?>
	    </div>
	  </div>
	</div>
	</form>
  </body>
</html>