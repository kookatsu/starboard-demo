<?php
////////////////////////////////////////////////////////////////
// 2018-12-20 FTP�o�R�ɕύX
//            �N������FTP�T�[�o����_�E�����[�h
////////////////////////////////////////////////////////////////

	define( GUI12_1 , "839" );
	define( GUI12_2 , "839*2" );
	define( IMP_FILENAME , "dumplist.TXT" );

	session_start();


	include "../common/fjcall_comfunc.php";
	include "../common/MYDB.php";
	include "../common/fjcall_const.php";

	$this_pg='dataimp.php';
	$modname = "�_���v���X�g�捞";

	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];
	$face = $_SESSION["face_call"];
	$birth = $_SESSION["birth_call"];
	$maindeskcode = $_SESSION["maindeskcode_call"];

	//URL���͔r��
	if($user == ""){
		$first="../fcprt.php";
		header("location: {$first}");
		exit;
	}

	// ���J�����g�����߽+�utmp�v+̧�ٖ�
	$dump_full_path = "../../dataimp/" . IMP_FILENAME;  //FJ-QSC ���p�t�H���_

	//�捞�{�^��
	if ($_POST["comButton"] == "comadd"){
		if ( file_exists( $dump_full_path ) ) {

			//÷�ā����
			$l_data = GetTxtRead( $dump_full_path );

			//��؁��ް��ް�
			PreMakeWkData( $l_data, IMP_FILENAME );

			//۰�ُ��̧�ق͕s�v�ׁ̈A�폜����
			unlink( $dump_full_path );

			print "<script language=javascript>alert('�捞�������܂����I')</script>";

		}
	}else{
		//FTP�����޳�۰�ނ���
		$ftp_err = FTPdownload( $dump_full_path, IMP_FILENAME );
	}


	//�捞�Ώۂ̃t�@�C�������݂��邩�̃`�F�b�N
	$wExitFlg = 0;
	if (file_exists($dump_full_path)) {
		//�^�C���X�^���v�擾
		$updateDate = filemtime($dump_full_path);
		//�������`
		$updateDate = date('Y-m-d H:i:s', $updateDate);
		$exit_mess  ="�捞�Ώۂ̃_���v���X�g�����݂��܂��B"  . "[" . $updateDate . "]";
		$wExitFlg = 1;
	} else {
		$exit_mess  ="�捞�Ώۂ̃_���v���X�g�����݂��܂���B�B�B";
		$wExitFlg = 0;
	}

//////////////////////////////////////////
// FTP�����޳�۰�ނ���
// $file�F��M�����ۂ̃t���p�X�ł�̧�ٖ�
// $filename�F��M�t�@�C����
//////////////////////////////////////////
function FTPdownload( $file, $filename )
{

	$FTPdownload = true;

	//DB�����
	$conn = db_connect();

	//FTP�����擾����
	$sql = "SELECT * FROM ftpinfo";
	$sql = $sql . " WHERE id=0";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$resultw = $conn->prepare($sql);
	$resultw->execute();
	if ($row = $resultw->fetch(PDO::FETCH_ASSOC))
	{
		$ftp = array(
				"FTP_SERVER"	=> $row["ftp_server"],
				"FTP_USER_NAME"	=> $row["ftp_userid"],
				"FTP_USER_PASS"	=> $row["ftp_userpass"],
				"FTP_USER_ROOT"	=> $row["ftp_root"],
				);
	}
	$row = null;
	$resultw = null;

	//DB�۰��
	$conn = null;


	//FTP�T�[�o��ɒu���Ă���t�@�C��(�t���p�X)
	$remote_file = $ftp['FTP_USER_ROOT'] ."/" . $filename; // �� /fjcall/dumplist.TXT

	//�ڑ����m������
	$conn_id = ftp_connect($ftp['FTP_SERVER']);
	if ($conn_id == false)
	{
		echo "<script language=javascript>alert('FTP�ڑ��Ɏ��s���܂����I')</script>";
		$FTPdownload = false;
		return $FTPdownload;
	}

	//���[�U���ƃp�X���[�h�Ń��O�C������
	$login_result = ftp_login($conn_id, $ftp['FTP_USER_NAME'], $ftp['FTP_USER_PASS']);
	if ($login_result == false)
	{
		echo "<script language=javascript>alert('FTP�ڑ����O�C���Ɏ��s���܂����I')</script>";
		$FTPdownload = false;
		return $FTPdownload;
	}

	//�_�E�����[�h����t�@�C����FTP�ɑ��݂��邩
	$res = ftp_size($conn_id, $remote_file);
	if ($res == -1) 
	{
//		echo "<script language=javascript>alert('�Ώ�̧�ق�����܂���I')</script>";
		$FTPdownload = false;
	}else{
		//�p�b�V�u���[�h��OFF�ɂ���
		//ftp_pasv($conn_id, false);
		//�p�b�V�u���[�h��ON�ɂ��� --- �N���E�h�T�[�o SPG01
		ftp_pasv($conn_id, true);

		// �t�@�C�����_�E�����[�h���� (Get�R�}���h)
		if (!ftp_get($conn_id, $file, $remote_file, FTP_BINARY)) 
		{
			echo "<script language=javascript>alert('�t�@�C���̃_�E�����[�h�Ɏ��s���܂����I')</script>";
			$FTPdownload = false;
		}
	}

	// �ڑ������
	ftp_close($conn_id);
	
	return $FTPdownload;
	
}

