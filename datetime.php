
<html>
<head></head>
<body>

<form action="datetime.php" method="post">
Enter the US style date (mm/dd/yyyy) you would like to convert to unix time: 
<?

	echo "<br/><input type='text=' name='datetime' size='20' value='".$_POST["datetime"]."' /><br/>";

?>

Hours 
<select name='hours'>
<?
	$i = 0;
	while($i < 24)
	{
		if(isset($_POST["hours"]) && $i == $_POST["hours"])
			$selected = "SELECTED";
		else
			$selected = "";
				
		echo  "<option value='$i' $selected>$i</option>";
		$i++;
	}
?>

</select>
 Minutes
<select name='minutes'>
<?
	$i = 0;
	while($i < 60)
	{
		if(isset($_POST["minutes"]) && $i == $_POST["minutes"])
			$selected = "SELECTED";
		else
			$selected = "";
				
		echo  "<option value='$i' $selected>$i</option>";
		$i++;
	}

?>
</select>

 Seconds
<select name='seconds'>
<?
	$i = 0;
	while($i < 60)
	{
		if(isset($_POST["seconds"]) && $i == $_POST["seconds"])
			$selected = "SELECTED";
		else
			$selected = "";
				
		echo  "<option value='$i' $selected>$i</option>";
		$i++;
	}

?>
</select>

<br/><br/>

Or enter some unix time to convert back to a human readable date

<?

echo "<input type='text' name='unix' value='$unix' />";

?>

<input type="submit" name='submit' value='submit'/>
</form>

<?php 

	if(isset($_POST["datetime"]) && $_POST["datetime"] != "")
	{
		$info = explode("/",$_POST["datetime"]);
		$month = $info[0];
		$day = $info[1];
		$year = $info[2];
		$time = mktime($_POST["hours"],$_POST["minutes"],$_POST["seconds"],$month,$day,$year);
		
		echo "<br/>$time<br/>";
		//mail("msnider@andrew.cmu.edu", "test", "is email even working?", "From: Fusion Administrator <support.fusion@gmail.com>");
	}
	
	if(isset($_POST["unix"]))
		echo "<br/>".date("F d, Y, H i s", $_POST["unix"])."<br/>";
?>
		


</body>


</html>