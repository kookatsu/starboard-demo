/////////////////////////////////////////
// �����I������
/////////////////////////////////////////
function MyModClick(skmode)
{

	if (skmode == "sk0")
	{
		form01.ecd.value = "";
		form01.ecd.disabled = false;
		form01.ecd.focus();form01.ecd.select();
		form01.btn.value = "�o�^";
	} else if (skmode == "sk2"){
		form01.ecd.value = "";
		form01.ecd.disabled = false;
		form01.ecd.focus();form01.ecd.select();
		form01.btn.value = "�폜";
	} else {
		form01.ecd.value = "";
		form01.ecd.disabled = false;
		form01.ecd.focus();form01.ecd.select();
		form01.btn.value = "�X�V";
	}

	form01.ename.value = "";
	form01.dspno.value = "";

	form01.btn.disabled = true;
	form01.comButton.value = "";
//	form01.submit();
}
/////////////////////////////////////////
// �N���C�A���g�R���{�N���b�N
/////////////////////////////////////////
function MyCComboClick()
{
	form01.ename.value = "";
	form01.dspno.value = "";

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
// �\������(�ꗗ)
/////////////////////////////////////////
function MyIchiranDsp()
{
  form01.comButton.value="comDsp";
  form01.submit();
}

/////////////////////////////////////////
// �o�^����
/////////////////////////////////////////
function MyComAdd(){

	//��������
	//���O
	intxt = form01.ename.value;
	intxtlen = intxt.length;
	if (intxtlen < 1){
		alert("�G���h���[�U������͂��Ă�������");
		form01.ename.focus()
		return false
	}

  flag = confirm("�X�V���Ă���낵���ł����H");
  if(flag){
    form01.comButton.value="comadd";
    form01.submit();
  }else{
    return false;
  }
}


