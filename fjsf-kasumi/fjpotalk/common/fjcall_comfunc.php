<?php
//---------------------------------------------------*
// 共通関数群
//---------------------------------------------------*
// ShowFooter				フッター表示 (TOPメニュー以外)
// GetKaisenInfo			mcompanyからデスクとクライアントコードを取得
// GetSystemLevel			mcompanyから権限ﾚﾍﾞﾙ情報を取得
// GetSysStartNen			mcompanyからシステム開始年を取得
// CalcDateStartDay			引数の年月日を元にSystemInfoから締日を取得し、その月度の開始日を計算
// GetHoliday_OPEN			holidayinfoを元に祝日かどうかをチェック(既にDBオープン済みVer)
// SetClientCombo			クライアントｺﾝﾎﾞの作成
// SetEndUserCombo			エンドユーザーｺﾝﾎﾞの作成
// GetEndUserName			エンドユーザー名の取得
// SetNenCombo				年ｺﾝﾎﾞの作成
// SetTukiCombo				月ｺﾝﾎﾞの作成
// SetHiCombo				日ｺﾝﾎﾞの作成
// GetWeekStr				曜日名の取得
// GetLongDate				システム日付を整数で返す
// getDDList				日付ｺﾝﾎﾞﾎﾞｯｸｽの作成
// ch_in_String				文字の長さ判定
// ch_in_String				文字の長さ判定
// len_byte					バイト数を返す
// Replace_Fullpitch		Replace_Fullpitch
// StrToUnixTime			日付文字列をUnixタイムスタンプに変換する
// DateDiff					日付の差分を取得する
// GetUserFace				suserから写真ファイル名を取得
// us_Date					数値日付をフォーマットする 19991201→1999/12/01
// ch_Date					日付 変換できないときはnullを返す
// nz						数値であれば数値を、そうでなければ０を返す
// is_Number_only			全部数字かを返す
//////////////////////////////////////////
// フッター表示 TOPメニュー以外)
//////////////////////////////////////////
function ShowFooter(){
?>
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="center" id="copyright" name="copyright">ver.1.0.2&nbsp&nbsp&nbsp&nbsp;
					Copyright&nbsp;&copy;&nbsp;<?php print date("Y")?>&nbsp;Fujimoto&nbsp;Corporation&nbsp;All&nbsp;Rights&nbsp;Reserved.
			</td>
		</tr>
	</table>
<?php
}
?>
<?php
//////////////////////////////////////////
//<説明>
//  完了理由→No
//<引数>
// $wStr :完了理由
//<戻り値>
//  完了理由No
//////////////////////////////////////////
function SfCloseresonNameToNo( $wStr ){

	$SfCloseresonNo = 52; //未完了

	//1は総件数でのFLGで使用中の為、2から使う


	if($wStr == "コール対応"){
		$SfCloseresonNo = 11;
	}elseif($wStr == "出向（内部）"){
		$SfCloseresonNo = 12;
	}elseif($wStr == "出向（外部）"){
		$SfCloseresonNo = 13;
	}elseif($wStr == "返答受領（外部）"){
		$SfCloseresonNo = 14;
	}elseif($wStr == "返答受領（内部）"){
		$SfCloseresonNo = 15;
	}elseif($wStr == "外部依頼"){
		$SfCloseresonNo = 16;
	}elseif($wStr == "自己解決"){
		$SfCloseresonNo = 17;
	}elseif($wStr == "社内解決"){
		$SfCloseresonNo = 18;
	}elseif($wStr == "キャンセル"){
		$SfCloseresonNo = 19;
	}elseif($wStr == "手動完了"){
		$SfCloseresonNo = 20;
	}else{
	}

	return $SfCloseresonNo;

}
//////////////////////////////////////////
//<説明>
//  No→完了理由
//<引数>
// $wNo :No
//<戻り値>
//  完了理由名
//////////////////////////////////////////
function SfCloseresonNoToName( $wNo ){

	$SfCloseresonName = "";


	if($wNo == 11){
		$SfCloseresonName = "コール対応";
	}elseif($wNo == 12){
		$SfCloseresonName = "出向（内部）";
	}elseif($wNo == 13){
		$SfCloseresonName = "出向（外部）";
	}elseif($wNo == 14){
		$SfCloseresonName = "返答受領（外部）";
	}elseif($wNo == 15){
		$SfCloseresonName = "返答受領（内部）";
	}elseif($wNo == 16){
		$SfCloseresonName = "外部依頼";
	}elseif($wNo == 17){
		$SfCloseresonName = "自己解決";
	}elseif($wNo == 18){
		$SfCloseresonName = "社内解決";
	}elseif($wNo == 19){
		$SfCloseresonName = "キャンセル";
	}elseif($wNo == 20){
		$SfCloseresonName = "手動完了";
	}else{
	}

	return $SfCloseresonName;

}

