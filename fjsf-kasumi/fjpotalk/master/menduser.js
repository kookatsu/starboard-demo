/////////////////////////////////////////
// 処理選択ﾎﾞﾀﾝ
/////////////////////////////////////////
function MyModClick(skmode)
{

	if (skmode == "sk0")
	{
		form01.ecd.value = "";
		form01.ecd.disabled = false;
		form01.ecd.focus();form01.ecd.select();
		form01.btn.value = "登録";
	} else if (skmode == "sk2"){
		form01.ecd.value = "";
		form01.ecd.disabled = false;
		form01.ecd.focus();form01.ecd.select();
		form01.btn.value = "削除";
	} else {
		form01.ecd.value = "";
		form01.ecd.disabled = false;
		form01.ecd.focus();form01.ecd.select();
		form01.btn.value = "更新";
	}

	form01.ename.value = "";
	form01.dspno.value = "";

	form01.btn.disabled = true;
	form01.comButton.value = "";
//	form01.submit();
}
/////////////////////////////////////////
// クライアントコンボクリック
/////////////////////////////////////////
function MyCComboClick()
{
	form01.ename.value = "";
	form01.dspno.value = "";

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
// 表示ﾎﾞﾀﾝ(一覧)
/////////////////////////////////////////
function MyIchiranDsp()
{
  form01.comButton.value="comDsp";
  form01.submit();
}

/////////////////////////////////////////
// 登録ﾎﾞﾀﾝ
/////////////////////////////////////////
function MyComAdd(){

	//入力ﾁｪｯｸ
	//名前
	intxt = form01.ename.value;
	intxtlen = intxt.length;
	if (intxtlen < 1){
		alert("エンドユーザ名を入力してください");
		form01.ename.focus()
		return false
	}

  flag = confirm("更新してもよろしいですか？");
  if(flag){
    form01.comButton.value="comadd";
    form01.submit();
  }else{
    return false;
  }
}


