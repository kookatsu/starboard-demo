/////////////////////////////////////////
// �����I������
/////////////////////////////////////////
function MyModClick(skmode)
{
	if (skmode == "sk0")
	{
		form01.bcd.value = "";
		form01.bcd.disabled = false;
		form01.bcd.focus();form01.bcd.select();
		form01.btn.value = "�V�K";
	} else if (skmode == "sk2"){
		form01.bcd.value = "";
		form01.bcd.disabled = false;
		form01.bcd.focus();form01.bcd.select();
		form01.btn.value = "�폜";
	} else {
		form01.bcd.value = "";
		form01.bcd.disabled = false;
		form01.bcd.focus();form01.bcd.select();
		form01.btn.value = "�C��";
	}

	form01.gaisenno.value = "";
	form01.gaisenname.value = "";
	form01.maintrunkno.value = "0";
	form01.trunkmemo.value = "";


	form01.btn.disabled = true;
	form01.comButton.value = "";
}
/////////////////////////////////////////
// �R�[�h�N���b�N
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
// OK����
/////////////////////////////////////////
function MyComOK()
{
  form01.comButton.value="comok";
  form01.submit();
}
/////////////////////////////////////////
// �N���C�A���g�̑I��
/////////////////////////////////////////
function MyClientClick(){

  form01.endusercode.value="000";
  form01.comButton.value="comboselclient";
  form01.submit();
}


/////////////////////////////////////////
// �o�^����
/////////////////////////////////////////
function MyComAdd(){

	//��������
	//OS��
	intxt = form01.gaisenno.value;
	intxtlen = intxt.length;
	if (intxtlen < 1){
		alert("�O���ԍ�����͂��Ă�������");
		form01.gaisenno.focus()
		return false
	}
	//����������
	var flag = chkMaxLength(document.form01.bname, 50);
	if (flag==false)
	{
		alert("���͕��������ő啶�����𒴂��Ă��܂��I\n\n(�S�p25����/���p50����)");
		form01.gaisenname.focus()
		return false;
	}


	flag = confirm("�o�^���Ă���낵���ł����H");
	if(flag){
		form01.comButton.value="comadd";
		form01.submit();
	}else{
		return false;
	}
}