//////////////////////////////////////////
//<説明>
//  mcompanyから会社名と回線数を取得
//////////////////////////////////////////
function GetCompanyInfo1(){

global $ENV_MODE;
global $Const_DB_SCHEMA;
global $Const_COMPANYCODE;
global $MOJI_ORG;
global $MOJI_NEW;


	$companyname = "";
	$sysclient = "000";
	$sysdesk = 0;
	$sysend = "000";


	$conn = db_connect();

	$sql = "SELECT companyname,clientcode,deskcode,endusercode FROM " . $Const_DB_SCHEMA . "mcompany";
	$sql = $sql . " WHERE companycode='" . $Const_COMPANYCODE . "'";
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		if( $ENV_MODE == 1){
			$companyname = mb_convert_encoding( $rs['companyname'], $MOJI_NEW, $MOJI_ORG); //文字コード変換
		}else{
			$companyname = $rs['companyname'];
		}
		$sysclient = $rs['clientcode'];
		$sysdesk = $rs['deskcode'];
		$sysend = $rs['endusercode'];

	}
	$result = null;
	$conn = null;

	return array( $companyname, $sysclient, $sysdesk, $sysend );

}
//////////////////////////////////////////
//<説明>
//  mcompanyからデスクとクライアントコードを取得
//////////////////////////////////////////
function GetKaisenInfo_OPEN($conn){

global $Const_DB_SCHEMA;
global $Const_COMPANYCODE;
global $ENV_MODE;
global $MOJI_ORG;
global $MOJI_NEW;


	$talksu = 0;
	$guidance = 0;
	$basestorecnt = 0;
	$notstorecnt = 0;
	$reportname = "";
	$reportnamelastupdate = 0;


	$sql = "SELECT talksu,guidance,basestorecnt,notstorecnt,reportname,reportnamelastupdate, reportfilename FROM " . $Const_DB_SCHEMA . "mcompany";
	$sql = $sql . " WHERE companycode='" . $Const_COMPANYCODE . "'";
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		$talksu = $rs['talksu'];
		$guidance = $rs['guidance'];
		$basestorecnt = $rs['basestorecnt'];
		$notstorecnt = $rs['notstorecnt'];
		$reportnamelastupdate = $rs['reportnamelastupdate'];
		$reportfilename = $rs['reportfilename'];
		if( $ENV_MODE == 1){
			$reportname = mb_convert_encoding( $rs['reportname'], $MOJI_NEW, $MOJI_ORG); //文字コード変換
		}else{
			$reportname =  $rs['reportname'];
		}
	}
	$result = null;

	return array( $talksu, $guidance, $basestorecnt,$notstorecnt,$reportname, $reportnamelastupdate, $reportfilename );

}
//////////////////////////////////////////
//<説明>
//  mcompanyから権限ﾚﾍﾞﾙ情報を取得
//////////////////////////////////////////
function GetSystemLevel(){

global $Const_DB_SCHEMA;
global $Const_COMPANYCODE;


	$sysLevel0Name = "level0";
	$sysLevel1Name = "level1";

	$conn = db_connect();

	$sql = "SELECT * FROM " . $Const_DB_SCHEMA . "mcompany";
	$sql = $sql . " WHERE companycode='" . $Const_COMPANYCODE . "'";
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		$sysLevel0Name = $rs['level0name'];
		$sysLevel1Name = $rs['level1name'];
	}
	$result = null;
	$conn = null;

	return array($sysLevel0Name,$sysLevel1Name);

}
//////////////////////////////////////////
//<説明>
//  mcompanyから運用開始年を取得
//////////////////////////////////////////
function GetSysStartNen(){

global $Const_DB_SCHEMA;
global $Const_COMPANYCODE;

	$wStartNen = 2000;

	$conn = db_connect();

	$sql = "SELECT systemstartyear FROM " . $Const_DB_SCHEMA . "mcompany";
	$sql = $sql . " Where companycode='" . $Const_COMPANYCODE . "'";
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		$wStartNen = $rs['systemstartyear'];
	}
	$result = null;
	$conn = null;

	return $wStartNen;

}
//////////////////////////////////////////
//<説明>
//  月末日計算
//<引数>
// $wYY :対象年
// $wMM :対象月
// $wMode :0:本年 -1:前年 -2:前々年 (遡りたい年数)
//<戻り値>
// $fromYc:開始年(YYYY) $fromMc:開始月(MM) $fromDc:開始日(DD)
// $toYc:終了年(YYYY) $toMc:終了月(MM) $toDc:終了日(DD)
//////////////////////////////////////////
function CalcDateFromTo( $wYY, $wMM, $wMode ){

	//引数を桁数あわせ
	$wYY = sprintf("%04d",$wYY + $wMode); //ここで遡る年を計算
	$wMM = sprintf("%02d",$wMM);

	$sysShimebi = 31;
	$sysShimebiFlg = 0;

	//開始日は1日
	$fromYc = $wYY;
	$fromMc = $wMM;
	$fromDc = "01";
	//終了日は月末日
	$toYc = $wYY;
	$toMc = $wMM;
	$endymd = date("Ymd", mktime(0, 0, 0, $wMM, "1", $wYY));
	$endymd = date("Ymt", strtotime($endymd) );//月末
	$toDc = abs(date('d',strtotime($endymd)));


	return array($fromYc, $fromMc, $fromDc, $toYc, $toMc, $toDc );

}

