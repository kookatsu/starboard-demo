//---------------------------------------------------*
// 共通関数群
//---------------------------------------------------*
// setCursorLast			カーソルを一番最後に
// CheckDate				日付チェック
// IsNumeric				数値変換
// EnterToTab				ｴﾝﾀｰｷｰが押された場合、Tabキーに変換する
// MyGoTopMenu				トップメニューへ戻る
// MyWinClose				Windowクローズ(メッセージなし)
// MyWondowLogout			ログアウト
// numOnly					入力制御　数字のみ
// telOnly					入力制御　数字のみ(ﾊｲﾌﾝ付き)
// numOnly2					入力制御　数字のみ(/付き)
// numOnly3					入力制御　数字のみ(.付き)
// chkKana					半角のカナが入っていないかチェックする
// chkMaxLength				文字数をｶｳﾝﾄする
// countLength				文字数をｶｳﾝﾄする ﾊﾞｲﾄ計算
//---------------------------------------------------*


/////////////////////////////////////////
// カーソルを一番最後に
/////////////////////////////////////////
function setCursorLast(objname) {

	var obj = document.getElementsByName( objname )[0]; //テキストボックスを指定
	obj.focus();     //テキストボックスにフォーカスを移動
	obj.value += ''; //テキストボックス内の文字列末尾にカーソルを移動

}
/////////////////////////////////////////
// 日付チェック
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
// 数値変換
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
// ｴﾝﾀｰｷｰが押された場合、Tabキーに変換する
/////////////////////////////////////////
function EnterToTab(event){

  if(event.keyCode==13){
    event.keyCode=9;
  }

}
/////////////////////////////////////////
// トップメニューへ戻る
/////////////////////////////////////////
function MyGoTopMenu(){
  parent.location = '../menu/topmenu.php';
}

/////////////////////////////////////////
// Windowクローズ(メッセージなし)
/////////////////////////////////////////
function MyWinClose(){
	window.open("about:blank","_self").close();
}

/////////////////////////////////////////
// TOPメニューへ
/////////////////////////////////////////
function MyGoTopMenu(){
  parent.location = '../menu/topmenu.php'
}
/////////////////////////////////////////
// ログアウト
/////////////////////////////////////////
function MyWondowLogout(){
  parent.location = '../fjptlksm.php' //カスミ専用
}
/////////////////////////////////////////
// 入力制御　数字のみ
/////////////////////////////////////////
function numOnly() 
{
	m = String.fromCharCode(event.keyCode);
	if("0123456789\b\r".indexOf(m, 0) < 0) return false;
	return true;
}
/////////////////////////////////////////
// 入力制御　数字のみ(ﾊｲﾌﾝ付き)
/////////////////////////////////////////
function telOnly() 
{
	m = String.fromCharCode(event.keyCode);
	if("-0123456789\b\r".indexOf(m, 0) < 0) return false;
	return true;
}
/////////////////////////////////////////
// 入力制御　数字のみ(/付き)
/////////////////////////////////////////
function numOnly2() 
{
	m = String.fromCharCode(event.keyCode);
	if("/0123456789\b\r".indexOf(m, 0) < 0) return false;
	return true;
}
/////////////////////////////////////////
// 入力制御　数字のみ(.付き)
/////////////////////////////////////////
function numOnly3() 
{
	m = String.fromCharCode(event.keyCode);
	if(".0123456789\b\r".indexOf(m, 0) < 0) return false;
	return true;
}

/////////////////////////////////////////
// 半角のカナが入っていないかチェックする
// obj:対象のｵﾌﾞｼﾞｪｸﾄ
/////////////////////////////////////////
function chkKana(obj) 
{
	hc = "ｱｲｳｴｵｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃﾄﾅﾆﾇﾈﾉﾊﾋﾌﾍﾎﾏﾐﾑﾒﾓﾔﾕﾖﾗﾘﾙﾚﾛﾜｦﾝｧｨｩｪｫｬｭｮｯｰ､｡｢｣ﾞﾟ";

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
// 文字数をｶｳﾝﾄする
// obj:対象のｵﾌﾞｼﾞｪｸﾄ
// strLength:入力最大値
/////////////////////////////////////////
function chkMaxLength(obj, strLength) 
{
	var tmpLength = countLength(obj.value);
	
    if(tmpLength > strLength) 
    {
        /* 入力文字数が最大文字数を超えている場合 */
        return false;
	} else if(tmpLength == strLength) {
		return true;
    } else {
        /* 入力文字数が最大文字数に満たない場合 */
		return true;
    }
}
/////////////////////////////////////////
// 文字数をｶｳﾝﾄする ﾊﾞｲﾄ計算
// str:ｵﾌﾞｼﾞｪｸﾄ
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
