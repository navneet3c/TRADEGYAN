<?php
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>TradeGyan Rankings</title>
<link rel="stylesheet" href="files/terminal.css"></link>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<h3 class="rankTable">TradeGyan Rankings</h3>
	</div>
	<div id="body">
		<div id="tables">
			<table class="rankTable"><tr><th>Rank</th><th>Team Name</th><th>Commodity Balance</th><th>Cash Balance</th><th>Net Worth</th></tr>
<?php

try{
$result=run_query('SELECT `name`,`asset`,`bonus` FROM `user` ORDER BY `asset`+`bonus` DESC,`name` ASC;');
mysql_close($c);
$i=1;
while($row=mysql_fetch_assoc($result)){
	
	$net=$row['asset']+$row['bonus'];
	echo "<tr><td>$i</td><td>Team {$row['name']}</td><td>{$row['asset']}</td><td>{$row['bonus']}</td><td>$net</td></tr>";
	++$i;
}
} catch(Exception $e){
	$error=$e->getMessage();
}
?></table>
		</div>
		<?php
if(isset($error))
	echo "<div class='errorDiv'>$error</div>";
if(isset($info))
	echo "<div class='infoDiv'>$info</div>";
?>
		<div class="clear"></div>
	</div>
	<div id="footer">
	&copy; TradeGyan Team.
	</div>
</div>
</body>
</html>