///////////////////////////////////////////////////////////////////////////
//<説明>
//  引数の年月日を元にSystemInfoから締日を取得し、その月度の開始日を計算
//<引数>
// $wYY :対象年
// $wMM :対象月
// $wMM :対象日
// $wMode :0:本年 -1:前年 -2:前々年 (遡りたい年数)
//<戻り値>
// $fromYc:開始年(YYYY) $fromMc:開始月(MM) $fromDc:開始日(DD)
///////////////////////////////////////////////////////////////////////////
function CalcDateStartDay( $wYY, $wMM, $wDD, $wMode ){

global $Const_DB_SCHEMA;
global $Const_COMPANYCODE;

	//引数を桁数あわせ
	$wYY = sprintf("%04d",$wYY + $wMode); //ここで遡る年を計算
	$wMM = sprintf("%02d",$wMM);
	$wDD = sprintf("%02d",$wDD);

	$sysShimebi = 31;
	$sysShimebiFlg = 0;

	//DBオープン
	$conn = db_connect();

	$sql = "SELECT shimebi FROM " . $Const_DB_SCHEMA . "mcompany";
	$sql = $sql . " Where companycode='" . $Const_COMPANYCODE . "'";
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		$sysShimebi = $rs['shimebi'];
	}
	$result = null;
	$conn = null;


	//締日が28日以降の設定は月末扱いの為、1日でOK 
	if( $sysShimebi>=28 ){
		$fromYc = $wYY;
		$fromMc = $wMM;
		$fromDc = "01";

	//締日が1日～27日の場合
	}else{
		$wDay = 0 + $wDD;
		//締日より選択日の方が大きい→開始日：当月の締日+1日
		if( $wDay>$sysShimebi ){
			$fromYc = $wYY;
			$fromMc = $wMM;
			$fromDc = sprintf("%02d",$sysShimebi + 1);
		//締日より選択日の方が小さい→開始日：前月の締日+1日
		}else{
			//開始日は前月の締日+1日
			$startymd = date("Ymd", mktime(0, 0, 0, $wMM-1, $sysShimebi+1 , $wYY));
			$fromYc = abs(date('Y',strtotime($startymd)));
			$fromMc = abs(date('m',strtotime($startymd)));
			$fromMc = sprintf("%02d",$fromMc );
			$fromDc = abs(date('d',strtotime($startymd)));
			$fromDc = sprintf("%02d",$fromDc );
		}
	}


	return array($fromYc, $fromMc, $fromDc );

}

