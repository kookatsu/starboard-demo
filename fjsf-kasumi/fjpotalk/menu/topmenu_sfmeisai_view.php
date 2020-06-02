<?php
/////////////////////////////////////////////
// インシデント詳細内容の表示
/////////////////////////////////////////////

	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";
	include "../common/fjcall_const.php";

	$this_pg='topmenu_sfmeisai_view.php';
	$modname = "インシデント内容確認";

	$companycode = $_SESSION["companycode_call"];//会社コード
	$companyname = $_SESSION["companyname_call"];
	$selClient = $_SESSION["selclient_call"];
	$selDesk = $_SESSION["seldesk_call"];
	$selEndUser = $_SESSION["selenduser_call"];
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];

	//URL直は排除
	if( $user == "" ){
		$first="../" . $Const_LOGIN_PHP;
		header("location: {$first}");
		exit;
	}
	
	//処理起動時のﾌﾗｸﾞ用
	$mode = $_POST["mode"];

	//ｴﾗｰﾒｯｾｰｼﾞの初期化
	$err_msg = "";

	//親からのインシデント番号
	$SelInNo = $_GET["datano"];

	//ﾃﾞｰﾀﾍﾞｰｽを検索
	$conn = db_connect();

	$sql = "SELECT * From " . $Const_DB_SCHEMA . "case";
	$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "case.casenumber='" . $SelInNo . "')";
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		$receiotdatetime__c = date("Y-m-d H:i:s",strtotime($rs["receiotdatetime__c"] . "+9 hour")); //ここで9時間足す
		if($rs["closeddate"] > 0){
			$closeddate = date("Y-m-d H:i:s",strtotime($rs["closeddate"] . "+9 hour")); //ここで9時間足す
		}else{
			$closeddate = "";
		}
		if($ENV_MODE == 1){
			$storename = mb_convert_encoding( $rs['shopname__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$staffname = mb_convert_encoding( $rs['customerstaff__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$naiyou = mb_convert_encoding( $rs['inquirycategory3__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$description = mb_convert_encoding( $rs['description'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$kisyu = mb_convert_encoding( $rs['inquirycategory2__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$machine = mb_convert_encoding( $rs['machinenumber__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$status = mb_convert_encoding( $rs['status'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$taiou =mb_convert_encoding( $rs['closereson__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
			$operator =mb_convert_encoding( $rs['laststaff__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
		}else{
			$storename =  $rs['shopname__c'];
			$staffname =  $rs['customerstaff__c'];
			$naiyou =  $rs['inquirycategory3__c'];
			$description =  $rs['description'];
			$kisyu =  $rs['inquirycategory2__c'];
			$machine = $rs['machinenumber__c'];
			$status = $rs['status'];
			$taiou = $rs['closereson__c'];
			$operator = $rs['laststaff__c'];
		}

		$toiawase ="";
		if($ENV_MODE == 1){
			$report =mb_convert_encoding( $rs['lastreport__c'], $MOJI_NEW,$MOJI_ORG); //文字コード変換;
		}else{
			$report =$rs['lastreport__c'];
		}


	}
	$result= null;
	$conn = null;





?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?= $Const_HTML_CHARSET ?>>
<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">
<link rel="stylesheet" type="text/css" href="../common/fjcall_common.css">
<title><? print $modname?></title>
</head>
<body bgcolor="<?= $SUBWINDOW_BGCOLOR ?>">
<form id="formid" name="form01" method="POST" action="<?= $this_pg ?>"  onsubmit="return false;">


<center>

	<!-- ﾛｸﾞｲﾝ情報エリア -->
	<table width="1000" border="0" style="font-size:9pt;">
		<tr>
			<td width="260" align="left"><?= $companyname ?> 様</td>
			<td width="840" align="left">&nbsp;</td>
			<td align="right" width="300"><a href="#" onClick="MyWinClose()">閉じる</a></td>
		</tr>
	</table>

	<hr width="1000">


	<table width="1000" border="0">
		<tr>
			<td  width="500"  align="left" valign="top">
				<FIELDSET><LEGEND><font color=<? print $FRAME_FTCOLOR ?>>お問合せ情報</font></LEGEND>
				<table width="500" border="0" style="font-size:9pt;">
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>インシデントNo</font></td>
						<td width="400"><?=$SelInNo?></td>
					</tr>
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>受付日時</font></td>
						<td width="400"><?=$receiotdatetime__c?></td>
					</tr>
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>店舗名</font></td>
						<td width="400"><?=$storename?></td>
					</tr>
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>ご担当者様</font></td>
						<td width="400"><?=$staffname?></td>
					</tr>
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>内容</font></td>
						<td width="400"><?=$naiyou?></td>
					</tr>
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>機種名</font></td>
						<td width="400"><?=$kisyu?></td>
					</tr>
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>機器番号</font></td>
						<td width="400"><?=$machine?></td>
					</tr>
					<!-- Filler -->
					<tr>
						<td width="100">&nbsp;</td>
						<td width="400">&nbsp;</td>
					</tr>
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>ステータス</font></td>
						<td width="400"><?=$status?></td>
					</tr>
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>完了理由</font></td>
						<td width="400"><?=$taiou?></td>
					</tr>
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>完了日時</font></td>
						<td width="400"><?=$closeddate?> </td>
					</tr>
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>オペレータ</font></td>
						<td width="400"><?=$operator?></td>
					</tr>


					</tr>
				</table>
				</FIELDSET>
			</td>


			<!-- 右-->
			<td  width="500"  align="left" valign="top">
				<FIELDSET><LEGEND><font color=<? print $FRAME_FTCOLOR ?>>詳細情報</font></LEGEND>
				<table width="500" border="0" style="font-size:9pt;">

					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>お問い合わせ内容</font></td>
						<td width="400"><TEXTAREA name="gensyonaiyou" rows="15" wrap="hard" style="width:370px;ime-mode:active" readonly><?=$description?></TEXTAREA></td>
					</tr>
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>対応内容</font></td>
						<td width="400"><TEXTAREA name="taiounaiyou" rows="15" wrap="hard" style="width:370px;ime-mode:active" readonly><?=$report?></TEXTAREA></td>
					</tr>
					<tr>
						<td width="100" align="center">&nbsp;</td>
						<td width="400" align="left" >&nbsp;</td>
					</tr>

				</table>
				</FIELDSET>
			</td>

		</tr>
	</table>


	<!-- フッダーエリア -->
	<div id="bottom_menu">
		<br>
		<hr>
		<?php ShowFooter(); ?>
	</div>

</center>

</form>
</body>
</html>




<script language="javascript" src="../common/fjcall_ComFunc.js"></script>
