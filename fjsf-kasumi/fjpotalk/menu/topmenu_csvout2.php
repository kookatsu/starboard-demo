<?php
//**********************************************************************
//�y�����z�ꗗCSV�o��(POST��̏���) (�N���C�A���gPC�ɒ��ڕۑ�����)
//**********************************************************************

	session_start();

	include "../common/MYDB.php";
	include "../common/MYCONST.php";
	include "../common/fjcall_comfunc.php";

	$this_pg = "topmenu_csvout2.php";

	$companycode = $_SESSION["companycode_call"];//��ЃR�[�h
	$user = $_SESSION['userid_call'];
	$name = $_SESSION['name_call'];
	$level = $_SESSION['levelid_call'];
	$maindeskcode = $_SESSION["maindeskcode_call"];

	//�e̫�тł̑I�����
	$selclient = $_GET["selclient"];
	$selenduser = $_GET["selenduser"];

	//DB�����
	$conn = db_connect();

	//�N���C�A���g���̎擾
	if($selclient =="ALL"){
		$clientname = "�S�Ă̂��q�l";
	}else{
		$sql = "Select clientname " . $Const_DB_SCHEMA . "From mclient";
		$sql = $sql . " WHERE clientcode='" . $selclient . "'";
		$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
		$result = $conn->prepare($sql);
		$result->execute();
		if ($rs = $result->fetch(PDO::FETCH_ASSOC)){
			$clientname = $rs["clientname"];
		}
		$rs = null;
		$result = null;
	}
	//�G���h���[�U���̎擾
	if($selenduser =="ALL"){
		$endusername = "�S�Ẵ��[�U";
	}else{
		$endusername = GetEndUserName( $conn, $selclient, $selenduser ); //fjcall_comfunc
	}

	//ͯ�ް���
	header("Content-Type: application/octet-stream");
	$outdate = date("Ymd_His");
	$outdate = $outdate."_dailylist";
	header("Content-Disposition: attachment; filename=$outdate.csv");


	//�N���C�A���g�ƃG���h���[�U�̏o��
	$csvHead = "\"" . $clientname . "\",\"";
	$csvHead = $csvHead .  $endusername . "\",\"";
	$csvHead = $csvHead . "\"\n";
	//�w�b�_�̏o��
	print $csvHead;


	//�w�b�_�̍쐬
	$csvHead = "\"" . "��" . "\",\"";
	$csvHead = $csvHead .  "�j��" . "\",\"";
	$csvHead = $csvHead .  "�����M��" . "\",\"";
	$csvHead = $csvHead .  "�ʘb��" . "\",\"";
	$csvHead = $csvHead .  "�ʘb��" . "\",\"";
	$csvHead = $csvHead .  "�ʘb�b" . "\",\"";
	$csvHead = $csvHead .  "�K�C�_���X��" . "\",\"";
	$csvHead = $csvHead .  "�K�C�_���X��" . "\",\"";
	$csvHead = $csvHead .  "12�b�K�C�_���X��" . "\",\"";
	$csvHead = $csvHead .  "12�b�K�C�_���X��" . "\",\"";
	$csvHead = $csvHead .  "�s�o�Đ�" . "\",\"";
	$csvHead = $csvHead .  "�s�o�Đ���";
	$csvHead = $csvHead . "\"\n";

	//�w�b�_�̏o��
	print $csvHead;

	//�f�[�^�o��
	$sql = "SELECT * FROM " . $Const_DB_SCHEMA . "wdeskhistory2day";
	$sql = $sql . " WHERE userid='" . $Const_COMPANYCODE. $user . "'";
	$sql = $sql . " ORDER BY hiduke";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		//�����M��
		$wTotalDaySu = $rs["talksu"] +  $rs["guidanceSu"] + $rs["guidance12Su"] + $rs["fusyutsuSu"];

		//�ʘb��%
		if($wTotalDaySu!=0){
			$talkP = ($rs["talksu"] / $wTotalDaySu) *100;
		}else{
			$talkP = 0;
		}
		//�K�C�_���X%
		if($wTotalDaySu!=0){
			$guidanceP = ($rs["guidanceSu"] / $wTotalDaySu) *100;
		}else{
			$guidanceP = 0;
		}
		//�K�C�_���X12%
		if($wTotalDaySu!=0){
			$guidance12P = ($rs["guidance12Su"] / $wTotalDaySu) *100;
		}else{
			$guidance12P = 0;
		}
		//�s�o�Đ�%
		if($wTotalDaySu!=0){
			$fusyutsuP = ($rs["fusyutsuSu"] / $wTotalDaySu) *100;
		}else{
			$fusyutsuP = 0;
		}

		//���t�\���p(���v�s�͋󗓂ɂ�����)
		if($rs["RecFlg"] == 9){
			$dspDay2 = "";
		}else{
			$dspDay2 = $rs["dspday"];
		}

		$contents = "\"" . $dspDay2 . "\",\"" 
                         . $rs["youbimei"] . "\",\"" 
                         . number_format($wTotalDaySu,0) . "\",\"" 
                         . number_format($rs["talksu"],0) . "\",\"" 
                         . number_format($talkP,1) . "\",\"" 
                         . number_format($rs["talktime"],0) . "\",\"" 
                         . number_format($rs["guidanceSu"],0) . "\",\"" 
                         . number_format($guidanceP,1) . "\",\"" 
                         . number_format($rs["guidance12Su"],0) . "\",\"" 
                         . number_format($guidance12P,1) . "\",\"" 
                         . number_format($rs["fusyutsuSu"],0) . "\",\"" 
                         . number_format($fusyutsuP,1) . "\"\n";
		print $contents;

	}
	$rs = null;
	$result = null;
	$conn = null;
?>