//////////////////////////////////////////
// �_���v���X�g����؂�
//////////////////////////////////////////
function GetTxtRead( $r_fullfilename ) {

	//�z�񏉊���
	$memData = array();

	$contents = @file($r_fullfilename);

	//�t�@�C����������
	foreach($contents as $line){
		//�z��i�[
		$memData[] = explode(',',$line);
	}

	return $memData;

}
//////////////////////////////////////////
// �f�[�^�x�[�X�֍X�V����
// ��؁�wimport1��dcall_datadump
//////////////////////////////////////////
function PreMakeWkData( $l_data, $delete_filename ) {

	global $user;


	//DB�����
	$conn = db_connect();

	//ܰ��O�폜(wimport1)
	$sql = "DELETE FROM wimport1";
	$sql = $sql." WHERE userid ='". $user."'";//����
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;

	//��؁��ް��ް�(wimport1)
	//1�s�ڂ�ͯ�ް�ׁ̈A��荞�܂Ȃ�
	$memCnt = sizeof( $l_data );
	if( $memCnt > 1 ){ //2�s�ȏ゠��

		//ں��ސ���ٰ��
		for ( $i = 1; $i < $memCnt; $i++ ) { //2�s�ڂ���ǂݍ���
			//�����̕���
			$wHiduke = substr( $l_data[$i][0], 0, 4 ) . substr( $l_data[$i][0], 5, 2 ). substr( $l_data[$i][0], 8, 2 );
			$wJikan = substr( $l_data[$i][0], 11, 2 ) . substr( $l_data[$i][0], 14, 2 );

			$sql2 = "INSERT INTO wimport1";
			$sql2 = $sql2. "(userid, recno, calldatetime,calldate,calltime,syubetsu,trunkno,talktime,idnaisen,dialno,rumblingtime)";
			$sql2 = $sql2. " VALUES(";
			$sql2 = $sql2. "'" . $user."'";
			$sql2 = $sql2. "," . $i;
			$sql2 = $sql2. ",'" . $wHiduke . $wJikan . "'";
			$sql2 = $sql2. "," . $wHiduke;
			$sql2 = $sql2. ",'" . $wJikan ."'";
			$sql2 = $sql2. ",'" . $l_data[$i][1] ."'";
			$sql2 = $sql2. "," . $l_data[$i][3];
			$sql2 = $sql2. "," . $l_data[$i][4];
			$sql2 = $sql2. ",'" . rtrim($l_data[$i][5]) ."'";
			$sql2 = $sql2. ",'" . rtrim($l_data[$i][6]) ."'";
			$sql2 = $sql2. "," . $l_data[$i][7];
			$sql2 = $sql2. ")";
			$sql2 = mb_convert_encoding( $sql2, "SJIS-win", "SJIS-win");
			$result2 = $conn->prepare($sql2);
			$result2->execute();
			$result2 = null;

		}
	}

	//�捞���e���L�[�v���Ă���
	$wMaxDate = 0; $wMinDate = 0; $wRecCnt = 0;
	$sql = "SELECT Max(calldatetime) AS calldatetime_max, Min(calldatetime) AS calldatetime_min, Count(recno) AS recno_cnt";
	$sql = $sql . " FROM wimport1";
	$sql = $sql . " WHERE userid='" . $user . "'";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$wMaxDate = $rs["calldatetime_max"]; //�ő����
		$wMinDate = $rs["calldatetime_min"]; //�ŏ�����
		$wRecCnt = $rs["recno_cnt"]; //�捞����
	}
	$rs = null;
	$result = null;

	$wTargetFromDate = substr($wMinDate,0,8);
	$wTargetFromTime = substr($wMinDate,8,4);
	$wTargetToDate = substr($wMaxDate,0,8);
	$wTargetToTime = substr($wMaxDate,8,4);
	$wTargetCnt = $wRecCnt;

	//�O�폜
	$sql = "Delete From dcall_datadump";
	$sql = $sql  ." WHERE calldate>=" . $wTargetFromDate;
	$sql = $sql  ." AND   calldate<=" . $wTargetToDate;
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;


	//////////////////////////////////////
	// wimport1��dcall_datadump
	//////////////////////////////////////
	//�ő�RecNo�̎擾
	$wMaxNo = 0;
	$sql = "SELECT Max(recno) AS recno_max  FROM dcall_datadump";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$wMaxNo = $rs["recno_max"];
	}
	$rs = null;
	$result = null;


	//dcall_datadump��
	$sql = "SELECT wimport1.*, mtrunk.gaisenno, mtrunk.gaisenname, mtrunk.maintrunkno";
	$sql = $sql . " FROM wimport1 LEFT JOIN mtrunk ON wimport1.trunkno = mtrunk.trunkno";
	$sql = $sql . " WHERE wimport1.userid='" . $user . "'";
	$sql = $sql . " ORDER BY wimport1.recno";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$wMaxNo = $wMaxNo + 1;

		//���ԑ�
		$wTimeZone = substr( $rs["calltime"],0,2);
		$wTimeZone +=0;

		$sql2 = "INSERT INTO dcall_datadump";
		$sql2 = $sql2. "(recno, calldate,calltime,timezone,syubetsu,trunkno,telno,telname,talktime,idnaisen,dialno,rumblingtime,fusyutsuflg,guidance1flg,guidance2flg,talkieflg)";
		$sql2 = $sql2. " VALUES(";
		$sql2 = $sql2. $wMaxNo;
		$sql2 = $sql2. "," . $rs["calldate"];
		$sql2 = $sql2. ",'" . $rs["calltime"] . "'";
		$sql2 = $sql2. "," . $wTimeZone;
		$sql2 = $sql2. ",'" . $rs["syubetsu"] . "'";
		$sql2 = $sql2. "," . $rs["trunkno"];
		$sql2 = $sql2. ",'" . $rs["gaisenno"] . "'";
		$sql2 = $sql2. ",'" . $rs["gaisenname"] . "'";
		$sql2 = $sql2. "," . $rs["talktime"];
		$sql2 = $sql2. ",'" . $rs["idnaisen"] . "'";
		$sql2 = $sql2. ",'" . $rs["dialno"] . "'";
		$sql2 = $sql2. "," . $rs["rumblingtime"];
		$sql2 = $sql2. ",0";
		$sql2 = $sql2. ",0";
		$sql2 = $sql2. ",0";
		$sql2 = $sql2. "," . $rs["maintrunkno"];
		$sql2 = $sql2. ")";
		$sql2 = mb_convert_encoding( $sql2, "SJIS-win", "SJIS-win");
		$result2 = $conn->prepare($sql2);
		$result2->execute();
		$result2 = null;

	}
	$rs = null;
	$result = null;


	//�ʘb�����Ԃ��ǂ�����O���폜
	$sql = "DELETE FROM dcall_datadump";
