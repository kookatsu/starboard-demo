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

	form01.bname.value = "";
	form01.bpass.value = "";
	form01.blsu.value = "";

	form01.btn.disabled = true;
	form01.comButton.value = "";
}
/////////////////////////////////////////
// �R�[�h�N���b�N
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
// OK����
/////////////////////////////////////////
function MyComOK()
{
  form01.comButton.value="comok";
  form01.submit();
}


/////////////////////////////////////////
// �o�^����
/////////////////////////////////////////
function MyComAdd(){

	//��������
	//���O
	intxt = form01.bname.value;
	intxtlen = intxt.length;
	if (intxtlen < 1){
		alert("���[�U������͂��Ă�������");
		form01.bname.focus()
		return false
	}
	//�߽ܰ��
	intxt = form01.bpass.value;
	intxtlen = intxt.length;
	if (intxtlen < 3){
		alert("�߽ܰ�ނ�3�����ȏ�ł�");
		form01.bpass.focus()
		return false
	}

  flag = confirm("�o�^���Ă���낵���ł����H");
  if(flag){
    form01.comButton.value="comadd";
    form01.submit();
  }else{
    return false;
  }
}


