window.onload=function(){
	var ajax;
	if(window.XMLHttpRequest)
		ajax=new XMLHttpRequest();
	else
		ajax=new ActiveXObject( 'Microsoft.XMLHTTP'); 
	ajax.onreadystatechange=function(){
		if(ajax.readyState==4 && ajax.status==200){
			var response= JSON.parse(ajax.responseText);
			if(document.getElementById('updateIn').innerHTML!=response.update)
				animateUpdate(response.update);
			document.getElementById('companies').innerHTML='<table id="companiesTable" cellspacing="0"><tbody><tr><th>Company</th><th>Base Price</th><th>Current Price</th><th>Volume Traded</th><th>Status</th></tr>'+response.company+'</tbody></table>'; 
			document.getElementById('currentPrices').innerHTML='<table id="pricesTable" cellspacing="0"><tbody><tr><th>Item</th><th>Base Price</th><th>Current Price</th><th>Volume Traded</th><th>Status</th></tr>'+response.price+'</tbody></table>'; 
			document.getElementById('ranking').innerHTML='<table id="rankingTable" cellspacing="0"><tbody><tr><th>Player</th><th>Rank</th></tr>'+response.ranking+'</tbody></table>';
		document.getElementById('actionStatus').innerHTML='Content Refreshed';
	}};
	window.setInterval(function(){
		document.getElementById('actionStatus').innerHTML='Refreshing... ';
		ajax.open("POST","files/table_content.php",true);
		ajax.send();
	},3500);
};
function animateUpdate(txt){
	var el=document.getElementById('updateIn');
	el.style.left='600px';
	document.getElementById('updateIn').innerHTML=txt;
	var t=window.setInterval(function(){
		el.style.left=(parseInt(el.style.left)-10)+'px';
		if(parseInt(el.style.left)<=0)
			window.clearInterval(t);
	},5);
}