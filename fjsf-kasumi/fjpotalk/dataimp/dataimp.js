/////////////////////////////////////////
// �捞����
/////////////////////////////////////////
function MyComAdd(){

	flag = confirm("��荞��ł���낵���ł����H");
	if(flag){
		form01.comButton.value="comadd";

		var labelObj = document.getElementById("wait_label");
		labelObj.innerHTML = "���΂炭���҂��������B�B�B";

		form01.submit();
	}else{
		return false;
	}
}
