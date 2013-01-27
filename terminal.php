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
try{
/*if(isset($_POST['addUsersSubmit']))
	$mode=1;
else if(isset($_POST['commodityBuySubmit'])){
	$mode=2;
	$desc='Commodity Buy';
}
else if(isset($_POST['commoditySellSubmit'])){
	$mode=3;
	$desc='Commodity Sell';
}
else if(isset($_POST['commodityDeposite'])){
	$mode=4;
	$desc='Commodity Deposit';
}
else if(isset($_POST['companyBuySubmit'])){
	$mode=5;
	$desc='Company Buy';
}
else if(isset($_POST['companySellSubmit'])){
	$mode=6;
	$desc='Company Sell';
}*/

$id=$_POST['teamName'];
$time=htmlentities(date('F j, Y, g:i a',time()));
run_query('LOCK TABLES `user` LOW_PRIORITY WRITE,`log` LOW_PRIORITY WRITE,`product` LOW_PRIORITY WRITE,`company` LOW_PRIORITY WRITE;');
switch($mode){
case 1://create user
	if(!filter_var($_POST['appendCount'],FILTER_VALIDATE_INT))
		throw new Exception('Invalid No. of Users Entered to Append! Must be an Integer.');
	$res=run_query('SELECT count(*) FROM `user`;');
	$count=mysql_fetch_row($res);
	$count=$count[0];
	for($i=$_POST['appendCount'];$i>0;--$i){
		++$count;
		run_query("INSERT INTO `tradegyan`.`user` (`id`, `name`, `coal`, `lead`, `iron`, `copper`, `silver`, `gold`, `platinum`, `comp1`, `comp2`, `comp3`, `comp4`, `comp5`, `comp6`, `comp7`, `comp8`, `comp9`, `comp10`, `comp11`, `comp12`, `comp13`, `comp14`, `comp15`, `comp16`, `comp17`, `comp18`, `comp19`, `comp20`, `asset`, `bonus`) VALUES (NULL, '{$count}', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '200000');");
	}
	$info=$_POST['appendCount'].' users created succesfully. Base values initialized.';
	break;
case 2://buy commodity
	$weight=$_POST['weight'];
	$product=$_POST['product'];
	if($product=='Platinum')
		throw new Exception('Lower circuit is applied to platinum.');
	if($product=='Coal')
		throw new Exception('Lower circuit is applied to coal.');
	$result=run_query("SELECT `name`,`bonus`,`Coal`,`Lead`,`Iron`,`Copper`,`Silver`,`Gold`,`Platinum` FROM `user` WHERE `id`='$id';");
	if(!$row=mysql_fetch_assoc($result))
		throw new Exception('Invalid Team Selected!');
	$result=run_query("SELECT `current_kg`,`current_price`,`marketworth` FROM `product` WHERE `product`='$product';");
	$row1=mysql_fetch_assoc($result);
	if($weight>=$row1['current_kg'] || $row['bonus']<$row1['current_price']*$weight)
		throw new Exception("This transaction cannot be made[INVALID REQUEST]!");
	$kg1=$row[$product]+$weight;
	$bonus=$row['bonus']-$row1['current_price']*$weight;
	$mw=$row1['current_price']*$weight+$row1['marketworth'];
	$kg=$row1['current_kg']-$weight;
	$cp=round($mw/$kg,2);
	//var_dump($mw,$kg,$cp,$row1['current_price'],$cp/$row1['current_price']);
	if($cp/$row1['current_price']>2)
		throw new Exception("The transaction can not be made due to Upper Circuit.");
	//change made here
	$query=run_query("SELECT `vtd` FROM `product` WHERE `product`='$product';");
	$row5=mysql_fetch_assoc($query);
	$volume=$row5['vtd']+$weight;
	$query=run_query("UPDATE `product` SET `vtd`='$volume' WHERE `product`='$product';");
	//till here
	run_query("UPDATE `user` SET `{$product}`='$kg1',`bonus`='$bonus' WHERE `id`='$id';");
	run_query("UPDATE `product` SET `marketworth`='$mw',`current_kg`='$kg',`current_price`='$cp' WHERE `product`='$product';");
	$info="Team {$row['name']} bought $weight kgs of $product.";
	
	$resultu=run_query("SELECT `user`.`id`,`$product`,`asset` FROM `user`;");
	while($user=mysql_fetch_assoc($resultu)){
	if($user['id']==$id)
		$asset=$user['asset']-($row1['current_price']*$row[$product])+($user[$product]*$cp);
	else
		$asset=$user['asset']+($cp-$row1['current_price'])*$user[$product];
	run_query("UPDATE `user` SET `asset`='$asset' WHERE `id`='{$user['id']}';");
	}
	run_query("INSERT INTO `log` VALUES(null,'$id','{$row['name']}','$desc','$weight','$product','$time');");
	break;
case 3://sell commodity
	$weight=$_POST['weight'];
	$product=$_POST['product'];
	if($product=='Platinum')
		throw new Exception('Lower circuit is applied to platinum.');
		if($product=='Coal')
		throw new Exception('Lower circuit is applied to coal.');
	$result=run_query("SELECT `name`,`bonus`,`Coal`,`Lead`,`Iron`,`Copper`,`Silver`,`Gold`,`Platinum` FROM `user` WHERE `id`='$id';");
	if(!$row=mysql_fetch_assoc($result))
		throw new Exception('Invalid Team Selected!');
	$result=run_query("SELECT `current_kg`,`current_price`,`marketworth` FROM `product` WHERE `product`='$product';");
	$row1=mysql_fetch_assoc($result);
	if($weight>$row[$product])
		throw new Exception("This transaction cannot be made[INVALID REQUEST]!");
	$kg=$row[$product]-$weight;
	$bonus=$row['bonus']+$row1['current_price']*$weight;
	$mw=$row1['marketworth']-$row1['current_price']*$weight;
	$kg=$row1['current_kg']+$weight;
	$cp=round($mw/$kg,2);
	if($row1['current_price']/$cp>2)
		throw new Exception("The transaction can not be made due to lower circuit.");
		//change made here
	$query=run_query("SELECT `vtd` FROM `product` WHERE `product`='$product';");
	$row5=mysql_fetch_assoc($query);
	$volume=$row5['vtd']+$weight;
	$queery=run_query("UPDATE `product` SET `vtd`='$volume' WHERE `product`='$product';");
	//till here
	run_query("UPDATE `user` SET `{$product}`='$kg',`bonus`='$bonus' WHERE `id`='$id';");
	run_query("UPDATE `product` SET `marketworth`='$mw',`current_kg`='$kg',`current_price`='$cp' WHERE `product`='$product';");
	$info="Team {$row['name']} sold $weight kgs of $product.";
	
	$resultu=run_query("SELECT `user`.`id`,`$product`,`asset` FROM `user`;");
	while($user=mysql_fetch_assoc($resultu)){
	if($user['id']==$id)
		$asset=$user['asset']-($row1['current_price']*$row[$product])+($user[$product]*$cp);
	else
		$asset=$user['asset']+($cp-$row1['current_price'])*$user[$product];
	run_query("UPDATE `user` SET `asset`='$asset' WHERE `id`='{$user['id']}';");
	}
	run_query("INSERT INTO `log` VALUES(null,'$id','{$row['name']}','$desc','$weight','$product','$time');");
	break;
case 4://deposit commodity
	$weight=$_POST['weight'];
	$product=$_POST['product'];
	$result=run_query("SELECT `name`,`bonus`,`Coal`,`Lead`,`Iron`,`Copper`,`Silver`,`Gold`,`Platinum` FROM `user` WHERE `id`='$id';");
	if(!$row=mysql_fetch_assoc($result))
		throw new Exception('Invalid Team Selected!');
	$result=run_query("SELECT `current_kg`,`current_price`,`marketworth` FROM `product` WHERE `product`='$product';");
	$row1=mysql_fetch_assoc($result);
	$amt=$row['bonus']+$row1['current_price']*$weight;
	$basekg=$row1['current_kg']+$weight;
	$cp=round($row1['marketworth']/$basekg,2);
	run_query("UPDATE `product` SET `current_kg`='$basekg',`current_price`='$cp' WHERE `product`='$product';");
	run_query("UPDATE `user` SET `bonus`='$amt' WHERE `id`='$id';");
	$info="Team {$row['name']} deposited $weight kgs of $product .";
	//change made here
	$query=run_query("SELECT `vtd` FROM `product` WHERE `product`='$product';");
	$row2=mysql_fetch_assoc($query);
	$volume=$row2['vtd']+$weight;
	$queery=run_query("UPDATE `product` SET `vtd`='$volume' WHERE `product`='$product';");
	//till here
	$resultu=run_query("SELECT `user`.`id`,`$product`,`asset` FROM `user`;");
	while($user=mysql_fetch_assoc($resultu)){
	if($user['id']==$id)
		$asset=$user['asset']-($row1['current_price']*$row[$product])+($user[$product]*$cp);
	else
		$asset=$user['asset']+($cp-$row1['current_price'])*$user[$product];
	run_query("UPDATE `user` SET `asset`='$asset' WHERE `id`='{$user['id']}';");
	}
	run_query("INSERT INTO `log` VALUES(null,'$id','{$row['name']}','$desc','$weight','$product','$time');");
	break;
case 5://buy shares
	$nos=$_POST['sharesCount'];
	$comp=$_POST['company'];
	
	$result=run_query("SELECT `name`,`bonus`,`{$comp}` FROM `user` WHERE `id`='$id';");
	if(!$row=mysql_fetch_assoc($result))
		throw new Exception('Invalid Team Selected!');
	$result=run_query("SELECT `compname`,`currentno`,`currentprice`,`total` FROM `company` WHERE `varname`='$comp';");
	$row2=mysql_fetch_assoc($result);
	
	if($row2['currentno']-$nos<1000)
		throw new Exception("The transaction can not be made due to Upper Circuit.");
	if($row['bonus']<$row2['currentprice']*$nos)
		throw new Exception("This transaction cannot be made[INVALID REQUEST]!");
	$unos=$row[$comp]+$nos;
	$bonus=$row['bonus']-$row2['currentprice']*$nos;
	$cw=$row2['total']+$row2['currentprice']*$nos;
	$cnos=$row2['currentno']-$nos;
	$cp=round($cw/$cnos,2);
	if($cp/$row2['currentprice']>2)
		throw new Exception("The transaction can not be made due to Upper Circuit.");
	run_query("UPDATE `user` SET `{$comp}`='$unos',`bonus`='$bonus' WHERE `id`='$id';");
	run_query("UPDATE `company` SET `total`='$cw',`currentno`='$cnos',`currentprice`='$cp' WHERE `varname`='$comp';");
	$info="Team {$row['name']} bought $nos shares of {$row2['compname']}.";
	//change made here
	$query=run_query("SELECT `vtd` FROM `company` WHERE `varname`='$comp';");
	$row5=mysql_fetch_assoc($query);
	$volume=$row5['vtd']+$nos;
	$queery=run_query("UPDATE `company` SET `vtd`='$volume' WHERE `varname`='$comp';");
	//till here
	$resultu=run_query("SELECT `id`,`$comp`,`asset` FROM `user`;");
	while($user=mysql_fetch_assoc($resultu)){
	if($user['id']==$id)
		$asset=$user['asset']-($row2['currentprice']*$row[$comp])+($user[$comp]*$cp);
	else
		$asset=$user['asset']+($cp-$row2['currentprice'])*$user[$comp];
	run_query("UPDATE `user` SET `asset`='$asset' WHERE `id`='{$user['id']}';");
	}
	run_query("INSERT INTO `log` VALUES(null,'$id','{$row['name']}','$desc','$nos','$comp','$time');");
	break;
case 6://sell shares
	$nos=$_POST['sharesCount'];
	$comp=$_POST['company'];
	
	$result=run_query("SELECT `name`,`bonus`,`{$comp}` FROM `user` WHERE `id`='$id';");
	if(!$row=mysql_fetch_assoc($result))
		throw new Exception('Invalid Team Selected!');
	$result=run_query("SELECT `compname`,`currentno`,`currentprice`,`total` FROM `company` WHERE `varname`='$comp';");
	$row2=mysql_fetch_assoc($result);
	if($row[$comp]<$nos)
		throw new Exception("This transaction cannot be made[INVALID REQUEST]!");
	$unos=$row[$comp]-$nos;
	$bonus=$row['bonus']+$row2['currentprice']*$nos;
	
	$cw=$row2['total']-$row2['currentprice']*$nos;
	if($cw<1000)
		throw new Exception('The transaction can not be made due to lower circuit.');
	$cnos=$row2['currentno']+$nos;
	$cp=round($cw/$cnos,2);
	if($row2['currentprice']/$cp>2)
		throw new Exception("The transaction can not be made due to lower circuit.");
	run_query("UPDATE `user` SET `{$comp}`='$unos',`bonus`='$bonus' WHERE `id`='$id';");
	run_query("UPDATE `company` SET `total`='$cw',`currentno`='$cnos',`currentprice`='$cp' WHERE `varname`='$comp';");
	$info="Team {$row['name']} sold $nos shares of {$row2['compname']}.";
	//change made here
	$query=run_query("SELECT `vtd` FROM `company` WHERE `varname`='$comp';");
	$row5=mysql_fetch_assoc($query);
	$volume=$row5['vtd']+$nos;
	$queery=run_query("UPDATE `company` SET `vtd`='$volume' WHERE `varname`='$comp';");
	//till here
	$resultu=run_query("SELECT `id`,`$comp`,`asset` FROM `user`;");
	while($user=mysql_fetch_assoc($resultu)){
	if($user['id']==$id)
		$asset=$user['asset']-($row2['currentprice']*$row[$comp])+($user[$comp]*$cp);
	else
		$asset=$user['asset']+($cp-$row2['currentprice'])*$user[$comp];
	run_query("UPDATE `user` SET `asset`='$asset' WHERE `id`='{$user['id']}';");
	}
	run_query("INSERT INTO `log` VALUES(null,'$id','{$row['name']}','$desc','$nos','$comp','$time');");
	break;
}
run_query('UNLOCK TABLES;');
} catch(Exception $e){
	$error=$e->getMessage();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>TradeGyan Terminal</title>
<script type="text/javascript" src="files/terminal.js"></script>
<link rel="stylesheet" href="files/terminal.css"></link>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<h3>Welcome to TradeGyan Terminal</h3>
<?php
if(isset($error))
	echo "<div class='errorDiv'>$error</div>";
if(isset($info))
	echo "<div class='infoDiv'>$info</div>";
?>
	</div>
	<div id="body">
	<div id="adduser" style="float: right;">
		<form method="POST" action="" onsubmit="return addUsersValidate()" ><table style="text-align: center;">
		<thead>Add Users</thead>
		<tr><td><input type="text" name="appendCount" onclick="this.select()" onkeypress = "onlyNumbers(event)" value='0'/></td></tr>
		<tr><td><input type="submit" value="Add Users" name="addUsersSubmit" /></td></tr>
		</table></form>
	</div>
	<div id="commodity">
		<form method="POST" action="" ><table>
		<thead>Commodity</thead>
<?php
		$teamName='<tr><td>Team Name</td><td><select name="teamName" size="1">
		<option value="0"></option>';
$res=run_query('SELECT `id`,`name` FROM `user`;');
while($row=mysql_fetch_row($res))
	$teamName.="<option value='{$row[0]}'>{$row[1]}Team</option>";
$teamName.='</select></td></tr>';
echo $teamName;
?>
		<tr><td>Select product and enter weight</td><td><select name="product">
		<option value="0"></option>
<?php
$res=run_query('SELECT `product` FROM `product`;');
while($row=mysql_fetch_row($res)){
	echo "<option>{$row[0]}</option>";
}
?>
		</select><input type="text" name="weight" value="0" onclick="this.select()" onkeypress = "onlyNumbers(event)" /></td></tr>
		<tr><td colspan="2"><!--<input type="submit" name="commodityBuySubmit" onclick=" return commodityBuyValidate()" value="Buy" /><input type="submit" name="commoditySellSubmit" onclick="return commoditySellValidate()" value="Sell"/>--></td></tr>
		</table></form><br /><br />
	<!-- Banking the stocks -->
	<div id="banking">
		<form method="POST" action="" onsubmit="return commodityDepositValidate()" ><table>
		<thead>Deposite your commodity</thead>
<?php
		$teamName='<tr><td>Team Name</td><td><select name="teamName" size="1">
		<option value="0"></option>';
$res=run_query('SELECT `id`,`name` FROM `user`;');
while($row=mysql_fetch_row($res))
	$teamName.="<option value='{$row[0]}'>{$row[1]}Team</option>";
$teamName.='</select></td></tr>';
echo $teamName;
?>
		<tr><td>Select product and enter weight</td><td><select name="product">
		<option value="0"></option>
<?php
$res=run_query('SELECT `product` FROM `product`;');
while($row=mysql_fetch_row($res)){
	echo "<option>{$row[0]}</option>";
}
?>
		</select><input type="text" name="weight" value="0" onclick="this.select()"onkeypress = "onlyNumbers(event)" /></td></tr>
		<tr><td colspan="2"><!--<input type="submit" name="commodityDeposite" value="Deposit" />-->
		</table></form>
	</div>
<br/><br/><!-- End of banking-->
	</div>
	<div id="Company">
	<form method="POST" action="" ><table>
	<thead>Company</thead>
<?php echo $teamName; ?>
	<tr><td>Company</td><td><select size="1" name="company">
	<option value="0"></option>
<?php
$res=run_query('SELECT `varname`,`compname` FROM `company`;');
mysql_close($c);
while($row=mysql_fetch_row($res))
	echo "<option value='{$row[0]}'>{$row[1]}</option>";
?>
	</select></td></tr>
	<tr><td>No Of Shares</td><td><input type="text" value="0" name="sharesCount" onclick="this.select()" onkeypress = "onlyNumbers(event)" /></td></tr>
	<tr><td colspan="2"><!--<input type="submit" name="companyBuySubmit" value="Buy" onclick="return companyBuyValidate()" /><input type="submit" name="companySellSubmit" value="Sell" onclick="return companySellValidate()"/>--></td></tr>
	</table></form>
	</div>
	</div>
	<div id="footer">
	&copy; TradeGyan Team.
	</div>
</div>
</body>
</html>