/////////////////////////////////////////
// ����������
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
// �w�i�F
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
// ���O�C��
/////////////////////////////////////////
function GoLogin(){

	//��������
	intxt = form01.u_id.value;
	intxtlen = intxt.length;
	if (intxtlen < 1){
		alert("���[�UID����͂��Ă�������");
		form01.u_id.focus()
		return false
	}

    form01.submit();

}
