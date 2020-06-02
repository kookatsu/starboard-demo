/////////////////////////////////////////
// 張棟慖戰无垒
/////////////////////////////////////////
function MyModClick(skmode)
{
	if (skmode == "sk0")
	{
		form01.bcd.value = "";
		form01.bcd.disabled = false;
		form01.bcd.focus();form01.bcd.select();
		form01.btn.value = "怴婯";
	} else if (skmode == "sk2"){
		form01.bcd.value = "";
		form01.bcd.disabled = false;
		form01.bcd.focus();form01.bcd.select();
		form01.btn.value = "嶍彍";
	} else {
		form01.bcd.value = "";
		form01.bcd.disabled = false;
		form01.bcd.focus();form01.bcd.select();
		form01.btn.value = "廋惓";
	}

	form01.bname.value = "";
	form01.bdspno.value = "";

	form01.btn.disabled = true;
	form01.comButton.value = "";
}
/////////////////////////////////////////
// 僐乕僪僋儕僢僋
/////////////////////////////////////////
function MyCodeClick()
{
	form01.bname.value = "";
	form01.bdspno.value = "";

	form01.btn.disabled = true;
	form01.comButton.value = "";

}

/////////////////////////////////////////
// OK无垒
/////////////////////////////////////////
function MyComOK()
{
  form01.comButton.value="comok";
  form01.submit();
}


/////////////////////////////////////////
// 搊榐无垒
/////////////////////////////////////////
function MyComAdd(){

	//擖椡联
	//OS柤
	intxt = form01.bname.value;
	intxtlen = intxt.length;
	if (intxtlen < 1){
		alert("偍媞條柤傪擖椡偟偰偔偩偝偄");
		form01.bname.focus()
		return false
	}
	//暥帤悢联
	var flag = chkMaxLength(document.form01.bname, 100);
	if (flag==false)
	{
		alert("擖椡暥帤悢偑嵟戝暥帤悢傪挻偊偰偄傑偡両\n\n(慡妏50暥帤/敿妏100暥帤)");
		form01.bname.focus()
		return false;
	}


	flag = confirm("搊榐偟偰傕傛傠偟偄偱偡偐丠");
	if(flag){
		form01.comButton.value="comadd";
		form01.submit();
	}else{
		return false;
	}
}
