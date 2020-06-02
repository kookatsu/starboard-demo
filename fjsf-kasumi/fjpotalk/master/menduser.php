<?php

	session_start();

	include "../common/fjcall_comfunc.php";
	include "../common/MYDB.php";
	include "../common/fjcall_const.php";

	$this_pg='menduser.php';
	$this_modname = "エンドユーザマスタ";

	$user = $_SESSION['userid_call'];
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];
	$face = $_SESSION["face_call"];
	$birth = $_SESSION["birth_call"];
	$maindeskcode = $_SESSION["maindeskcode_call"];

	//URL直は排除
	if($user == ""){
		$first="../fcprt.php";
		header("location: {$first}");
		exit;
	}

	//処理起動時のﾌﾗｸﾞ用
	$mode = $_POST["mode"];

	//OKﾎﾞﾀﾝのﾁｪｯｸ用
	$push_ok = $_POST["push_ok"];
	
	//ｴﾗｰﾒｯｾｰｼﾞの初期化
	$err_msg = "";

	if($mode == "dsp_after") {

		//OKボタン
		if ($_POST["comButton"] =="comok"){

			$ecd = $_POST["ecd"];
			//数値変換と桁数合わせ(0ｻﾌﾟﾚｽ)
			$ecd += 0;
			$ecd = "000".$ecd;
			$ecdlen=strlen($ecd);
	        $ecd = substr($ecd,$ecdlen-3,3);

			$_POST["ecd"] = $ecd;


			if($ecd == "000" && $_POST["skubun"]=="add"){
				$err_msg = "コードは１以上を入力して下さい。";
			}
			if( $err_msg ==""){
				//ﾃﾞｰﾀﾍﾞｰｽを検索
				list( $reccnt, $l_ename, $l_dspno ) = SqlSearch( $_POST["skubun"], $_POST["clientcode"], $ecd);

				//修正＆データなし
				if($reccnt == 0 && $_POST["skubun"]=="mod"){
					$err_msg = "登録されていません。";
				}
				//削除＆データなし
				if($reccnt == 0 && $_POST["skubun"]=="del"){
					$err_msg = "登録されていません。";
		      	}
				//登録＆データあり
				if($reccnt <> 0 && $_POST["skubun"]=="add"){
					$err_msg = "既に登録されています。";
				}
				if($err_msg == ""){
					$_POST["ename"] =$l_ename;
					$_POST["dspno"] =$l_dspno;
					$_POST["push_ok"]="1";
				}else{
					$_POST["push_ok"]="0";
				}
			}else{
				$_POST["ename"]="";
				$_POST["dspno"]="";
				$_POST["push_ok"]="0";
			}

		}
		//登録ボタン
		elseif ($_POST["comButton"] == "comadd"){
			if ($_POST["push_ok"]=="1"){
				list($ek, $Name) = ch_in_String($_POST["ename"], 1, 50);
				if (!is_null($ek)){
					$err_msg="エンドユーザ名 : ".$ek;
				} 
				if ($err_msg==""){
					if ($_POST["skubun"]=="add"){
						//追加
						SqlAddnew( $_POST["clientcode"], $_POST["ecd"], $_POST["ename"], $_POST["dspno"] );
						print "<script language=javascript>alert('登録しました！')</script>";
					}elseif ($_POST["skubun"]=="mod"){
						//更新
						SqlEdit( $_POST["clientcode"], $_POST["ecd"], $_POST["ename"], $_POST["dspno"] );
						print "<script language=javascript>alert('更新しました！')</script>";
					}elseif ($_POST["skubun"]=="del"){
						//削除
						SqlDelete( $_POST["clientcode"], $_POST["ecd"] );
						print "<script language=javascript>alert('削除しました！')</script>";
					}else{
					}
					$_POST["push_ok"]="0";
					$_POST["ename"]="";
					$_POST["dspno"]="";
				}

			}

		}
		else{
			$_POST["ename"]="";
			$_POST["dspno"]="";
			$_POST["push_ok"]="0";
		}

	}


	//ｵﾌﾟｼｮﾝﾎﾞﾀﾝの初期表示
	if ($_POST["skubun"]=="mod"){
		$check_mod = ("checked");
	}elseif ($_POST["skubun"]=="del"){
		$check_del = ("checked");
	}else{
		$check_add = ("checked");
	}


