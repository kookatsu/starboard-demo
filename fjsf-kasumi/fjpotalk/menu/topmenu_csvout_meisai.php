<?php
//**********************************************************************
//�y�����z�ꗗCSV�o��(POST��̏���) (�N���C�A���gPC�ɒ��ڕۑ�����)
//**********************************************************************

	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";

	$this_pg = "topmenu_csvout_meisai.php";

	$companycode = $_SESSION["companycode_call"];//��ЃR�[�h
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];
	$maindeskcode = $_SESSION["maindeskcode_call"];

	//�e̫�тł̑I�����
	$selymd1 = $_GET["selymd1"];
	$selymd2 = $_GET["selymd2"];
	$gvalue = $_GET["gvalue"];

	$selclient = mb_substr($gvalue, 0, 3);
	$selenduser = mb_substr($gvalue, 3, 3);

	//DB�����
	$conn = db_connect();

	$endusername = GetEndUserName( $conn, $selclient, $selenduser ); //fjcall_comfunc

	//ͯ�ް���
	header("Content-Type: application/octet-stream");
	$outdate = date("Ymd_His");
	$outdate = $outdate."_timezonelist";
	header("Content-Disposition: attachment; filename=$outdate.csv");


	//�N���C�A���g�ƃG���h���[�U�̏o��
	$csvHead = "\"" . $endusername . "\",\"";
	$csvHead = $csvHead . "\"\n";
	//�w�b�_�̏o��
	print $csvHead;


	//�w�b�_�̍쐬
	$csvHead = "\"" . "No" . "\",\"";
	$csvHead = $csvHead .  "���t" . "\",\"";
	$csvHead = $csvHead .  "����" . "\",\"";
	$csvHead = $csvHead .  "�g�����N�ԍ�" . "\",\"";
	$csvHead = $csvHead .  "�O���ԍ�" . "\",\"";
	$csvHead = $csvHead .  "�O������" . "\",\"";
	$csvHead = $csvHead .  "�ʘb����" . "\",\"";
	$csvHead = $csvHead .  "����" . "\",\"";
	$csvHead = $csvHead .  "������" . "\",\"";
	$csvHead = $csvHead .  "G" . "\",\"";
	$csvHead = $csvHead .  "12" . "\",\"";
	$csvHead = $csvHead .  "�s";
	$csvHead = $csvHead . "\"\n";

	//�w�b�_�̏o��
	print $csvHead;

	//�f�[�^�o��
	$wRecCnt = 0;
	$sql = "SELECT " . $Const_DB_SCHEMA . "dcall_datadump.* FROM " . $Const_DB_SCHEMA . "dcall_datadump LEFT JOIN " . $Const_DB_SCHEMA . "mtrunk ON " . $Const_DB_SCHEMA . "dcall_datadump.trunkno = " . $Const_DB_SCHEMA . "mtrunk.trunkno";
	$sql = $sql . " WHERE (" . $Const_DB_SCHEMA . "dcall_datadump.calldate>=" . $selymd1 . " And " . $Const_DB_SCHEMA . "dcall_datadump.calldate<=" . $selymd2 . ")";
	$sql = $sql . "   AND (" . $Const_DB_SCHEMA . "mtrunk.clientcode='" . $selclient . "' AND " . $Const_DB_SCHEMA . "mtrunk.endusercode='" . $selenduser . "')";
	$sql = $sql . " ORDER BY " . $Const_DB_SCHEMA . "dcall_datadump.calldate, " . $Const_DB_SCHEMA . "dcall_datadump.calltime";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$wRecCnt = $wRecCnt + 1;
		$markG = ""; $markG12 = ""; $markF = "";

		//�s�o�Đ�
		if($rs["fusyutsuFLG"] == 1){
			$bkcolor_base = $GRID_MEISAI_COLOR2;
			$markF = "��";
		}
		if( $rs["guidance1FLG"] == 1 || $rs["guidance2FLG"] == 1 ){
			$bkcolor_base = $GRID_MEISAI_COLOR3;
			$markG12 = "��";
		}
		if( $rs["talkieFLG"] != 0 ){
			$bkcolor_base = $GRID_MEISAI_COLOR4;
			$markG = "��";
		}


		$contents = "\"" . $wRecCnt . "\",\"" 
                         . $rs["calldate"] . "\",\"" 
                         . $rs["calltime"] . "\",\"" 
                         . $rs["trunkno"] . "\",\"" 
                         . $rs["telno"] . "\",\"" 
                         . $rs["telname"] . "\",\"" 
                         . $rs["talktime"] . "\",\"" 
                         . $rs["idnaisen"] . "\",\"" 
                         . $rs["rumblingtime"] . "\",\"" 
                         . $markG . "\",\"" 
                         . $markG12 . "\",\"" 
                         . $markF . "\"\n";
		print $contents;

	}
	$rs = null;
	$result = null;
	$conn = null;
?>