//////////////////////////////////////////
//<説明>
//  holidayinfoを元に祝日かどうかをチェック
//<引数>
//  $wYear :年(YYYY)
//  $wMonth:月(MM)
//  $wDay  :日(DD)
//<戻り値>
//  祝日の名称(祝日でない場合は、"")
//////////////////////////////////////////
function GetHoliday_OPEN( $conn, $wYear, $wMonth, $wDay ){

global $ENV_MODE;
global $Const_DB_SCHEMA;
global $MOJI_ORG;
global $MOJI_NEW;

	$GetHoliday = "";

	//年の桁数あわせ
	$wYear += 0;
	$wYear = "0000".$wYear;
	$wYearlen = strlen($wYear);
    $wYear = substr($wYear,$wYearlen-4,4);

	//月の桁数あわせ
	$wMonth += 0;
	$wMonth = "00".$wMonth;
	$wMonthlen = strlen($wMonth);
    $wMonth = substr($wMonth,$wMonthlen-2,2);

	//日の桁数あわせ
	$wDay += 0;
    $wDay = "00".$wDay;
	$wDaylen = strlen($wDay);
    $wDay = substr($wDay,$wDaylen-2,2);

	//月日の作成
	$wMonthDay = $wMonth.$wDay;


$sql = <<<EOS
      SELECT * FROM $Const_DB_SCHEMA holidayinfo 
        WHERE
          year = '$wYear'
		And
          monthDay = '$wMonthDay'
EOS;
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		if( $ENV_MODE == 1){
			$GetHoliday = mb_convert_encoding( $rs['holidayname'], $MOJI_NEW, $MOJI_ORG); //文字コード変換
		}else{
			$GetHoliday = $rs['holidayname'];
		}
	}
	$result = null;

	return $GetHoliday;

}
//////////////////////////////////////////
// デスクのｺﾝﾎﾞﾎﾞｯｸｽ作成
// 初期値の候補選択
// $wDefDeskCode:対象のデスク
//////////////////////////////////////////
function SetDeskCombo( $wDefDeskCode ) {

global $ENV_MODE;
global $Const_DB_SCHEMA;
global $MOJI_ORG;
global $MOJI_NEW;

	$conn = db_connect();

	$sql = "SELECT deskcode, deskname FROM " . $Const_DB_SCHEMA . "mdesk";
	$sql = $sql . " ORDER By deskcode";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		if( $ENV_MODE == 1){
			$wdeskname = mb_convert_encoding( $rs['deskname'], $MOJI_NEW, $MOJI_ORG); //文字コード変換
		}else{
			$wdeskname = $rs['deskname'];
		}

		//ｺﾝﾎﾞの初期表示
		if ($wDefDeskCode == $rs['deskcode']){
			print "<option value='" . $rs['deskcode'] . "' selected>" . $wdeskname .  "</option>"; 
		}else{
			print "<option value='" . $rs['deskcode'] . "'>" . $wdeskname .  "</option>"; 
		}
	}
	$result = null;
	$conn = null;
}

