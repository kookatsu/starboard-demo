//---------------------------------------------------*
// ���ʊ֐��Q
//---------------------------------------------------*
// setCursorLast			�J�[�\������ԍŌ��
// CheckDate				���t�`�F�b�N
// IsNumeric				���l�ϊ�
// EnterToTab				�������������ꂽ�ꍇ�ATab�L�[�ɕϊ�����
// MyGoTopMenu				�g�b�v���j���[�֖߂�
// MyWinClose				Window�N���[�Y(���b�Z�[�W�Ȃ�)
// MyWondowLogout			���O�A�E�g
// numOnly					���͐���@�����̂�
// telOnly					���͐���@�����̂�(ʲ�ݕt��)
// numOnly2					���͐���@�����̂�(/�t��)
// numOnly3					���͐���@�����̂�(.�t��)
// chkKana					���p�̃J�i�������Ă��Ȃ����`�F�b�N����
// chkMaxLength				���������Ă���
// countLength				���������Ă��� �޲Čv�Z
//---------------------------------------------------*


/////////////////////////////////////////
// �J�[�\������ԍŌ��
/////////////////////////////////////////
function setCursorLast(objname) {

	var obj = document.getElementsByName( objname )[0]; //�e�L�X�g�{�b�N�X���w��
	obj.focus();     //�e�L�X�g�{�b�N�X�Ƀt�H�[�J�X���ړ�
	obj.value += ''; //�e�L�X�g�{�b�N�X���̕����񖖔��ɃJ�[�\�����ړ�

}
/////////////////////////////////////////
// ���t�`�F�b�N
/////////////////////////////////////////
function CheckDate(strYear,strMonth,strDay)
{
  if (IsNumeric(strYear) || IsNumeric(strMonth) || IsNumeric(strDay)) return false;
  var inYear = Math.round(strYear);
  var inMonth = Math.round(strMonth);
  var inDay = Math.round(strDay);
  var inDate = "" + inYear + "/" + inMonth + "/" + inDay;
  var newDate = new Date(inDate);
  var newYear = newDate.getFullYear();
  var newMonth = newDate.getMonth() + 1;
  var newDay = newDate.getDate();
  var outDate = newYear + "/" + newMonth + "/" + newDay;
  if(outDate != inDate)
  {
    return false;
  }
  return true;
}

/////////////////////////////////////////
// ���l�ϊ�
/////////////////////////////////////////
function IsNumeric(strNum){
  var bError = false;
  var nStart = 0;
  if ((strNum == "") || (strNum == null)) return true;
  for(var i = nStart ; i < strNum.length ; i++)
  {
    if ((strNum.charAt(i) < '0') || (strNum.charAt(i) > '9'))
    {
      bError = true;
      break;
    }
  }
  return bError;
}

/////////////////////////////////////////
// �������������ꂽ�ꍇ�ATab�L�[�ɕϊ�����
/////////////////////////////////////////
function EnterToTab(event){

  if(event.keyCode==13){
    event.keyCode=9;
  }

}
/////////////////////////////////////////
// �g�b�v���j���[�֖߂�
/////////////////////////////////////////
function MyGoTopMenu(){
  parent.location = '../menu/topmenu.php';
}

/////////////////////////////////////////
// Window�N���[�Y(���b�Z�[�W�Ȃ�)
/////////////////////////////////////////
function MyWinClose(){
	window.open("about:blank","_self").close();
}

/////////////////////////////////////////
// TOP���j���[��
/////////////////////////////////////////
function MyGoTopMenu(){
  parent.location = '../menu/topmenu.php'
}
/////////////////////////////////////////
// ���O�A�E�g
/////////////////////////////////////////
function MyWondowLogout(){
  parent.location = '../fjptlksm.php' //�J�X�~��p
}
/////////////////////////////////////////
// ���͐���@�����̂�
/////////////////////////////////////////
function numOnly() 
{
	m = String.fromCharCode(event.keyCode);
	if("0123456789\b\r".indexOf(m, 0) < 0) return false;
	return true;
}
/////////////////////////////////////////
// ���͐���@�����̂�(ʲ�ݕt��)
/////////////////////////////////////////
function telOnly() 
{
	m = String.fromCharCode(event.keyCode);
	if("-0123456789\b\r".indexOf(m, 0) < 0) return false;
	return true;
}
/////////////////////////////////////////
// ���͐���@�����̂�(/�t��)
/////////////////////////////////////////
function numOnly2() 
{
	m = String.fromCharCode(event.keyCode);
	if("/0123456789\b\r".indexOf(m, 0) < 0) return false;
	return true;
}
/////////////////////////////////////////
// ���͐���@�����̂�(.�t��)
/////////////////////////////////////////
function numOnly3() 
{
	m = String.fromCharCode(event.keyCode);
	if(".0123456789\b\r".indexOf(m, 0) < 0) return false;
	return true;
}

/////////////////////////////////////////
// ���p�̃J�i�������Ă��Ȃ����`�F�b�N����
// obj:�Ώۂ̵�޼ު��
/////////////////////////////////////////
function chkKana(obj) 
{
	hc = "�������������������������������������������ܦݧ���������������";

	for(i=0; i<obj.length; i++) 
	{
		if(hc.indexOf(obj.charAt(i),0) >= 0)
		{
			return false;
		}
	}
	return true;
}
/////////////////////////////////////////
// ���������Ă���
// obj:�Ώۂ̵�޼ު��
// strLength:���͍ő�l
/////////////////////////////////////////
function chkMaxLength(obj, strLength) 
{
	var tmpLength = countLength(obj.value);
	
    if(tmpLength > strLength) 
    {
        /* ���͕��������ő啶�����𒴂��Ă���ꍇ */
        return false;
	} else if(tmpLength == strLength) {
		return true;
    } else {
        /* ���͕��������ő啶�����ɖ����Ȃ��ꍇ */
		return true;
    }
}
/////////////////////////////////////////
// ���������Ă��� �޲Čv�Z
// str:��޼ު��
/////////////////////////////////////////
function countLength(str) { 
    var r = 0; 
    for (var i = 0; i < str.length; i++) { 
        var c = str.charCodeAt(i); 
        if ( (c >= 0x0 && c < 0x81) || (c == 0xf8f0) || (c >= 0xff61 && c < 0xffa0) || (c >= 0xf8f1 && c < 0xf8f4)) { 
            r += 1; 
        } else { 
            r += 2; 
        } 
    } 
    return r; 
}
