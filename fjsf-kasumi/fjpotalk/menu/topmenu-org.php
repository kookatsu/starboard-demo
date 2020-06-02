<?php
//2019-05-09 10分に１回OKボタンを自動でクリック
//2019-05-09 起動時の日付範囲を今日～今日へ変更
//2019-05-13 起動時の日付範囲　9時間足す


	//新着マーク日数
	$NEW_LIMIT =7;



	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";
	include "../common/fjcall_const.php";
	include "./topmenu_callcal.php";
	include "./topmenu_callcal_sf.php";

	$this_pg = 'topmenu.php';
	$modname = "デスク別集計";


	//Session変数取得
	$companycode = $_SESSION["companycode_call"];//会社コード
	$companyname = $_SESSION["companyname_call"];
	$selClient = $_SESSION["selclient_call"];
	$selDesk = $_SESSION["seldesk_call"];
	$selEndUser = $_SESSION["selenduser_call"];
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];
	$fujimotoflg = $_SESSION["fujimotoflg_call"];
	//Session変数取得


	//URL直は排除
	if( $user == "" ){
		$first="../" . $Const_LOGIN_PHP; //Login
		header("location: {$first}");
		exit;
	}

	//処理起動時のﾌﾗｸﾞ用
	$mode = $_POST["mode"];


	//OKﾎﾞﾀﾝのﾁｪｯｸ用
	$push_ok = $_POST["push_ok"];

	//グラフの初期表示は日別
	$graphFlg = "g31";





	//起動時の表示後
	if( $mode == "dsp_after" ) {

		if( $_POST["comButtonG"] =="g31"){//日別
			$graphFlg = "g31";
		}
		if( $_POST["comButtonG"] =="g32" ){//時間帯別
			$graphFlg = "g32";
		}
		if( $_POST["comButtonG"] =="g33" ){//月別
			$graphFlg = "g33";
		}


		$seldate1 =  $_POST["colname1"];
		$seldate2 =  $_POST["colname2"];
		$selymd1 = str_replace("/", "", $seldate1);//ｽﾗｯｼｭを外す
		$selymd2 = str_replace("/", "", $seldate2);//ｽﾗｯｼｭを外す

		$talksu = $_POST["talksu"];
		$guidance = $_POST["guidance"];
		$basestorecnt = $_POST["basestorecnt"];
		$notstorecnt = $_POST["notstorecnt"];
		$reportname = $_POST["reportname"];
		$reportnamelastupdate = $_POST["reportnamelastupdate"];
		$reportfilename = $_POST["reportfilename"];

		//データ抽出ボタン
		if ( $_POST["comButton"] =="ok" ){

			$_POST["push_ok"] = "1";

			//日付指定時
			$fromY = substr( $selymd1, 0, 4 );
			$fromM = sprintf("%02d", substr( $selymd1,4, 2 ));
			$fromD = sprintf("%02d", substr(  $selymd1,6, 2 ));
			$toY = substr( $selymd2,0, 4 );
			$toM = sprintf("%02d", substr( $selymd2,4, 2 ));
			$toD = sprintf("%02d", substr(  $selymd2,6, 2 ));
			//(グラフ用開始日)---31日前
			$ad = date("Y-m-d", strtotime( -31 . "day", strtotime($fromY ." -" . $fromM . "-" . $fromD)) );
			$fromY2 = substr( $ad, 0, 4 );
			$fromM2 = sprintf("%02d", substr( $ad,5, 2 ));
			$fromD2 = sprintf("%02d", substr(  $ad,8, 2 ));


			//日数チェック
//			$wday1 = $fromY . "-" . $fromM . "-" . $fromD;
//			$wday2 = $toY . "-" . $toM . "-" . $toD;
//			$date1 =  StrToUnixTime( $wday1 );
//			$date2 =  StrToUnixTime( $wday2 );
//			$wDiff = DateDiff( $date2, $date1 );
//Herokuでは StrToUnixTimeは機能しない
			$wday1 = strtotime($fromY . "-" . $fromM . "-" . $fromD);
			$wday2 = strtotime($toY . "-" . $toM . "-" . $toD);
			$seconddiff = abs($wday2 - $wday1);
			$wDiff = $seconddiff / (60 * 60 * 24);


			$wDiff = $wDiff + 1;
			if($wDiff>31){
				echo "<script type='text/javascript'>";
				echo "alert('日付の最大期間は31日までです！');";
				echo "</script>";
				$_POST["push_ok"] = "0";
			}

			//DBｵｰﾌﾟﾝ
			$conn = db_connect();

			//入力用(wdeskhistory1)にﾃﾞｰﾀ作成
			$recflg = PreMakeWkData( $conn, $selDesk, $selClient, $selEndUser );
			$recflg = PreMakeWkDataSF( $conn, $selDesk,  $selClient, $selEndUser);
			$recflg = PreMakeWkDataSF6Month($conn, $selDesk,  $selClient, $selEndUser);//過去6ヶ月
			//過去全ての未完了件数を取得
			$mikanryosu = Mikanryo( $conn );

			//DBｸﾛｰｽﾞ
			$conn = null;

		}
	}else{
		//起動時 ->自分のデスクを初期表示したい
		$_POST["push_ok"] = "1";

		//終了日(今日)
		$seldate2 = date("Y/m/d", strtotime("+9 hour")); //2019-05-13 システム日付が9時間時差がある
//		$seldate2 = date("Y/m/d", strtotime("-0 days")); //今日
		$_POST["colname2"] = $seldate2;
		$selymd2 = str_replace("/", "", $seldate2);//ｽﾗｯｼｭを外す
		$toY = substr( $selymd2,0, 4 );
		$toM = sprintf("%02d", substr( $selymd2,4, 2 ));
		$toD = sprintf("%02d", substr( $selymd2,6, 2 ));

		//開始日(期初日)
		$in_year = substr( $selymd2, 0, 4 );
		$in_month = substr( $selymd2, 4, 2 );
		$in_day = substr( $selymd2, 6, 2 );

//2019-05-09 MOD
//		list( $fromY, $fromM, $fromD ) = CalcDateStartDay($in_year, $in_month,$in_day , 0 );
		$fromY = $toY;
		$fromM = $toM;
		$fromD = $toD;

		$seldate1 = $fromY. "/" . $fromM. "/" . $fromD;
		$_POST["colname1"] = $seldate1;
		$selymd1 = $fromY. $fromM. $fromD;

		//(グラフ用開始日)---31日前
		$ad = date("Y-m-d", strtotime( -31 . "day", strtotime($fromY ." -" . $fromM . "-" . $fromD)) );
		$fromY2 = substr( $ad, 0, 4 );
		$fromM2 = sprintf("%02d", substr( $ad,5, 2 ));
		$fromD2 = sprintf("%02d", substr(  $ad,8, 2 ));


		//DBｵｰﾌﾟﾝ
		$conn = db_connect();

		//クライアントとデスクとエンドユーザ
		list(  $talksu, $guidance, $basestorecnt, $notstorecnt, $reportname, $reportnamelastupdate, $reportfilename ) = GetKaisenInfo_OPEN( $conn );

		//初期データ作成
		$recflg = PreMakeWkData( $conn, $selDesk,  $selClient, $selEndUser);
		$recflg = PreMakeWkDataSF( $conn, $selDesk,  $selClient, $selEndUser);
		//過去全ての未完了件数を取得
		$mikanryosu = Mikanryo( $conn );

		//DBｸﾛｰｽﾞ
		$conn = null;

	}


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?= $Const_HTML_CHARSET ?>">
<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">
<link rel="icon" href="../img/sb.ico" />
<link rel="shortcut icon" href="../img/sb.ico"  />
<link rel="stylesheet" type="text/css" href="../common/fjcall_common.css">
<title>FJコール受付集計 <? print $modname?></title>