//////////////////////////////////////////
// menduserからﾃﾞｰﾀを取得
//////////////////////////////////////////
function SqlSearch( $smode, $clientcode, $search_key ) {

	$wk_reccnt = 0;

	$conn = db_connect();

	$sql = "SELECT * FROM menduser";
	$sql = $sql . " WHERE clientcode ='" . $clientcode . "'";
	$sql = $sql . " AND   endusercode ='" . $search_key . "'";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		$wk_reccnt = 1;
		$l_ename = $rs['endusername'];
		$l_dspno = $rs['dspno'];
	}
	$result = null;

	$conn = null;

	return array($wk_reccnt, $l_ename, $l_dspno);

}

//////////////////////////////////////////
// menduserへﾃﾞｰﾀを挿入
//////////////////////////////////////////
function SqlAddnew( $clientcode, $e_code, $e_name, $dspno ) {

global $user;

	//最終更新日用のｼｽﾃﾑ日付
	$NowYMD = date("Y").date("m").date("d");

	$dspno +=0;

	$conn = db_connect();

    $sql = <<<EOS
	INSERT INTO menduser 
		(
        clientcode,
        endusercode,
        endusername,
        dspno,
        insertday,
        lastupdateday,
        modifyuserid
		)VALUES(
        '$clientcode',
        '$e_code',
        '$e_name',
        $dspno,
        $NowYMD,
        $NowYMD,
        '$user'
		);
EOS;

	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();

	$result = null;
	$conn = null;

}

//////////////////////////////////////////
// menduserへﾃﾞｰﾀを更新
//////////////////////////////////////////
function SqlEdit( $clientcode, $e_code, $e_name, $dspno ) {

global $user;

	//最終更新日用のｼｽﾃﾑ日付
	$NowYMD = date("Y").date("m").date("d");

	$dspno +=0;

	$conn = db_connect();

    $sql = <<<EOS
	UPDATE menduser 
	SET
        endusername = '$e_name',
        dspno = $dspno,
        lastupdateday = $NowYMD,
        modifyuserid = '$user'

	WHERE 
        clientcode = '$clientcode'
	AND 
        endusercode = '$e_code'

EOS;
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();

	$result = null;
	$conn = null;
}
//////////////////////////////////////////
// menduserからﾃﾞｰﾀを削除
//////////////////////////////////////////
function SqlDelete( $clientcode, $e_code ) {

	$conn = db_connect();

	$sql = "DELETE FROM menduser";
	$sql = $sql . " WHERE clientcode = '" . $clientcode . "'";
	$sql = $sql . " AND   endusercode = '" . $e_code . "'";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();

	$result = null;
	$conn = null;
}

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">
<link rel="stylesheet" type="text/css" href="../common/fjcall_common.css">
<title><? print $this_modname?></title>
</head>


<?
//ﾌｫｰｶｽ制御
if($_POST["push_ok"]=="1"){
?>
  <body OnLoad="form01.ename.focus();form01.ename.select()">
<?
}else{
?>
  <body OnLoad="form01.ecd.focus();form01.ecd.select()">
<?
}
?>

<form name="form01" method="POST" action="<?= $this_pg ?>"  onsubmit="return false;">

  <input type="hidden" name="mode" value="dsp_after" >
  <input type="hidden" name="push_ok" Value="<?=$_POST["push_ok"]?>">
  <input type="hidden" name="comButton" value="">