//	$sql = $sql ." WHERE (talktime=0) AND (rumblingtime=0) AND (talkieflg=0)"; //�e����̂�
	$sql = $sql ." WHERE (talktime=0) AND (rumblingtime=0)"; //�S��
	$sql = $sql ." AND   (calldate>=" . $wTargetFromDate ." AND calldate<=" . $wTargetToDate . ")"; //����捞���t�̂�
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;

	//����ЊQ���M�̍폜(�g�����N3�`8)
	$sql = "DELETE FROM dcall_datadump";
	$sql = $sql ." WHERE (trunkno>=3 And trunkno<=8)";
	$sql = $sql ." AND   (calldate>=" . $wTargetFromDate ." AND calldate<=" . $wTargetToDate . ")"; //����捞���t�̂�
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;

	//�s�o�Đ�Flg�̍X�V
	$sql = "UPDATE dcall_datadump SET dcall_datadump.fusyutsuflg = 1";
	$sql = $sql . " WHERE (talktime=0)";
	$sql = $sql . " AND   (calldate>=" . $wTargetFromDate ." AND calldate<=" . $wTargetToDate . ")"; //����捞���t�̂�
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;

	//12�b�K�C�_���XFlg(839)
	$sql = "UPDATE dcall_datadump SET dcall_datadump.guidance1flg = 1";
	$sql = $sql . " WHERE (idnaisen='" . GUI12_1 . "' AND talktime>0)";
	$sql = $sql . " AND   (calldate>=" . $wTargetFromDate ." AND calldate<=" . $wTargetToDate . ")"; //����捞���t�̂�
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;

	//12�b�K�C�_���XFlg(839*2)
	$sql = "UPDATE dcall_datadump SET dcall_datadump.guidance2flg = 1";
	$sql = $sql . " WHERE (idnaisen='" . GUI12_2 . "' AND talktime>0)";
	$sql = $sql . " AND   (calldate>=" . $wTargetFromDate ." AND calldate<=" . $wTargetToDate . ")"; //����捞���t�̂�
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;


	//////////////////////////
	// �捞���O�̒ǉ��X�V
	// �ŏI�X�V���p�̼��ѓ��t
	//////////////////////////
	$NowYMD = date("Y").date("m").date("d");
	$NowHIS = date("His");

	$sql = "INSERT INTO dimportlog";
	$sql = $sql. "(updateDate, updateTime,datafrom,timefrom,datato,timeto,importcnt,modifyuserid)";
	$sql = $sql. " VALUES(";
	$sql = $sql. "'" . $NowYMD . "'";
	$sql = $sql. ",'" . $NowHIS . "'";
	$sql = $sql. "," . $wTargetFromDate;
	$sql = $sql. ",'" . $wTargetFromTime . "'";
	$sql = $sql. "," . $wTargetToDate;
	$sql = $sql. ",'" . $wTargetToTime . "'";
	$sql = $sql. "," . $wTargetCnt;
	$sql = $sql. ",'" . $user . "'";
	$sql = $sql. ")";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	$result = null;

	//////////////////////////
	// FTP�T�[�o����폜����
	//////////////////////////
	$ftp_err = FTPdelete( $conn, $delete_filename );



	//DB�۰��
	$conn = null;

}
//////////////////////////////////////////
// FTP����_�E�����[�h�ς݃e�L�X�g�t�@�C�����폜����
// $conn�FDB
// $filename�F�폜�����M�t�@�C����
//////////////////////////////////////////
function FTPdelete( $conn, $filename )
{

	$FTPdelete = true;

	//FTP�����擾����
	$sql = "SELECT * FROM ftpinfo";
	$sql = $sql . " WHERE id=0";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$resultw = $conn->prepare($sql);
	$resultw->execute();
	if ($row = $resultw->fetch(PDO::FETCH_ASSOC))
	{
		$ftp = array(
				"FTP_SERVER"	=> $row["ftp_server"],
				"FTP_USER_NAME"	=> $row["ftp_userid"],
				"FTP_USER_PASS"	=> $row["ftp_userpass"],
				"FTP_USER_ROOT"	=> $row["ftp_root"],
				);
	}
	$row = null;
	$resultw = null;


	//FTP�T�[�o��ɒu���Ă���t�@�C��(�t���p�X)
	$remote_file = $ftp['FTP_USER_ROOT'] ."/" . $filename; // �� /fjcall/dumplist.TXT

	//�ڑ����m������
	$conn_id = ftp_connect($ftp['FTP_SERVER']);
	if ($conn_id == false)
	{
		echo "<script language=javascript>alert('FTP�ڑ��Ɏ��s���܂����I')</script>";
		$FTPdelete = false;
		return $FTPdownload;
	}

	//���[�U���ƃp�X���[�h�Ń��O�C������
	$login_result = ftp_login($conn_id, $ftp['FTP_USER_NAME'], $ftp['FTP_USER_PASS']);
	if ($login_result == false)
	{
		echo "<script language=javascript>alert('FTP�ڑ����O�C���Ɏ��s���܂����I')</script>";
		$FTPdelete = false;
		return $FTPdownload;
	}

	//�_�E�����[�h����t�@�C����FTP�ɑ��݂��邩
	$res = ftp_size($conn_id, $remote_file);
	if ($res == -1) 
	{
		$FTPdelete = true;
	}else{
		//�p�b�V�u���[�h��OFF�ɂ���
		//ftp_pasv($conn_id, false);
		//�p�b�V�u���[�h��ON�ɂ��� --- �N���E�h�T�[�o SPG01
		ftp_pasv($conn_id, true);
		// �t�@�C�����폜����
		if (!ftp_delete($conn_id, $remote_file)) 
		{
			echo "<script language=javascript>alert('FTP�T�[�o��̃t�@�C���폜�Ɏ��s���܂����I')</script>";
			$FTPdelete = false;
		}
	}

	// �ڑ������
	ftp_close($conn_id);
	
	return $FTPdelete;
	
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


<body OnLoad="form01.btn.focus();">

<form name="form01" method="POST" action="<?= $this_pg ?>"  onsubmit="return false;">
<input type="hidden" name="comButton" value="">

<center>

	<!-- ۸޲ݏ��G���A -->
	<div id="logininfo">
		<table border="0">
			<tr>
				<td width="80"  align="left"><img src=<?php print $GUIDE_MODNAME_TOP ?> border="0"></td>
				<td width="160" align="left"><img src=<?php print $GUIDE_MODNAME6 ?> border="0"></td>
				<td width="230" align="left"><img src=<? print $LOGO_LOGIN_S ?> border="0">
											<?php print "�y " ; print $name; print " ����z" ?>
				</td>
				<td align="right" width="630">
					<a href="#" onClick="MyGoTopMenu()"><img src=<?= $MOD_LINKPRE_01 ?> border="0" alt="�A�J�E���g�ݒ�">�g�b�v���j���[�ɖ߂�</a>�b
					<a href="#" onClick="MyWondowLogout()">���O�A�E�g����</a>
				</td>
			</tr>
		</table>
		<hr>
	</div>

<?
	//�t�@�C�������݂��Ȃ��ꍇ�̓{�^������
	if($wExitFlg == 1){
		$addEna = "";
	}else{
		$addEna = "disabled";
	}
?>

	<!-- �捞�{�^���G���A -->
	<table width="910" border="0">
		<tr>
			<td width="200"><input type="button" name="btn" value="�_���v���X�g�捞" onClick="MyComAdd()" style="height:40px;width:160px" <?= $addEna ?>></td>
			<td width="510"><font color="red" style="font-size:8pt;"><label id="exit_label"><?= $exit_mess ?> &nbsp;</label></font></td>
			<td width="200"><font color="red" style="font-size:8pt;"><label id="wait_label"><?= $wait_mess ?> &nbsp;</label></font></td>

		</tr>
	</table>

	<!-- �捞���O�G���A -->
	<table width="910" height="700"  border="0">
		<tr>
			<td><img src=<? print $LOGO_DATATABLE2 ?> border="0" alt=<? print $modname?>></td>
		</tr>
		<tr>
			<td><iframe src="dataimp_list.php" name="RowMasterList" width="705" height="650" frameborder="1"></iframe></td>
		</tr>
	</table>

	<!-- �t�b�_�[�G���A -->
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
<SCRIPT Language="JavaScript" type="text/javascript" src="dataimp.js"></script>
