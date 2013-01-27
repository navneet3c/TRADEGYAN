<?php
function run_query($str){
	$ress=@mysql_query($str);
	if(!$ress)
		throw new Exception('MySQL Error: '.mysql_error());
	return $ress;
}
define('alpha123@#','c1ada32eef');
require('mySQL.php');
$c=@mysql_connect($SQLserver.':'.$SQLport,$SQLuser,$SQLpassword);
if(!$c)
	die( 'Database connection failed! Please Try Again Later');
if(!mysql_select_db($SQLdatabase,$c))
	die('Database selection failed! Please Try Again Later.');

$result=run_query('SELECT * FROM `updates` LIMIT 0,1;');
$result=mysql_fetch_row($result);
$update=html_entity_decode($result[0], ENT_QUOTES);

$company='';
$result=run_query('SELECT `compname`,`baseprice`,`currentprice`,`vtd` FROM `company` ORDER BY `compname`;');
while($row=mysql_fetch_row($result)){
	$icon=$row[2]>$row[1]?'yesicon':'noicon';
	if($row[2]==$row[1])
		$icon='equalicon';
	$company.="<tr><td>{$row[0]}</td><td>{$row[1]}</td><td>{$row[2]}</td><td>{$row[3]}</td><td><span class='$icon'></span></td></tr>";
}

$price='';
$result=run_query('SELECT `product`,`base_price`,`current_price`,`vtd` FROM `product` ORDER BY `product`;');
while($row=mysql_fetch_row($result)){
	$icon=$row[2]>$row[1]?'yesicon':'noicon';
	if($row[2]==$row[1])
		$icon='equalicon';
	$price.="<tr><td>{$row[0]}</td><td>{$row[1]}</td><td>{$row[2]}</td><td>{$row[3]}</td><td><span class='$icon'></span></td></tr>";
}

$ranking='';
$result=run_query('SELECT `name` FROM `user` ORDER BY `asset`+`bonus` DESC,`name` ASC LIMIT 0,10;');
mysql_close($c);
$i=0;
while($row=mysql_fetch_row($result)){
	++$i;
	$ranking.="<tr><td>Team {$row[0]}</td><td>$i</td></tr>";
}

echo json_encode(array('update'=>$update,'company'=>$company,'price'=>$price,'ranking'=>$ranking));
?>