<center>

	<!-- ﾛｸﾞｲﾝ情報エリア -->
	<div id="logininfo">
		<table border="0">
			<tr>
				<td width="80"  align="left"><img src=<?php print $GUIDE_MODNAME_TOP ?> border="0"></td>
				<td width="160" align="left"><img src=<?php print $GUIDE_MODNAME4 ?> border="0"></td>
				<td width="230" align="left"><img src=<? print $LOGO_LOGIN_S ?> border="0">
											<?php print "【 " ; print $name; print " さん】" ?>
											<img src=<? print "../img/face/" . $face ?> border="0">
				</td>
				<td align="right" width="630">
					<a href="#" onClick="MyGoTopMenu()"><img src=<?= $MOD_LINKPRE_01 ?> border="0" alt="トップメニュー">トップメニューに戻る</a>｜
					<a href="#" onClick="MyWondowLogout()">ログアウトする</a>
				</td>
			</tr>
		</table>
		<hr>
	</div>

	<table width="980"height="680"  border="0">
		<tr>
			<td align="center" valign="top">
				<table width="350" border="0" cellpadding="2" style="font-size:10pt;">
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>処理区分</font></td>
						<td width="250">
							<input type="radio" name="skubun" value="add" <?=$check_add?> onClick="MyModClick('sk0')" >新規　
							<input type="radio" name="skubun" value="mod" <?=$check_mod?> onClick="MyModClick('sk1')" >修正　
							<input type="radio" name="skubun" value="del" <?=$check_del?> onClick="MyModClick('sk2')" >削除
						</td>
					</tr>

					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>クライアント</font></td>
						<td width="250"><select name="clientcode" onkeydown=EnterToTab(event) onClick="MyCComboClick()" ><?php SetClientCombo( $_POST["clientcode"] ,0); ?></select></td>
						</td>
					</tr>
					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>エンドユーザ</font></td>
						<td width="250">
							<input name="ecd" type="text" maxlength="3" Value="<?=$_POST["ecd"]?>" onFocus="MyCComboClick()" onkeypress="return numOnly()" onkeydown=EnterToTab(event) style="width:30px;text-align:center;ime-mode:disabled">
							<input name="comok" type="button" value=" OK " onClick="MyComOK()">
						</td>
					</tr>

					<tr>
						<td align="center">&nbsp;</td>
						<td width="250"><font color="#ff3333\"><?= $err_msg ?></font></td>
					</tr>

					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>エンドユーザ名</font></td>
						<td width="250"><input name="ename" type="text" maxlength="16" Value="<?=$_POST["ename"]?>" onkeydown=EnterToTab(event) style="width:210px;ime-mode:active"></td>
					</tr>

					<tr>
						<td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>表示順</font></td>
						<td width="250"><input name="dspno" type="text" maxlength="3" Value="<?=$_POST["dspno"]?>" onkeydown=EnterToTab(event) style="width:30px;text-align:right;ime-mode:disabled"></td>
					</tr>



				</table>

				<br>

				<table width="350" border="0" cellpadding="2">
					<tr align="center">
<?php
if($_POST["skubun"]=="add"){
	$btncap = "登録";
}elseif($_POST["skubun"]=="del"){
	$btncap = "削除";
}else{
	$btncap = "更新";
}

if ($_POST["push_ok"] =="1"){
?>
						<td><input type="button" name="btn" value=<?= $btncap ?> onClick="MyComAdd()" style="width:150"></td>
<?php
}else{
?>
						<td><input type="button" name="btn" value=<?= $btncap ?> style="width:150" disabled></td>
<?php
}
?>
					</tr>
				</table>

			</td>

			<!-- 登録状況 -->
			<td width="625" valign="top"><img src=<? print $LOGO_DATATABLE ?> border="0" alt=<? print $this_modname?>>
				<br>
				<select name="gbumoncd" ><?php SetClientCombo( $_POST["gbumoncd"],2); ?></select>
				<input type="button" name="gbumonDSP" value="表示" onClick="MyIchiranDsp()">
				<iframe src="menduser_list.php?selgbumoncd=<? echo rawurlencode($_POST["gbumoncd"])?>" name="RowMasterList" width="625" height="650" frameborder="1"></iframe>
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



<script language="javascript" src="menduser.js"></script>
<script language="javascript" src="../common/fjcall_comfunc.js"></script>

