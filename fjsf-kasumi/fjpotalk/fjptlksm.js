/////////////////////////////////////////
// ｴﾝﾀｰｷｰ制御
/////////////////////////////////////////
function u_id_down(evnt) {
	if(evnt.keyCode==13) {
	form01.u_pass.focus();
	}
}
function u_pass_down(evnt) {
	if(evnt.keyCode==13) {
	form01.btn1.focus();
	document.selection.empty();
	window.event.returnValue=false;
	}
}


/////////////////////////////////////////
// 背景色
/////////////////////////////////////////
function u_id_fcs() {
	form01.u_id.select();
	form01.u_id.className="bkcolor_on";
}
function u_id_fcsout() {
	form01.u_id.className="bkcolor_off";
}

function u_pass_fcs() {
	form01.u_pass.select();
	form01.u_pass.className="bkcolor_on";
}
function u_pass_fcsout() {
	form01.u_pass.className="bkcolor_off";
}
/////////////////////////////////////////
// ログイン
/////////////////////////////////////////
function GoLogin(){

	//入力ﾁｪｯｸ
	intxt = form01.u_id.value;
	intxtlen = intxt.length;
	if (intxtlen < 1){
		alert("ユーザIDを入力してください");
		form01.u_id.focus()
		return false
	}

    form01.submit();

}