//////////////////////////////////////////
// クライアントのｺﾝﾎﾞﾎﾞｯｸｽ作成
// 初期値の候補選択
// $wDefClientCode:対象のクライアント
// $wMode:0→候補の1行目にｶﾞｲﾀﾞﾝｽを入れない(必須選択用)
//         1→候補の1行目にｶﾞｲﾀﾞﾝｽを入れる(任意選択用)
//////////////////////////////////////////
function SetClientCombo( $wDefClientCode, $wMainDeskCode, $wMode ) {

global $ENV_MODE;
global $Const_DB_SCHEMA;
global $MOJI_ORG;
global $MOJI_NEW;

	$conn = db_connect();

	if($wMode==1){
		//１行目のﾃﾞﾌｫﾙﾄ値をｾｯﾄ
		print "<option value='000'>▼指定なし</option>"; 
	}
	if($wMode==2){
		//１行目のﾃﾞﾌｫﾙﾄ値をｾｯﾄ
		print "<option value='ALL'>▼全クライアント</option>"; 
	}

	$sql = "SELECT clientcode, clientname FROM " . $Const_DB_SCHEMA . "mclient";
	if($wMainDeskCode != 999){ //自分のデスクのみ
		$sql = $sql . " WHERE deskcode =" . $wMainDeskCode;
	}
	$sql = $sql . " ORDER By dspno, clientcode";

	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		if( $ENV_MODE == 1){
			$clientname = mb_convert_encoding( $rs['clientname'], $MOJI_NEW,$MOJI_ORG); //文字コード変換
		}else{
			$clientname = $rs['clientname'];
		}

		//ｺﾝﾎﾞの初期表示
		if ($wDefClientCode == $rs['clientcode']){
			print "<option value='" . $rs['clientcode'] . "' selected>" . $clientname .  "</option>"; 
		}else{
			print "<option value='" . $rs['clientcode'] . "'>" . $clientname .  "</option>"; 
		}
	}
	$result = null;
	$conn = null;
}
//////////////////////////////////////////
// エンドユーザのｺﾝﾎﾞﾎﾞｯｸｽ作成
// 初期値の候補選択
// $wClientCode:対象のクライアント
// $wDefEndUserCode:対象のエンドユーザ
// $wMode:0→候補の1行目にｶﾞｲﾀﾞﾝｽを入れない(必須選択用)
//         1→候補の1行目にｶﾞｲﾀﾞﾝｽを入れる(任意選択用)
//////////////////////////////////////////
function SetEndUserCombo( $wClientCode, $wDefEndUserCode, $wMode ) {

global $ENV_MODE;
global $Const_DB_SCHEMA;
global $MOJI_ORG;
global $MOJI_NEW;

	$conn = db_connect();

	if($wMode==1){
		//１行目のﾃﾞﾌｫﾙﾄ値をｾｯﾄ
		print "<option value='000'>▼選択して下さい</option>"; 
	}
	if($wMode==2){
		//１行目のﾃﾞﾌｫﾙﾄ値をｾｯﾄ
		print "<option value='ALL'>▼全ユーザ</option>"; 
	}

	$sql = "SELECT endusercode, endusername FROM " . $Const_DB_SCHEMA . "menduser";
	$sql = $sql . " WHERE clientcode='" . $wClientCode . "'";
	$sql = $sql . " ORDER By dspno, endusercode";
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		if( $ENV_MODE == 1){
			$endusername = mb_convert_encoding( $rs['endusername'], $MOJI_NEW,$MOJI_ORG); //文字コード変換
		}else{
			$endusername = $rs['endusername'];
		}

		//ｺﾝﾎﾞの初期表示
		if ($wDefEndUserCode == $rs['endusercode']){
			print "<option value='" . $rs['endusercode'] . "' selected>" . $endusername .  "</option>"; 
		}else{
			print "<option value='" . $rs['endusercode'] . "'>" . $endusername .  "</option>"; 
		}
	}
	$result = null;
	$conn = null;
}
//////////////////////////////////////////
// クライアント名の取得
// $wClientCode:対象のクライアント
//////////////////////////////////////////
function GetClientName( $conn, $wClientCode ) {

global $ENV_MODE;
global $Const_DB_SCHEMA;
global $MOJI_ORG;
global $MOJI_NEW;

	$l_cname = "";

	$sql = "SELECT clientname FROM " . $Const_DB_SCHEMA . "mclient";
	$sql = $sql . " WHERE clientcode ='" . $wClientCode . "'";
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		if( $ENV_MODE == 1){
			$l_cname = mb_convert_encoding( $rs['clientname'], $MOJI_NEW,$MOJI_ORG); //文字コード変換
		}else{
			$l_cname = $rs['clientname'];
		}
	}
	$result = null;

	return $l_cname;

}

