function onlyNumbers(event){
	var e = event;
	if(window.event){ // IE
		var charCode = e.keyCode;
	} else if (e.which) { // Safari 4, Firefox 3.0.4
		var charCode = e.which;
	}
	if (charCode > 31 && (charCode < 48 || charCode > 57)){
		e.preventDefault();
		return false;
	}
	return true;
}
function updateValidate(){
	if(confirm('Display the update \n"'+document.forms[0].update.options[document.forms[0].update.selectedIndex].text+'"?'))
		return true;
	return false;
}
function dividendValidate(){
	if(confirm('Provide '+document.forms[1].percent.options[document.forms[1].percent.selectedIndex].text+' Dividend for "'+document.forms[1].company.options[document.forms[1].company.selectedIndex].text+'"?'))
		return true;
	return false;
}
function bailoutValidate(){
	if(confirm('Provide Bailout of Rs. '+document.forms[2].amount.value+' for "'+document.forms[2].company.options[document.forms[2].company.selectedIndex].text+'"?'))
		return true;
	return false;
}
function addUsersValidate(){
	if(confirm('Add '+document.forms[0].appendCount.value+' new teams?'))
		return true;
	return false;
}
function commodityBuyValidate(){
	if(confirm('Buy '+document.forms[1].weight.value+'kgs of '+document.forms[1].product.options[document.forms[1].product.selectedIndex].text+' for Team "'+document.forms[1].teamName.options[document.forms[1].teamName.selectedIndex].text+'"?'))
		return true;
	return false;
}
function commoditySellValidate(){
	if(confirm('Sell '+document.forms[1].weight.value+'kgs of '+document.forms[1].product.options[document.forms[1].product.selectedIndex].text+' from Team "'+document.forms[1].teamName.options[document.forms[1].teamName.selectedIndex].text+'"?'))
		return true;
	return false;
}
function commodityDepositValidate(){
	if(confirm('Deposit '+document.forms[2].weight.value+'kgs of '+document.forms[2].product.options[document.forms[2].product.selectedIndex].text+' from Team "'+document.forms[2].teamName.options[document.forms[2].teamName.selectedIndex].text+'"?'))
		return true;
	return false;
}
function companyBuyValidate(){
	if(confirm('Buy '+document.forms[3].sharesCount.value+' shares of '+document.forms[3].company.options[document.forms[3].company.selectedIndex].text+' for Team "'+document.forms[3].teamName.options[document.forms[3].teamName.selectedIndex].text+'"?'))
		return true;
	return false;
}
function companySellValidate(){
	if(confirm('Sell '+document.forms[3].sharesCount.value+' shares of '+document.forms[3].company.options[document.forms[3].company.selectedIndex].text+' from Team "'+document.forms[3].teamName.options[document.forms[3].teamName.selectedIndex].text+'"?'))
		return true;
	return false;
}