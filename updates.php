<?php
session_start();
function run_query($str){
	$ress=@mysql_query($str);
	if(!$ress)
		throw new Exception('MySQL Error: '.mysql_error());
	return $ress;
}

define('alpha123@#','c1ada32eef');
require('files/mySQL.php');
$c=@mysql_connect($SQLserver.':'.$SQLport,$SQLuser,$SQLpassword);
if(!$c)
	die( 'Database connection failed! Please Try Again Later');
if(!mysql_select_db($SQLdatabase,$c))
	die('Database selection failed! Please Try Again Later.');
try{
	$login=0;
	if(isset($_SESSION['login']))
		$login=1;
if(isset($_POST['logoutSubmit'])){
	session_destroy();
	$login=0;
} else if(isset($_POST['loginSubmit'])){
	if(md5($_POST['password'])!=md5('Heaven'))
		throw new Exception('Wrong password');
	else {
		$login=1;
		$_SESSION['login']=1;
	}
} else if(isset($_POST['updateSubmit'])){
	$u=htmlentities($_POST['update'], ENT_QUOTES);
	run_query("UPDATE `updates` SET `currentupdate`='$u';");
	$info="Update '$u' is being Displayed.";
} else if(isset($_POST['dividendSubmit'])){
	$comp=$_POST['company'];
	run_query('LOCK TABLES `user` LOW_PRIORITY WRITE,`updates` LOW_PRIORITY WRITE,`company` LOW_PRIORITY WRITE;');
	$result=run_query("SELECT `compname`,`face_value` FROM `company` WHERE `varname`='$comp';");
	$company=mysql_fetch_row($result);
	$per=$_POST['percent'];
	$amt=$company[1];
	$amt=$amt*$per/100;
	$company=$company[0];
	$result=run_query("SELECT `id`,`bonus`,`{$comp}` FROM `user`;");
	while($user=mysql_fetch_assoc($result)){
	if($user[$comp]>0){
		$bonus=$user['bonus']+$amt*$user[$comp];
		run_query("UPDATE `user` SET `bonus`='$bonus' WHERE `id`='{$user['id']}';");
	}
	}
	$info=htmlentities("Company $company announced {$per}% dividend.", ENT_QUOTES);
	run_query("UPDATE `updates` SET `currentupdate`='$info';");
	run_query('UNLOCK TABLES;');
} else if(isset($_POST['bailoutSubmit'])){
	$amt=$_POST['amount'];
	$comp=$_POST['company'];
	run_query('LOCK TABLES `user` LOW_PRIORITY WRITE,`company` LOW_PRIORITY WRITE;');
	$result=run_query("SELECT `compname`,`currentno`,`currentprice`,`total` FROM `company` WHERE `varname`='$comp';");
	$company=mysql_fetch_assoc($result);
	$cw=$company['total']+$amt;
	$cp=$cw/$company['currentno'];
	run_query("UPDATE `company` SET `total`='$cw',`currentprice`='$cp' WHERE `varname`='$comp';");
	$resultu=run_query("SELECT `id`,`$comp`,`asset` FROM `user`;");
	while($user=mysql_fetch_assoc($resultu)){
		$asset=$user['asset']+($cp-$company['currentprice'])*$user[$comp];
	run_query("UPDATE `user` SET `asset`='$asset' WHERE `id`='{$user['id']}';");
	}
	run_query('UNLOCK TABLES;');
	$info=htmlentities("Bailout of Rs. $amt granted successfully.", ENT_QUOTES);
}
} catch(Exception $e){
	$error=$e->getMessage();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>TradeGyan Updates</title>
<script type="text/javascript" src="files/terminal.js"></script>
<link rel="stylesheet" href="files/terminal.css"></link>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<h3>Welcome to TradeGyan Updates Terminal</h3>
<?php
if($error!='')
	echo "<div class='errorDiv'>$error</div>";
if($info!='')
	echo "<div class='infoDiv'>$info</div>";
?>
	</div>
	<div id="body">
<?php
echo '<form method="POST" action="">';
if(!$login){
	echo '
	<input type="password" name="password" /><input type="submit" value="Login" name="loginSubmit" />
	</form>';
} else {
	echo '<input type="submit" name="logoutSubmit" value="Logout" /></form>';
?>
	<div id="update">
		<form method="POST" action="" onsubmit="return updateValidate()"><table>
		<thead>Update</thead><tr><td>Select Update</td><td><select name="update" size="1">
		<option>&nbsp;</option>
<?php
		$file=fopen('updates.txt', "r") or exit("Error occured while reading Updates!");
	$con=fgets($file);
	while(!feof($file) && $con!=''){
	echo '<option>'.$con.'</option>';
	$con=trim(fgets($file));
	}
	fclose($file);
?></select></td></tr>
		<tr><td colspan="2"><input type="submit" name="updateSubmit" value="Display" /></td></tr>
		</table></form><br /><br />
	
	</div>
	<div id="dividend">
		<form method="POST" action="" onsubmit="return dividendValidate()" ><table>
		<thead>Company Dividend</thead>
		<tr><td>Company</td><td><select size="1" name="company">
	<option value="0"></option>
<?php
$res=run_query('SELECT `varname`,`compname` FROM `company`;');
while($row=mysql_fetch_row($res))
	echo "<option value='{$row[0]}'>{$row[1]}</option>";
?>
	</select></td></tr>
	<tr><td>Percent</td><td><select size="1" name="percent">
	<option value="0"></option>
	<option value="50">50%</option>
	<option value="100">100%</option>
	<option value="250">250%</option>
	<option value="500">500%</option>
	</select></td></tr>
		<tr><td colspan="2"><input type="submit" name="dividendSubmit" value="Submit" />
		</table></form>
	</div>
	<div id="bailout">
		<form method="POST" action="" onsubmit="return bailoutValidate()" ><table>
		<thead>Company Bailout</thead>
		<tr><td>Company</td><td><select size="1" name="company">
	<option value="0"></option>
<?php
$res=run_query('SELECT `varname`,`compname` FROM `company`;');
mysql_close($c);
while($row=mysql_fetch_row($res))
	echo "<option value='{$row[0]}'>{$row[1]}</option>";
?>
	</select></td></tr>
	<tr><td>Amount</td><td><input type="text" name="amount" value="0" onclick="this.select()" onkeypress = "onlyNumbers(event)" /></td></tr>
		<tr><td colspan="2"><input type="submit" name="bailoutSubmit" value="Submit" />
		</table></form>
<?php } ?>	
	</div>
	<div id="footer">
	&copy; TradeGyan Team.
	</div>
</div>
</body>
</html>