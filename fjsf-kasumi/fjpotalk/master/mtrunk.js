/////////////////////////////////////////
// 処理選択ﾎﾞﾀﾝ
/////////////////////////////////////////
function MyModClick(skmode)
{
	if (skmode == "sk0")
	{
		form01.bcd.value = "";
		form01.bcd.disabled = false;
		form01.bcd.focus();form01.bcd.select();
		form01.btn.value = "新規";
	} else if (skmode == "sk2"){
		form01.bcd.value = "";
		form01.bcd.disabled = false;
		form01.bcd.focus();form01.bcd.select();
		form01.btn.value = "削除";
	} else {
		form01.bcd.value = "";
		form01.bcd.disabled = false;
		form01.bcd.focus();form01.bcd.select();
		form01.btn.value = "修正";
	}

	form01.gaisenno.value = "";
	form01.gaisenname.value = "";
	form01.maintrunkno.value = "0";
	form01.trunkmemo.value = "";


	form01.btn.disabled = true;
	form01.comButton.value = "";
}
/////////////////////////////////////////
// コードクリック
/////////////////////////////////////////
function MyCodeClick()
{
	form01.gaisenno.value = "";
	form01.gaisenname.value = "";
	form01.maintrunkno.value = "0";
	form01.trunkmemo.value = "";

	form01.btn.disabled = true;
	form01.comButton.value = "";

}

/////////////////////////////////////////
// OKﾎﾞﾀﾝ
/////////////////////////////////////////
function MyComOK()
{
  form01.comButton.value="comok";
  form01.submit();
}
/////////////////////////////////////////
// クライアントの選択
/////////////////////////////////////////
function MyClientClick(){

  form01.endusercode.value="000";
  form01.comButton.value="comboselclient";
  form01.submit();
}


/////////////////////////////////////////
// 登録ﾎﾞﾀﾝ
/////////////////////////////////////////
function MyComAdd(){

	//入力ﾁｪｯｸ
	//OS名
	intxt = form01.gaisenno.value;
	intxtlen = intxt.length;
	if (intxtlen < 1){
		alert("外線番号を入力してください");
		form01.gaisenno.focus()
		return false
	}
	//文字数ﾁｪｯｸ
	var flag = chkMaxLength(document.form01.bname, 50);
	if (flag==false)
	{
		alert("入力文字数が最大文字数を超えています！\n\n(全角25文字/半角50文字)");
		form01.gaisenname.focus()
		return false;
	}


	flag = confirm("登録してもよろしいですか？");
	if(flag){
		form01.comButton.value="comadd";
		form01.submit();
	}else{
		return false;
	}
}
