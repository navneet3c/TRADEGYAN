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
}
} catch(Exception $e){
	$error=$e->getMessage();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>TradeGyan Log</title>
<link rel="stylesheet" href="files/terminal.css"></link>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<h3 class="rankTable">TradeGyan Logs</h3>
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
		<div id="tables">
			<table class="rankTable"><tr><th>&nbsp;</th><th>User Id</th><th>User Name</th><th>Transaction</th><th>Amount</th><th>Item</th><th>Time</th></tr>
<?php

try{
$result=run_query('SELECT * FROM `log` ORDER BY `id` DESC;');
mysql_close($c);
$i=1;
while($row=mysql_fetch_assoc($result)){
	
	$net=$row['asset']+$row['bonus'];
	echo "<tr><td>$i</td><td>{$row['user_id']}</td><td>Team {$row['user_name']}</td><td>{$row['description']}</td><td>{$row['amount']}</td><td>{$row['item']}</td><td>".html_entity_decode($row['time'])."</td></tr>";
	++$i;
}
} catch(Exception $e){
	$error=$e->getMessage();
}
?></table>
		</div>
		<?php
if($error!='')
	echo "<div class='errorDiv'>$error</div>";
if($info!='')
	echo "<div class='infoDiv'>$info</div>";
?>
		<div class="clear"></div>
		<?php } ?>	
	</div>
	<div id="footer">
	&copy; TradeGyan Team.
	</div>
</div>
</body>
</html>