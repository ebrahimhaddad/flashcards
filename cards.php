<!DOCTYPE html>
<html>
<?php
   include 'config.php';
?>
<head>
  <title>Ebrahim's Flash Cards!</title>
  <link rel="stylesheet" type="text/css" href="cards.css">
</head>
<body>
  <div class="login-container">
    <h2>Flash Cards</h2>
    <hr>
	<h3>Choose a Book and Lesson:</h3>
    	<form action='practice.php' method='POST'>
		<select class = "chose" name='lektion'>
    		<?php
       			$sql = "SELECT * FROM `woerter_txt` GROUP BY `COL 7`,`COL 8` ORDER BY `COL 7`,`COL 8`;";
        		$r = mysqli_query($db, $sql);
        		while ($row = mysqli_fetch_array($r)) {
 
            echo "<div style='align-items:center; background-color:#007bff'>
              <option class='choose' value='$row[6] - $row[7]' onchange='setLektion()'>$row[6] - $row[7]</option>
            </div>";
        }
       ?>
       </select>
       <br>
       <input type = 'submit' class='friends' name='sendmessage' value='Start'>
    </form>
  </div>
</body>
</html>