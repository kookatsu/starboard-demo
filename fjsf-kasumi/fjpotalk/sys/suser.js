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

	form01.bname.value = "";
	form01.bpass.value = "";
	form01.blsu.value = "";

	form01.btn.disabled = true;
	form01.comButton.value = "";
}
/////////////////////////////////////////
// コードクリック
/////////////////////////////////////////
function MyCodeClick()
{
	form01.bname.value = "";
	form01.bpass.value = "";
	form01.blsu.value = "";

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
// 登録ﾎﾞﾀﾝ
/////////////////////////////////////////
function MyComAdd(){

	//入力ﾁｪｯｸ
	//名前
	intxt = form01.bname.value;
	intxtlen = intxt.length;
	if (intxtlen < 1){
		alert("ユーザ名を入力してください");
		form01.bname.focus()
		return false
	}
	//ﾊﾟｽﾜｰﾄﾞ
	intxt = form01.bpass.value;
	intxtlen = intxt.length;
	if (intxtlen < 3){
		alert("ﾊﾟｽﾜｰﾄﾞを3文字以上です");
		form01.bpass.focus()
		return false
	}

  flag = confirm("登録してもよろしいですか？");
  if(flag){
    form01.comButton.value="comadd";
    form01.submit();
  }else{
    return false;
  }
}