<script type="text/javascript" language="javascript">
<!--
	//600秒(10分)に1回ﾘﾌﾚｯｼｭ
	setTimeout("MyComOK()",600000);

// -->
</script>

</head>

<BODY>
<center>


<form id="formid" name="form01" method="POST" action="<?= $this_pg ?>" onsubmit="return false;">

	<input type="hidden" name="mode" value="dsp_after" >
	<input type="hidden" name="comButton" value="">
	<input type="hidden" name="comButtonG" value="<?= $graphFlg ?>">
	<input type="hidden" name="selymd1" Value="<?=$fromY . $fromM . $fromD?>">
	<input type="hidden" name="selymd2" Value="<?=$toY . $toM . $toD?>">

	<input type="hidden" name="talksu" Value="<?=$talksu?>">
	<input type="hidden" name="guidance" Value="<?=$guidance?>">
	<input type="hidden" name="basestorecnt" Value="<?=$basestorecnt?>">
	<input type="hidden" name="notstorecnt" Value="<?=$notstorecnt?>">
	<input type="hidden" name="reportname" Value="<?=$reportname?>">
	<input type="hidden" name="reportnamelastupdate" Value="<?=$reportnamelastupdate?>">
	<input type="hidden" name="reportfilename" Value="<?=$reportfilename?>">



	<!-- ﾛｸﾞｲﾝ情報エリア -->
	<div id="logininfo">
		<table border="0" cellpadding="2" cellspacing="0" bgcolor="#999999" >
			<tr bgcolor="#FFFFFF">
				<td width="310" height="30" rowspan="2" align="left" style="font-size:14pt;"><?= $companyname ?> 様</td>
				<td width="230" height="30" align="left" style="font-size:14pt;"><img src=<?php print $GUIDE_MODNAME_TOP ?> border="0"></td>

				<td width="130" align="left" ><a href="javascript:void(0)" target="" Onclick="MyCodeClickh5();return false"><img src=<?= $MOD_LINKPRE_01 ?> border="0" alt="日次報告">&nbsp;日次報告</a></td>

				<td width="100" align="right" >対応店舗数</td>
				<td width="40"  align="right"><?=$basestorecnt?></td>
				<td width="30" align="lrft" >店舗</td>
				<td width="110" align="right" >通話回線数</td>
				<td width="20"  align="center" ><?=$talksu?></td>
				<td width="10" align="left" >&nbsp;</td>
				<td width="120" align="right" ><a href="#" onClick="MyWondowLogout()"><img src=<?= $MOD_LINKPRE_01 ?> border="0" alt="ログアウト">&nbsp;ログアウトする</a></td>
			</tr>

			<tr bgcolor="#FFFFFF">
				<td width="230" height="30" align="left"><img src=<? print $LOGO_LOGIN_S ?> border="0"><?php print "【 " ; print $name; print " さん】" ?></td>