//////////////////////////////////////////
// エンドユーザ名の取得
// $wClientCode:対象のクライアント
// $wDefEndUserCode:対象のエンドユーザ
//////////////////////////////////////////
function GetEndUserName( $conn, $wClientCode, $wEndUserCode ) {

global $ENV_MODE;
global $Const_DB_SCHEMA;
global $MOJI_ORG;
global $MOJI_NEW;

	$l_ename = "";

	$sql = "SELECT endusername FROM " . $Const_DB_SCHEMA . "menduser";
	$sql = $sql . " WHERE clientcode ='" . $wClientCode . "'";
	$sql = $sql . " AND   endusercode ='" . $wEndUserCode . "'";
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		if( $ENV_MODE == 1){
			$l_ename = mb_convert_encoding( $rs['endusername'], $MOJI_NEW,$MOJI_ORG); //文字コード変換
		}else{
			$l_ename = $rs['endusername'];
		}
	}
	$result = null;

	return $l_ename;

}
//////////////////////////////////////////
//<説明>
//  年ｺﾝﾎﾞの作成
//<引数>
//  $def_from:開始年
//  $def_to  :終了年
//  $wSelectData  :選択したいﾃﾞｰﾀ
//////////////////////////////////////////
function SetNenCombo($def_from,$def_to,$wSelectData) {

	$wSelectData += 0;
/*
	if($wSelectData == 0){
		print "<option value=0 selected>" . "-" .  "</option>"; 
	}else{
		print "<option value=0 >" . "-" .  "</option>"; 
	}
*/
	for( $i = $def_from; $i <= $def_to; $i++ ){
		if( $i == $wSelectData ){
			print "<option value=" . $i . " selected>" . $i . "</option>";
		}else{
			print "<option value=" . $i . ">" .$i . "</option>";
		}
	}
}
//////////////////////////////////////////
//<説明>
//  月ｺﾝﾎﾞの作成
//<引数>
//  $wSelectData  :選択したいﾃﾞｰﾀ
//////////////////////////////////////////
function SetTukiCombo($wSelectData) {

	$wSelectData += 0;
	if($wSelectData == 0){
	$wSelectData = 1;
	}
/*
	if($wSelectData == 0){
		print "<option value=0 selected>" . "-" .  "</option>"; 
	}else{
		print "<option value=0 >" . "-" .  "</option>"; 
	}
*/
	for( $i = 1; $i <= 12; $i++ ){
		if( $i == $wSelectData ){
			print "<option value=" . $i . " selected>" . $i . "</option>";
		}else{
			print "<option value=" . $i . ">" .$i . "</option>";
		}
	}
}
//////////////////////////////////////////
//<説明>
//  日ｺﾝﾎﾞの作成
//<引数>
//  $wSelectData  :選択したいﾃﾞｰﾀ
//////////////////////////////////////////
function SetHiCombo($wSelectData) {

	for( $i = 1; $i <= 31; $i++ ){
		if( $i == $wSelectData ){
			print "<option value=" . $i . " selected>" . $i . "</option>";
		}else{
			print "<option value=" . $i . ">" .$i . "</option>";
		}
	}
}

//////////////////////////////////////////
//<説明>
//  曜日名の取得
//<引数>
//  $dt :対象日付
//////////////////////////////////////////
function GetWeekStr( $dt )
{
	$arr = Array("日","月","火","水","木","金","土");

	$i = date( "w" , $dt );
	if( $i < count( $arr ) ){
		return $arr[$i];
	}
	return "";
}
//////////////////////////////////////////
//<説明>
//  システム日付を整数で返す
//////////////////////////////////////////
function GetLongDate() 
{
	$xYear = strftime("%Y",time());
	$xMonth = strftime("%m",time());
	$xDay = strftime("%d",time());

	$GetLongDate = $xYear * 10000 + $xMonth * 100 + $xDay;

	return $GetLongDate;
}

//////////////////////////////////////////
// 文字の長さ判定
// $MinStr:最小文字数
// $MaxStr=最大文字数
//////////////////////////////////////////
function ch_in_String($str, $MinStr, $MaxStr) {

	$ch_in_String = null;

	if ($MinStr > 0 && (is_Null($str) || mb_strlen($str) < 1)) {
		$ch_in_String="入力してください";
		return array($ch_in_String, $str);
	} elseif (len_byte($str) < $MinStr) {
		$ch_in_String="文字数が短すぎます";
		return array($ch_in_String, $str);
	} elseif (len_byte($str) > $MaxStr) {
		$ch_in_String="文字数が長すぎます";
		return array($ch_in_String, $str);
	}

	if (is_Null($ch_in_String) && is_Null($str)) {
		$str = "";
	}

	//全角空白を半角に変換
	$ck = 1;
	while($ck > 0) {
		//対象の文字は何番目？（0から）
		$ck = mb_strpos($str,"　");

		if ($ck > 0) {
			$str = mb_substr($str, 0, $ck) . " " . mb_substr($str, $ck + 1);
		}
	}

	return array($ch_in_String, $str);

}
//////////////////////////////////////////
// バイト数を返す
//////////////////////////////////////////
function len_byte($str) 
{
	$i = null;

	$len_byte=0;

	if (strlen($str) > 0) 
	{
		for ($i = 1; $i <= strlen($str); $i = $i + 1) 
		{
			$len_byte = $len_byte + 1;

			if (!(ord(substr($str,$i - 1,1)) >= 0 && ord(substr($str,$i - 1,1))<= "0xFF")) 
			{
				$len_byte = $len_byte + 1;
			}
		}
	}

	return $len_byte;
}

