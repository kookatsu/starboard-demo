<?php

	session_start();

	include "../common/fjcall_comfunc.php";
	include "../common/MYDB.php";
	include "../common/fjcall_const.php";

	$this_pg='mtrunk.php';
	$modname = "外線マスタ";

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
	$push_ok   = $_POST["push_ok"];
	
	//ｴﾗｰﾒｯｾｰｼﾞの初期化
	$err_msg = "";

	if($mode == "dsp_after") {

		//OKボタン
		if ($_POST["comButton"] =="comok"){

			$bcd = $_POST["bcd"];
			$bcd += 0;
			$_POST["bcd"] = $bcd;

			if($bcd == 0 && $_POST["skubun"]=="add"){
	        	$err_msg = "トランク番号は１文字以上を入力して下さい。";
	      	}
			if($bcd > 200 && $_POST["skubun"]=="add"){
	        	$err_msg = "トランク番号は200までです。";
	      	}

			//ﾃﾞｰﾀﾍﾞｰｽを検索
			list($reccnt, $l_gaisenno, $l_gaisenname, $l_clientcode, $l_endusercode, $l_maintrunkno, $l_trunkmemo, $l_insertday, $l_lastupdateday, $l_modifyuserid) = SqlSearch($bcd);

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
	  		if ($err_msg == "") {
				$_POST["gaisenno"] = $l_gaisenno;
				$_POST["gaisenname"] = $l_gaisenname;
				$_POST["clientcode"] = $l_clientcode;
				$_POST["endusercode"] = $l_endusercode;
				$_POST["maintrunkno"] = $l_maintrunkno;
				$_POST["trunkmemo"] = $l_trunkmemo;
				$_POST["push_ok"]="1";
				if($l_lastupdateday == "" ){
					$lastupdateday = "";
				}else{
					$lastupdateday = substr($l_lastupdateday,0,4).'/'.substr($l_lastupdateday,4,2).'/'.substr($l_lastupdateday,6,2);
				}
				if($l_modifyuserid == "" ){
					$modifyuserid = "";
				}else{
					$modifyuserid = " 更新User:".$l_modifyuserid;
				}
			}
			else{
				$_POST["gaisenno"]="";
				$_POST["gaisenname"]="";
				$_POST["maintrunkno"]="0";
				$_POST["trunkmemo"]= "";
				$_POST["push_ok"]="0";
			}

		}
		//クライアントコンボの選択時
		elseif ($_POST["comButton"] == "comboselclient"){
			$_POST["gaisenname"]=stripslashes($_POST["gaisenname"]);

		}
		//登録ボタン
		elseif ($_POST["comButton"] == "comadd"){
			if ($_POST["push_ok"]=="1"){


				list($ek, $Name) = ch_in_String($_POST["bname"], 1, 40);
	      		if (!is_null($ek)){
	        		$err_msg="状況 : ".$ek;
	      		} 
				if ($err_msg==""){

					if ($_POST["skubun"]=="add"){
						//追加
						SqlAddnew($_POST["bcd"], $_POST["gaisenno"], $_POST["gaisenname"], $_POST["clientcode"], $_POST["endusercode"], $_POST["maintrunkno"], $_POST["trunkmemo"], $user);
						print "<script language=javascript>alert('登録しました！')</script>";
					}elseif ($_POST["skubun"]=="mod"){
						//更新
						SqlEdit($_POST["bcd"], $_POST["gaisenno"], $_POST["gaisenname"], $_POST["clientcode"], $_POST["endusercode"], $_POST["maintrunkno"], $_POST["trunkmemo"], $user);
						print "<script language=javascript>alert('更新しました！')</script>";
					}elseif ($_POST["skubun"]=="del"){
						//削除
						SqlDelete($_POST["bcd"]);
						print "<script language=javascript>alert('削除しました！')</script>";
					}else{
					}
					$_POST["push_ok"]="0";
				}

			}else{
	        	$err_msg = "コードが未入力です。";
			}

		}
		else{
			$_POST["gaisenno"]="";
			$_POST["gaisenname"]="";
			$_POST["maintrunkno"]="0";
			$_POST["trunkmemo"]= "";
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
// mtrunkからﾃﾞｰﾀを取得
//////////////////////////////////////////
function SqlSearch( $search_key ) {

	$wk_reccnt = 0;

	$conn = db_connect();

    $sql = <<<EOS
      SELECT * FROM mtrunk 
        WHERE
         trunkno = $search_key
EOS;
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
		$wk_reccnt = 1;
		$l_gaisenno = $rs['gaisenno'];
		$l_gaisenname = $rs['gaisenname'];
		$l_clientcode = $rs['clientcode'];
		$l_endusercode = $rs['endusercode'];
		$l_maintrunkno = $rs['maintrunkno'];
		$l_trunkmemo = $rs['trunkmemo'];
		$l_insertday = $rs['insertday'];
		$l_lastupdateday = $rs['lastupdateday'];
		$l_modifyuserid = $rs['modifyuserid'];
	}
	$result = null;
	$conn = null;

	return array($wk_reccnt, $l_gaisenno, $l_gaisenname, $l_clientcode, $l_endusercode, $l_maintrunkno, $l_trunkmemo, $l_insertday, $l_lastupdateday, $l_modifyuserid);

}

//////////////////////////////////////////
// mtrunkへﾃﾞｰﾀを挿入
//////////////////////////////////////////
function SqlAddnew( $b_code, $gaisenno,$gaisenname,$clientcode, $endusercode, $maintrunkno, $trunkmemo, $userid ) {

	//最終更新日用のｼｽﾃﾑ日付
	$NowYMD = date("Y").date("m").date("d");

	$b_code +=0;
	$maintrunkno +=0;

    $conn = db_connect();

    $sql = <<<EOS
	INSERT INTO mtrunk 
		(
        trunkno,
        gaisenno,
        gaisenname,
        clientcode,
        endusercode,
        maintrunkno,
        trunkmemo,
        insertday,
		lastupdateday,
		modifyuserid
		)VALUES(
        $b_code,
        '$gaisenno',
        '$gaisenname',
        '$clientcode',
        '$endusercode',
        $maintrunkno,
        '$trunkmemo',
         $NowYMD,
         $NowYMD,
		 '$userid'
		);

EOS;

	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();

	$result = null;
	$conn = null;
}

//////////////////////////////////////////
// mtrunkへﾃﾞｰﾀを更新
//////////////////////////////////////////
function SqlEdit( $b_code, $gaisenno,$gaisenname,$clientcode, $endusercode, $maintrunkno, $trunkmemo, $userid  ) {

	//最終更新日用のｼｽﾃﾑ日付
	$NowYMD = date("Y").date("m").date("d");

	$b_code +=0;
	$maintrunkno +=0;

	$conn = db_connect();

    $sql = <<<EOS
	UPDATE mtrunk 
	SET
        gaisenno = '$gaisenno',
        gaisenname = '$gaisenname',
        clientcode = '$clientcode',
        endusercode = '$endusercode',
        maintrunkno = $maintrunkno,
        trunkmemo = '$trunkmemo',
        lastupdateday = $NowYMD,
		modifyuserid = '$userid'

	WHERE 
        trunkno = $b_code

EOS;

	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();

	$result = null;
	$conn = null;
}
//////////////////////////////////////////
// mtrunkからﾃﾞｰﾀを削除
//////////////////////////////////////////
function SqlDelete( $b_code ) {

	$b_code +=0;

	$conn = db_connect();

    $sql = <<<EOS
	DELETE FROM mtrunk WHERE trunkno = $search_key

EOS;
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
<title><? print $modname?></title>
</head>


<?
//ﾌｫｰｶｽ制御
if($_POST["push_ok"]=="1"){
?>
  <body OnLoad="form01.gaisenno.focus();form01.gaisenno.select()">
<?
}else{
?>
  <body OnLoad="form01.bcd.focus();form01.bcd.select()">
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
				<td width="160" align="left"><img src=<?php print $GUIDE_MODNAME5 ?> border="0"></td>
				<td width="230" align="left"><img src=<? print $LOGO_LOGIN_S ?> border="0">
											<?php print "【 " ; print $name; print " さん】" ?>
											<img src=<? print "../img/face/" . $face ?> border="0">
				</td>
				<td align="right" width="630">
					<a href="#" onClick="MyGoTopMenu()"><img src=<?= $MOD_LINKPRE_01 ?> border="0" alt="アカウント設定">トップメニューに戻る</a>｜
					<a href="#" onClick="MyWondowLogout()">ログアウトする</a>
				</td>
			</tr>
		</table>
		<hr>
	</div>

  <table width="910"height="700"  border="0">
    <tr>
      <td align="center" valign="top">
          <table width="400" border="0" cellpadding="2" style="font-size:9pt;">
            <tr>
              <td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>処理区分</font></td>
              <td width="300">
                <input type="radio" name="skubun" value="add" <?=$check_add?> onClick="MyModClick('sk0')" >新規　
                <input type="radio" name="skubun" value="mod" <?=$check_mod?> onClick="MyModClick('sk1')" >修正　
                <input type="radio" name="skubun" value="del" <?=$check_del?> onClick="MyModClick('sk2')" >削除
              </td>
            </tr>

            <tr>
              <td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>トランク番号</font></td>
              <td width="300">
                <input name="bcd" type="text" maxlength="3" Value="<?=$_POST["bcd"]?>" onkeypress="return numOnly()" onFocus="MyCodeClick()" onkeydown=EnterToTab(event) style="width:30px;ime-mode:disabled;text-align:right">
                <input name="comok" type="button" value=" OK " onClick="MyComOK()">
              </td>
            </tr>

            <tr>
              <td align="center">&nbsp;</td>
              <td width="300"><font color="#ff3333\"><?= $err_msg ?></font></td>
            </tr>

            <tr>
              <td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>外線番号</font></td>
              <td width="300"><input name="gaisenno" type="text" maxlength="20" Value="<?=$_POST["gaisenno"]?>" onkeypress="return numOnly()" onkeydown=EnterToTab(event) style="width:130px;ime-mode:disabled"></td>
            </tr>
            <tr>
              <td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>外線名称</font></td>
              <td width="300"><input name="gaisenname" type="text" maxlength="50" Value="<?=$_POST["gaisenname"]?>" onkeypress="return numOnly()" onkeydown=EnterToTab(event) style="width:270px;ime-mode:active"></td>
            </tr>

            <tr>
              <td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>クライアント</font></td>
              <td width="300"><select name="clientcode" onkeydown=EnterToTab(event) onChange="MyClientClick()" ><?php SetClientCombo( $_POST["clientcode"] ,0); ?></select></td>
            </tr>

            <tr>
              <td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>エンドユーザ</font></td>
              <td width="300"><select name="endusercode" onkeydown=EnterToTab(event) ><?php SetEndUserCombo( $_POST["clientcode"], $_POST["endusercode"] ,1); ?></select></td>
            </tr>

            <tr>
              <td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>親トランク番号</font></td>
              <td width="300"><input name="maintrunkno" type="text" maxlength="3" Value="<?=$_POST["maintrunkno"]?>" onkeypress="return numOnly()" onFocus="MyCodeClick()" onkeydown=EnterToTab(event) style="width:30px;ime-mode:disabled;text-align:right">&nbsp;(ﾄｰｷ回線のみ入力)</td>
            </tr>

            <tr>
              <td width="100" align="center" bgcolor=<?print $FIELD_BGCOLOR;?>><font color=<? print $FIELD_FTCOLOR ?>>備考</font></td>
              <td width="300"><TEXTAREA name="trunkmemo" rows="5" wrap="hard"  onkeydown=EnterToTab(event) style="width:270px;ime-mode:active"><?=$_POST["trunkmemo"]?></TEXTAREA></td>
            </tr>


            <tr>
              <td align="center">&nbsp;</td>
              <td width="300"></td>
            </tr>

            <tr>
              <td width="100" align="center" bgcolor="#CCCCCC">最終更新日</td>
              <td width="300"> <?= $lastupdateday ?> <?= $modifyuserid ?></td>
            </tr>

          </table>

          <br>

          <table width="400" border="0" cellpadding="2">
            <tr align="left">
<?php
if($_POST["skubun"]=="add"){
	$btncap = "登録";
}elseif($_POST["skubun"]=="del"){
	$btncap = "削除";
}else{
	$btncap = "更新";
}
?>

<?php
if ($_POST["push_ok"] =="1"){
?>
              <td><input type="button" name="btn" value=<?= $btncap ?> onClick="MyComAdd()" style="width:150"></td>
<?php
}else{
?>
              <td><input type="button" name="btn" value=<?= $btncap ?>  style="width:150" disabled></td>
<?php
}
?>
              <td width="250" align="center">&nbsp;</td>

            </tr>
          </table>

      </td>

		<!-- 登録状況 -->
		<td width="510" valign="top"><img src=<? print $LOGO_DATATABLE ?> border="0" alt=<? print $modname?>>
			<iframe src="mtrunk_list.php" name="RowMasterList" width="505" height="650" frameborder="1"></iframe>
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


<SCRIPT Language="JavaScript" type="text/javascript" src="../common/fjcall_ComFunc.js"></script>
<SCRIPT Language="JavaScript" type="text/javascript" src="mtrunk.js">

<!--
//-->
</SCRIPT>