<?
				//日付の差を求める
				//資料最終更新日
				$updateY = substr( $reportnamelastupdate, 0, 4 );
				$updateM = sprintf("%02d", substr( $reportnamelastupdate,4, 2 ));
				$updateD = sprintf("%02d", substr(  $reportnamelastupdate,6, 2 ));
				$wupdate1 = strtotime($updateY . "-" . $updateM . "-" . $updateD);
				//システム日付
				$nowtime = date("Y/m/d",strtotime("-0 day"));
				$wNow_Y = date("Y", strtotime($nowtime));
				$wNow_M = date("n", strtotime($nowtime));
				$wNow_D = date("j", strtotime($nowtime));;
				$wupdate2 = strtotime($wNow_Y."-".$wNow_M."-".$wNow_D);

				$seconddiff = abs($wupdate2 - $wupdate1);
				$wDiff = $seconddiff / (60 * 60 * 24);
?>
<?				if($wDiff <= $NEW_LIMIT){?>
					<td width="130" align="left" ><a href="javascript:void(0)" target="" Onclick="ManuOpen();return false"><img src=<?= $MOD_LINKPRE_01IVENT ?> border="0" alt="月次定例会資料">&nbsp;<?= $reportname ?></a></td>
<? }else{ ?>
					<td width="130" align="left" ><a href="javascript:void(0)" target="" Onclick="ManuOpen();return false"><img src=<?= $MOD_LINKPRE_01 ?> border="0" alt="月次定例会資料">&nbsp;<?= $reportname ?></a></td>
<? } ?>
				<td width="100" align="right" ><a href="javascript:void(0)" target="" Onclick="MyCodeClickh1(51);return false"><img src=<?= $MOD_LINKPRE_01 ?> border="0" alt="過去全て">&nbsp;未完了件数</a></td>
				<td width="40" align="right" ><?= $mikanryosu ?></td>
				<td width="30" align="left" >件</td>
				<td width="110" align="right" >ガイダンス回線数</td>
				<td width="20"  align="center" ><?=$guidance?></td>
				<td width="10" align="left" >&nbsp;</td>
				<td width="120" align="right" ><a href="javascript:void(0)" target="" Onclick="ManuOpen2();return false"><img src=<?= $MOD_LINKPRE_01 ?> border="0" alt="マニュアル">&nbsp;操作マニュアル</a></td>
			</tr>
		</table>
		<hr>
	</div>


	<!-- 1.条件選択欄 -->
	<div id="top_menu">
		<table  width="1100" border="0"><!-- 全体 -->

		<tr bgcolor="#FFFFFF">
			<td width="340" >
				<input type="text" name="colname1" Value="<? print $seldate1; ?>" maxlength="10" readonly="readonly" style="width:120px;font-size:16pt;text-align:center;">
				<a href="#" onclick="cal1.write(); return false;" onChange="cal1.getFormValue(); cal1.hide();" ><img src=<?= $SELECT_CAL ?>   border="0" alt="カレンダー選択" align="top"/></a> ～
				<input type="text" name="colname2" Value="<? print $seldate2; ?>" maxlength="10" readonly="readonly" style="width:120px;font-size:16pt;text-align:center;">
				<a href="#" onclick="cal2.write(); return false;" onChange="cal2.getFormValue(); cal2.hide();" ><img src=<?= $SELECT_CAL ?>   border="0" alt="カレンダー選択" align="top"/></a>
				<div id="calid"></div>
			</td>
			<td width="500"><input name="comok" type="button"  value="OK"  onClick="MyComOK()" style="height:25px;width:80px"></td>
			<td width="190"><font color="red" style="font-size:8pt;"><label id="wait_label"><?= $wait_mess ?> &nbsp;</label></font></td>
		</tr>


		</table><!-- 全体 -->
	</div>