//////////////////////////////////////////
// 文字列置換-->半角を全角に強制変換
// ①'(半角)→’(全角)
// ②,(半角)→，(全角)
//////////////////////////////////////////
function Replace_Fullpitch($wStr) 
{
	//'(半角)→’(全角)
	$wStr =str_replace("'", "’", $wStr);
	//,(半角)→，(全角)
	$wStr =str_replace(",", "，", $wStr);

	return $wStr;
}
//////////////////////////////////////////
//<説明>
// 日付文字列をUnixタイムスタンプに変換する
//<引数>
//  $s :YYYY/MM/DD
//////////////////////////////////////////
function StrToUnixTime( $s )
{
	$res = time();
	
	$arr = split( "/" , $s );
	if( count( $arr ) >= 3 ){
		$y = $arr[0];
		$m = $arr[1];
		$d = $arr[2];
		
		$res = mktime( 0 , 0 , 0 , $m , $d , $y );
	}
	
	return $res;
}
//////////////////////////////////////////
//<説明>
// 日付の差分を取得する
//<引数>
//  $d1 :比較日付
//  $d2 :比較日付
//////////////////////////////////////////
function DateDiff( $d1 , $d2 ){

	$y1 = date( "Y" , $d1 );
	$y2 = date( "Y" , $d2 );

	$m1 = date( "n" , $d1 );
	$m2 = date( "n" , $d2 );

	$d1 = date( "j" , $d1 );
	$d2 = date( "j" , $d2 );

	$tm1 = mktime( 0 , 0 , 0 , $m1 , $d1 , $y1 );
	$tm2 = mktime( 0 , 0 , 0 , $m2 , $d2 , $y2 );

	// 秒数で差分取得
	$diff = $tm1 - $tm2;

	// 日に変換
	$diff = intval( $diff / 3600 / 24 );

	return $diff;
}
//////////////////////////////////////////
//<説明>
//  suserから写真ファイル名を取得
//////////////////////////////////////////
function GetUserFace($wCode){

	$wName = "noimage.gif";

	$conn = db_connect();

$sql = <<<EOS
      SELECT facefile FROM suser 
		WHERE userid='$wCode'
EOS;
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		$wName = $rs['facefile'];
	}
	$result = null;
	$conn = null;

	return $wName;

}



