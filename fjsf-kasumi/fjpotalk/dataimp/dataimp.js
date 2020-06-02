/////////////////////////////////////////
// 取込ﾎﾞﾀﾝ
/////////////////////////////////////////
function MyComAdd(){

	flag = confirm("取り込んでもよろしいですか？");
	if(flag){
		form01.comButton.value="comadd";

		var labelObj = document.getElementById("wait_label");
		labelObj.innerHTML = "しばらくお待ち下さい。。。";

		form01.submit();
	}else{
		return false;
	}
}