<?
	//DBオープン
	$conn = db_connect();
?>


<?
	////////////////////
	// 共通条件作成
	////////////////////
	//抽出用日時
	//Salesforce上は、実際の時間の9時間前で記録されている為
	$wFromDate9 = $fromY . "/" . $fromM . "/" . $fromD . " 00:00:00";
	$wFromDate9 = date("Y-m-d H:i:s",strtotime($wFromDate9 . "-9 hour"));
	$wTo9 = $toY . "/" . $toM . "/" . $toD . " 00:00:00";
	$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "+1 day"));
	$wTo9 = date("Y-m-d H:i:s",strtotime($wTo9 . "-9 hour"));

	//クエリー用会社名 文字コード変換
	if( $ENV_MODE == 1){
		$Const_HQ_NAME_Cnv = mb_convert_encoding( $Const_HQ_NAME, $MOJI_ORG, $MOJI_NEW); //文字コード変換
	}else{
		$Const_HQ_NAME_Cnv = $Const_HQ_NAME;
	}

	$TotalSfCnt = 0;


	//↓最終着信日時(ssystem)
	$wlastday= 0; $wlasttime= "";
	$sql = "SELECT * From " . $Const_DB_SCHEMA . "ssystem";
	$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "ssystem.zerocode='0')";
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		$wlastday = $rs["lastimportday"];
		$wlasttime = $rs["lastimporttime"];
	}
	$result = null;

	$wdspwlastday = substr($wlastday, 0, 4) . "/" . substr($wlastday, 4, 2) . "/" . substr($wlastday, 6, 2);
	$wdsplasttime = substr( $wlasttime, 0, 2 ) . ":" . substr( $wlasttime, 2, 2 );
	//↑最終着信日時(ssystem)

?>


<!------------>
<!-- 1Block -->
<!------------>

	<!-- 外側のTable Start -->
	<table width="1100"  border="0" style="font-size:12pt;"><tr><td valign="top">
		<tr>
			<!-- ■インシデント報告 -->
			<td width="350" valign="top">
				<!-- 内側のTable Start -->
				<DIV style="width:350px; overflow:auto;">
				<FIELDSET><LEGEND>インシデント報告</LEGEND>
				<table border="0" cellpadding="2" cellspacing="1"  style="font-size:11pt;">
<?php
					//合計インシデント件数
					$TotalSfCnt =0;
					$sql = "SELECT " . "Count(" . $Const_DB_SCHEMA . "case.casenumber) AS casenumber_cnt FROM " . $Const_DB_SCHEMA . "case";
					$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
					$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
					$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
					if($ENV_MODE == 1){
						$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
					}
					$result = $conn->prepare($sql);
					$result->execute();
					while ($rs = $result->fetch(PDO::FETCH_ASSOC))
					{
						$TotalSfCnt = $rs["casenumber_cnt"];
					}
					$result = null;
?>

					<tr>
						<td width="190"  align="left" ><a href="javascript:void(0)" target="" Onclick="MyCodeClickh1(1);return false">総件数</a></td>
						<td width="65"  align="right" ><?=number_format( $TotalSfCnt )?></td>
						<td width="15"  align="right" >件</td>
						<td width="65"  align="right" >&nbsp;</td>
						<td width="15"  align="right" >&nbsp;</td>
					</tr>