//////////////////////////////////////////
//<説明>
//  数値日付をフォーマットする 19991201→1999/12/01
//<引数>
//  $lDate :対象日付(YYYYMMDD)
//////////////////////////////////////////
function us_Date($lDate) 
{
	$us_Date = null;

	if (nz($lDate) < 1) 
	{
		$us_Date = "";
		return $us_Date;
	}

	$x = ch_Date($lDate);

	if (is_Null($x)) 
	{
		$us_Date = $lDate;
	} else {

		if ($x == 0) 
		{
			$us_Date = "";
		} else {
			if (floor($x / 10000) == 0) 
			{
				$us_Date = sprintf("%02d",(($x / 100) % 100)) . "/" . sprintf("%02d",$x % 100);
			} else {
				$us_Date = floor($x / 10000) . "/" . sprintf("%02d",(($x / 100) % 100)) . "/" . sprintf("%02d",$x % 100);
			}
		}
	}
	return $us_Date;
}
//////////////////////////////////////////
//<説明>
//  日付 変換できないときはnullを返す
//<引数>
//  $str :文字列
//////////////////////////////////////////
function ch_Date($str) 
{
	$ch_Date = Null;

	$ch_Date = ch_DateSub($str, 0);

	return $ch_Date;
}
//日付変換 mode : 0 = 年月自動, 1 = 年省略可能
function ch_DateSub($str, $mode) 
{
	$ch_DateSub = null;

	if (is_Null($str) || strlen($str) < 1) 
	{
		return $ch_DateSub;
	}

	$i = null; $m = null; $mc = null; $mb = null; $s = null; $ck = null;
	$h1 = null; $h2 = null; $h3 = null; $wa = null; $sin = null;

	$sin = $str;

	$ck = 0;
	$wa = "";

	if (is_Number_only($sin)) 
	{
		$m = $sin;
	} else {
		$s = strtoupper(substr($sin,0,1)); //引数の文字列に含まれる英単語すべてを大文字英字

		if ($s == "S" || $s == "T" || $s == "M" || $s == "H") 
		{
			$wa = $s;
//      	$sin = 0;
			$sin = substr($sin, (strlen($sin) - 1) * (-1));
		}

		if (is_Number_only($sin)) 
		{
			$m = $sin;
		} else {
			$mb = "";
			$mc = 0;

			for ($i = 1; $i <= strlen($sin); $i = $i + 1) 
			{
				$s = substr($sin, $i - 1, 1);

				if ($s >= "0" && $s <= "9") 
				{
					$mb = $mb . $s;
					$mc = $mc + 1;
				} else {
					if ($s == "-" || $s == "/") 
					{
						$ck = $ck + 1;
						if ($mc < 2) 
						{
							$mb = "0".$mb;
						}

						if ($ck == 1 && $mc > 4) 
						{
							$ck = 9;
						}

						if ($ck == 2 && $mc > 2) 
						{
							$ck=9;
						}

						if ($ck == 3 && $mc > 2) 
						{
							$ck = 9;
						}
						$m = $m.$mb;
						$mb = "";
						$mc = 0;
					} else {
						$ck = 1;
					}
				}
			}

			if ($mc > 0) 
			{
				$ck = $ck + 1;
				if ($mc < 2) 
				{
					$mb = "0" . $mb;
				}
				if ($ck == 1 && $mc > 4) 
				{
					$ck = 9;
				}
				if ($ck == 2 && $mc > 2) 
				{
					$ck=9;
				}
				if ($ck == 3 && $mc > 2) 
				{
					$ck=9;
				}
				$m = $m . $mb;
			}
    	}
	}

	if ($ck>3) 
	{
		return $ch_DateSub;
	}

	$m = sprintf("%08d", $m);

	$h1 = intval(substr($m,0,4));
	$h2 = intval(substr($m,4,2));
	$h3 = intval(substr($m,6,2));

	if (mb_strlen($wa)>0) 
	{
		$m = $h1 * 10000 + $h2 * 100 + $h3;
		$mc = 0;

		if ($wa == "M") 
		{
			if ($m >= 10908 && $m <= 450729) 
			{
				$mc = 1867;
			}
		}

		if ($wa == "T") 
		{
			if ($m >= 10730 && $m <= 151224) 
			{
				$mc = 1911;
			}
		}

		if ($wa == "S") 
		{
			if ($m >= 11225 && $m <= 640107) 
			{
				$mc = 1925;
			}
		}

		if ($wa == "H") 
		{
			if ($m >= 10108) 
			{
				$mc = 1988;
			}
		}

		if ($mc == 0) 
		{
			return $ch_DateSub;
		}
		$h1 = $h1 + $mc;
	}

	if ($mode == 0) 
	{
		if ($h1 == 0) 
		{
			$h1 = strftime("%Y",time());
		}

		if ($h2 == 0) 
		{
			$h2 = strftime("%m",time());
		}
	} else {
		$hFlag = 0;
		if ($h1 == 0) 
		{
			$h1 = strftime("%Y",time());
			$hFlag = 1;
		}
	}

	//日付で正しいか
	if (!checkdate($h2, $h3, $h1)) 
	{
		return $ch_DateSub;
	}

	if ($mode == 0) 
	{
		$ch_DateSub = $h1 * 10000 + $h2 * 100 + $h3;
	} else {
		if ($hFlag == 1) 
		{
			$ch_DateSub = $h2 * 100 + $h3;
		} else {
			$ch_DateSub = $h1 * 10000 + $h2 * 100 + $h3;
		}
	}
	return $ch_DateSub;
}
//////////////////////////////////////////
//<説明>
//  数値であれば数値を、そうでなければ０を返す
//<引数>
//  $str :文字列
//////////////////////////////////////////
function nz($str) 
{
	$nz = 0;

	if (!is_numeric($str)) 
	{
		return $nz;
	}

	$x = floatval($str);
	if ($x < -2147483648 || $x > 2147483647) 
	{
		return $nz;
	}
	$nz = intval($str);
	return $nz;
}
//////////////////////////////////////////
//<説明>
//  全部数字かを返す
//<引数>
//  $str :文字列
//////////////////////////////////////////
function is_Number_only($str) 
{

	$is_Number_only=false;

	if (is_Null($str) || strlen($str) < 1) 
	{
		return $is_Number_only;
	}

	for ($i = 1; $i <= strlen($str); $i = $i + 1) 
	{
		if (substr($str,$i-1,1) < "0" || substr($str,$i-1,1) > "9") 
		{
			return $is_Number_only;
		}
	}

	$is_Number_only=true;

	return $is_Number_only;
}
?>