<?
					//完了理由別件数
					$sql = "SELECT " . $Const_DB_SCHEMA . "case.closereson__c, Count(" . $Const_DB_SCHEMA . "case.casenumber) AS casenumber_cnt FROM " . $Const_DB_SCHEMA . "case";
					$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
					$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
					$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
					$sql = $sql . " GROUP BY " . $Const_DB_SCHEMA . "case.closereson__c";
					$sql = $sql . " ORDER BY Count(" . $Const_DB_SCHEMA  . "case.casenumber) DESC";
					if($ENV_MODE == 1){
						$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
					}
					$result = $conn->prepare($sql);
					$result->execute();
					while ($rs = $result->fetch(PDO::FETCH_ASSOC))
					{

						//比率の計算
						if( $TotalSfCnt != 0 ){
							$wtalksuP = ($rs["casenumber_cnt"] / $TotalSfCnt) *100;
						}else{
							$wtalksuP = 0;
						}


						if($ENV_MODE == 1){
							$l_ename = mb_convert_encoding( $rs['closereson__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換
						}else{
							$l_ename = $rs['closereson__c'];
						}

						$winflg = SfCloseresonNameToNo( $l_ename );

						if($l_ename == ""){
							$l_ename="(対応中)";
						}
?>

						<tr>
							<td width="190"  align="left" >&nbsp;<a href="javascript:void(0)" target="" Onclick="MyCodeClickh1(<?= $winflg ?>);return false"><?=$l_ename?></a></td>
							<td width="65"  align="right" ><?=number_format($rs["casenumber_cnt"])?></td>
							<td width="15"  align="right" >件</td>
							<td width="65"  align="right" ><?=number_format($wtalksuP,1)?></td>
							<td width="15"  align="right" >%</td>
						</tr>
<?
					}
					$result = null;
?>
				</table>
				</DIV>
				<!-- 内側のTable End -->



			</td>


			<!-- 2.Filler Start-->
<!--			<td width="10" >&nbsp;</td>-->
			<!-- 2.Filler End-->


			<!-- ■グラフ -->
			<td width="740" valign="top">
				<!-- 内側のTable Start -->
				<DIV style="width:740px; overflow:auto;">
<?if($graphFlg == "g32"){?>
				<FIELDSET><LEGEND>時間帯別インシデント件数</LEGEND>
<? }else{ ?>
				<FIELDSET><LEGEND>日別インシデント件数</LEGEND>
<? } ?>

				<table width="700" border="0" style="font-size:11pt;">
					<tr>
						<td width="50">&nbsp;</td>
						<td width="100"><a href="javascript:void(0)" target="" Onclick="MyCodeClickh31();return false">日別</td></td>
						<td width="100"><a href="javascript:void(0)" target="" Onclick="MyCodeClickh32();return false">時間帯別</td></td>
						<td width="100"><a href="javascript:void(0)" target="" Onclick="MyCodeClickh33();return false">月別</td></td>
						<td width="390">&nbsp;</td>
					</tr>

					<tr>
<?if($graphFlg == "g32"){?>
						<td colspan="4" align="left" ><iframe id="chart32" src="topmenu_chart32G.php" name="RowMasterList32" width="700" height="250" scrolling="no" frameborder="0" ></iframe></td>
<? }elseif($graphFlg == "g33"){ ?>
						<td colspan="4" align="left" ><iframe id="chart33" src="topmenu_chart33G.php" name="RowMasterList33" width="700" height="250" scrolling="no" frameborder="0" ></iframe></td>
<? }else{ ?>
						<td colspan="4" align="left" ><iframe id="chart31" src="topmenu_chart31G.php" name="RowMasterList31" width="700" height="250" scrolling="no" frameborder="0" ></iframe></td>
<? } ?>

					</tr>

				</table>
				</DIV>
				<!-- 内側のTable End -->
			</td>



		</tr>
	<!-- 外側のTable End   -->
	</td></tr></table>









<!------------>
<!-- 2Block -->
<!------------>

	<!-- 外側のTable Start -->
	<table width="1100"  border="0" style="font-size:12pt;"><tr><td valign="top">
		<tr>
			<!-- ■回線状況 -->
			<td width="350" valign="top">
				<!-- 内側のTable Start -->
				<DIV style="width:350px; overflow:auto;">
				<FIELDSET><LEGEND>回線状況  <span class="pt8">(更新日付:<?= $wdspwlastday . " " . $wdsplasttime ?>)</span></LEGEND>
				<table border="0" cellpadding="2" cellspacing="1"  style="font-size:11pt;">

<?php
					//合計だけ
					$sql = "SELECT * FROM  " . $Const_DB_SCHEMA . "wdeskhistory1";
					$sql = $sql . " WHERE userid='" . $Const_COMPANYCODE . $user . "'";
					$sql = $sql . " AND   endusercode='" . $selEndUser . "'";
					$result = $conn->prepare($sql);
					$result->execute();
					while ($rs = $result->fetch(PDO::FETCH_ASSOC))
					{
						//総着信数の計算
						$wTotalDaySu = $rs["talksu"] +  $rs["guidancesu"] + $rs["guidance12su"] + $rs["fusyutsusu"];
						//比率の計算
						if( $wTotalDaySu != 0 ){
							$wtalksuP = ($rs["talksu"] / $wTotalDaySu)*100;
							$wguidancesuP = ($rs["guidancesu"] / $wTotalDaySu)*100;
							$wguidance12suP = ($rs["guidance12su"] / $wTotalDaySu)*100;
							$wfusyutsusuP = ($rs["fusyutsusu"] / $wTotalDaySu)*100;
						}else{
							$wtalksuP = 0;
							$wguidancesuP = 0;
							$wguidance12suP = 0;
							$wfusyutsusuP = 0;
						}
?>
						<tr>
							<td width="190"  align="left" ><a href="javascript:void(0)" target="" Onclick="MyCodeClickh2();return false">総着信数</td>
							<td width="65"  align="right" ><?=number_format($wTotalDaySu)?></td>
							<td width="15"  align="right" >件</td>
							<td width="65"  align="right" >&nbsp;</td>
							<td width="15"  align="right" >&nbsp;</td>
						</tr>
						<tr>
							<td width="190"  align="left" >&nbsp;通話数</td>
							<td width="65"  align="right" ><?=number_format($rs["talksu"])?></td>
							<td width="15"  align="right" >件</td>
							<td width="65"  align="right" ><?=number_format($wtalksuP,1)?></td>
							<td width="15"  align="right" >%</td>
						</tr>
						<tr>
							<td width="190"  align="left" >&nbsp;ガイダンス数</td>
							<td width="65"  align="right" ><?=number_format($rs["guidancesu"])?></td>
							<td width="15"  align="right" >件</td>
							<td width="65"  align="right" ><?=number_format($wguidancesuP,1)?></td>
							<td width="15"  align="right" >%</td>
						</tr>
						<tr>
							<td width="190"  align="left" >&nbsp;12秒ガイダンス数</td>
							<td width="65"  align="right" ><?=number_format($rs["guidance12su"])?></td>
							<td width="15"  align="right" >件</td>
							<td width="65"  align="right" ><?=number_format($wguidance12suP,1)?></td>
							<td width="15"  align="right" >%</td>
						</tr>
						<tr>
							<td width="190"  align="left" >&nbsp;不出呼数</td>
							<td width="65"  align="right" ><?=number_format($rs["fusyutsusu"])?></td>
							<td width="15"  align="right" >件</td>
							<td width="65"  align="right" ><?=number_format($wfusyutsusuP,1)?></td>
							<td width="15"  align="right" >%</td>
						</tr>
<?
					}
					$result = null;
?>
				</table>
				</DIV>
				<!-- 内側のTable End -->
			</td>


			<!-- 2.Filler Start-->
			<td width="25" >&nbsp;</td>
			<!-- 2.Filler End-->


			<!-- ■店舗別対応件数 -->
			<td width="330" valign="top">
				<!-- 内側のTable Start -->
				<DIV style="width:330px; overflow:auto;">
				<FIELDSET><LEGEND>店舗別インシデント件数</LEGEND>
				<table border="0"  style="font-size:11pt;">

<?php
					//店舗別件数
					$storeav = $TotalSfCnt / $basestorecnt;
?>
					<tr>
						<td width="170"  align="left" ><a href="javascript:void(0)" target="" Onclick="MyCodeClickh3();return false">平均問合せ数</td>
						<td width="45"  align="right" ><?=number_format($storeav,1)?></td>
						<td width="35"  align="right" >件</td>
						<td width="65"  align="right" >&nbsp;</td>
						<td width="15"  align="right" >&nbsp;</td>
					</tr>
<?
					$storeavUp = 0; $storeavDown=0; $storeZero = 0;
					$sql = "SELECT " . $Const_DB_SCHEMA . "case.shopname__c, Count(" . $Const_DB_SCHEMA . "case.casenumber) AS casenumber_cnt FROM " . $Const_DB_SCHEMA . "case";
					$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
					$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
					$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
					$sql = $sql . " GROUP BY " . $Const_DB_SCHEMA . "case.shopname__c";
					$sql = $sql . " ORDER BY Count(" . $Const_DB_SCHEMA  . "case.casenumber) DESC";
					if($ENV_MODE == 1){
						$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
					}
					$result = $conn->prepare($sql);
					$result->execute();
					$result->execute();
					while ($rs = $result->fetch(PDO::FETCH_ASSOC))
					{
						if($rs["casenumber_cnt"] >=$storeav){
							$storeavUp = $storeavUp +1;
						}else{
							$storeavDown = $storeavDown +1;
						}
					}
					$result = null;

					$storeZero = $basestorecnt - $storeavUp - $storeavDown;

					if($basestorecnt !=0){
						$storeavUpR = ($storeavUp / $basestorecnt) * 100;
						$storeavDownR = ($storeavDown / $basestorecnt) * 100;
						$storeZeroR = ($storeZero / $basestorecnt) * 100;
					}
?>
					<tr>
						<td width="170"  align="left" >&nbsp;平均値以上</td>
						<td width="45"  align="right" ><?=number_format($storeavUp)?></td>
						<td width="35"  align="right" >店舗</td>
						<td width="65"  align="right" ><?= number_format($storeavUpR,1)?></td>
						<td width="15"  align="right" >%</td>
					</tr>
					<tr>
						<td width="170"  align="left" >&nbsp;平均値以下</td>
						<td width="45"  align="right" ><?=number_format($storeavDown)?></td>
						<td width="35"  align="right" >店舗</td>
						<td width="65"  align="right" ><?= number_format($storeavDownR,1)?></td>
						<td width="15"  align="right" >%</td>
					</tr>
					<tr>
						<td width="170"  align="left" >&nbsp;０件</td>
						<td width="45"  align="right" ><?=number_format($storeZero)?></td>
						<td width="35"  align="right" >店舗</td>
						<td width="65"  align="right" ><?= number_format($storeZeroR,1)?></td>
						<td width="15"  align="right" >%</td>
					</tr>


				</table>
				</DIV>
				<!-- 内側のTable End -->
			</td>


			<!-- 2.Filler Start-->
			<td width="25" >&nbsp;</td>
			<!-- 2.Filler End-->



			<!-- ■機種別対応件数 -->
			<td width="370" valign="top">
				<!-- 内側のTable Start -->
				<DIV style="width:370px; height:250px; overflow:auto;"><!--高さを絞る-->
				<FIELDSET><LEGEND>機種別インシデント件数</LEGEND>
				<table border="0"  style="font-size:11pt;">

<?
					$sql = "SELECT " . $Const_DB_SCHEMA . "case.inquirycategory2__c, Count(" . $Const_DB_SCHEMA . "case.casenumber) AS casenumber_cnt FROM " . $Const_DB_SCHEMA . "case";
					$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.receiotdatetime__c>='" . $wFromDate9 . "'";
					$sql = $sql . " AND    " . $Const_DB_SCHEMA . "case.receiotdatetime__c<='" . $wTo9 . "')";
					$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "case.hq_name__c='" . $Const_HQ_NAME . "')";//MYCONST
					$sql = $sql . " GROUP BY " . $Const_DB_SCHEMA . "case.inquirycategory2__c"; //機種名
					$sql = $sql . " ORDER BY Count(" . $Const_DB_SCHEMA  . "case.casenumber) DESC";
					if($ENV_MODE == 1){
						$sql = mb_convert_encoding( $sql, $MOJI_ORG, $MOJI_NEW ); //ここは日本語が混じっているのでUTF-8へ
					}
					$result = $conn->prepare($sql);
					$result->execute();
					$result->execute();
					while ($rs = $result->fetch(PDO::FETCH_ASSOC))
					{

						if($ENV_MODE == 1){
							$dspkisyu = mb_convert_encoding( $rs['inquirycategory2__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換
						}else{
							$dspkisyu = $rs['inquirycategory2__c'];
						}
						if($TotalSfCnt !=0){
							$kisyuR = ($rs["casenumber_cnt"] / $TotalSfCnt) *100;
						}else{
							$kisyuR = 0;
						}
?>
						<tr>
							<td width="230" align="left" >&nbsp;<a href="javascript:void(0)" target="" Onclick="MyCodeClickh4('<?= $dspkisyu ?>');return false"><?=$dspkisyu?></a></td>
							<td width="45"  align="right" ><?=number_format($rs["casenumber_cnt"])?></td>
							<td width="15"  align="right" >件</td>
							<td width="65"  align="right" ><?= number_format($kisyuR,1)?></td>
							<td width="15"  align="right" >%</td>
						</tr>

<?

					}
					$result = null;
?>

				</table>
				</DIV>
				<!-- 内側のTable End -->
			</td>




		</tr>
	<!-- 外側のTable End   -->
	</td></tr></table>


<?
	$conn = null;
?>



	<!-- フッダーエリア -->
	<div id="bottom_menu">
		<br>
		<hr>
		<table width="1100" border="0">
			<tr>
				<td width="600" align="left">&nbsp;</td>
				<td width="500" align="right" id="copyright" name="copyright">	Copyright &copy FUJIMOTO CORP. All Rights Reserved.</td>
			</tr>
		</table>
	</div>


</form>
</center>
</body>
</html>



<SCRIPT type="text/javascript" src="../common/jkl-calendar.js" charset="Shift_JIS"></SCRIPT>

<script language="javascript" src="../common/fjcall_ComFunc.js"></script>
<SCRIPT Language="JavaScript">
<!--
var cal1 = new JKL.Calendar("calid","formid","colname1"); //From
var cal2 = new JKL.Calendar("calid","formid","colname2"); //To

/////////////////////////////////////////
// OKボタン
/////////////////////////////////////////
function MyComOK(){


	var labelObj = document.getElementById("wait_label");
	labelObj.innerHTML = "しばらくお待ち下さい。。。";

	form01.comButton.value="ok";
	form01.submit();


}
/////////////////////////////////////////
// 日次報告
/////////////////////////////////////////
function MyCodeClickh5()
{

	//日付範囲
	selymd1=form01.selymd1.value;
	selymd2=form01.selymd2.value;

	window.open('topmenu_sfdailyreport.php?selymd1=' + unescape(selymd1) + '&selymd2=' + unescape(selymd2), '_blank', 'width=1100,height=850,titlebar=no,toolbar=no,scrollbars=yes, top=0,left=0');

}

/////////////////////////////////////////
// 月次定例資料を表示する
/////////////////////////////////////////
function ManuOpen(){

reportfilename = '../manu/' + form01.reportfilename.value ;

	window.open(reportfilename ,'_blank', 'width=1000,height=700,titlebar=no,toolbar=no,scrollbars=yes');

}
/////////////////////////////////////////
// 操作マニュアルを表示する
/////////////////////////////////////////
function ManuOpen2(){

	window.open('../manu/manual_foruser.html', '_blank', 'width=1000,height=770,titlebar=no,toolbar=no,scrollbars=yes');


}
/////////////////////////////////////////
// インシデント総件数クリック
/////////////////////////////////////////
function MyCodeClickh1( wmode )
{
	//日付範囲
	selymd1=form01.selymd1.value;
	selymd2=form01.selymd2.value;

	window.open('topmenu_sfmeisai.php?selymd1=' + unescape(selymd1) + '&selymd2=' + unescape(selymd2)  + '&selmode=' + unescape(wmode), '_blank', 'width=1100,height=850,titlebar=no,toolbar=no,scrollbars=yes, top=' + (screen.availHeight-500)/2 + ',left=' + (screen.availWidth-500)/2);

}
/////////////////////////////////////////
// 総着信数クリック
/////////////////////////////////////////
function MyCodeClickh2(  )
{

	//日付範囲
	selymd1=form01.selymd1.value;
	selymd2=form01.selymd2.value;

	window.open('topmenu_callmeisai.php?selymd1=' + unescape(selymd1) + '&selymd2=' + unescape(selymd2) , '_blank', 'width=570,height=850,titlebar=no,toolbar=no,scrollbars=yes, top=' + (screen.availHeight-500)/2 + ',left=' + (screen.availWidth-500)/2);

}
/////////////////////////////////////////
// 店舗別平均問合せ件数クリック
/////////////////////////////////////////
function MyCodeClickh3( )
{

	//日付範囲
	selymd1=form01.selymd1.value;
	selymd2=form01.selymd2.value;

	window.open('topmenu_sfstore.php?selymd1=' + unescape(selymd1) + '&selymd2=' + unescape(selymd2) , '_blank', 'width=550,height=850,titlebar=no,toolbar=no,scrollbars=yes, top=' + (screen.availHeight-500)/2 + ',left=' + (screen.availWidth-500)/2);

}
/////////////////////////////////////////
// 機種名クリック
/////////////////////////////////////////
function MyCodeClickh4( wmode )
{
	//日付範囲
	selymd1=form01.selymd1.value;
	selymd2=form01.selymd2.value;

	window.open('topmenu_sfkisyumeisai.php?selymd1=' + unescape(selymd1) + '&selymd2=' + unescape(selymd2)  + '&selmode=' + unescape(wmode), '_blank', 'width=1100,height=850,titlebar=no,toolbar=no,scrollbars=yes, top=' + (screen.availHeight-500)/2 + ',left=' + (screen.availWidth-500)/2);

}

/////////////////////////////////////////
// グラフクリック
/////////////////////////////////////////
function MyCodeClickh31( )
{
	form01.comButton.value="ok";
    form01.comButtonG.value="g31";
    form01.submit();
}
/////////////////////////////////////////
// グラフクリック
/////////////////////////////////////////
function MyCodeClickh32( )
{

	form01.comButton.value="ok";
    form01.comButtonG.value="g32";
    form01.submit();
}
/////////////////////////////////////////
// グラフクリック
/////////////////////////////////////////
function MyCodeClickh33( )
{

	form01.comButton.value="ok";
    form01.comButtonG.value="g33";
    form01.submit();
}

//-->
</SCRIPT>

