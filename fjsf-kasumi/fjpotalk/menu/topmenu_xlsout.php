<?php
//**********************************************************************
//【処理】一覧EXCEL出力(POST先の処理) (クライアントPCに直接保存する)

// php.iniで有効にする必要あり
// Windowsサーバの場合：extension=php_zip.dll
// Linuxサーバの場合：extension=zip.so
//**********************************************************************

	session_start();

	include "../common/MYDB.php";

	$this_pg = "topmenu_xlsout.php";
	$modname = "トップメニュー";

	$user = $_SESSION['userid_call001'];
	$name = $_SESSION['name_call001'];

	//親ﾌｫｰﾑでの選択情報
	$fromdate = $_GET["selymdf"];
	$todate = $_GET["selymdt"];
	$seljyokyo = $_GET["jyokyo"];
	$selbunrui = $_GET["bunrui"];

	$selymdF = str_replace("/", "", $fromdate);//ｽﾗｯｼｭを外す
	$selymdT = str_replace("/", "", $todate);//ｽﾗｯｼｭを外す
	$seldspymd = $selymdF . "～" . $selymdT; //プロパティ用

	/** Error reporting */
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);


	/** Include PHPExcel */
	require_once  '../Classes/PHPExcel.php';


	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();

	// Excelファイルのプロパティ
	$objPHPExcel->getProperties()->setCreator($user) //作成者
								 ->setLastModifiedBy($user) //前回保存者
								 ->setTitle($modname) //タイトル
								 ->setSubject($modname) //件名
								 ->setKeywords($modname) //タグ
								 ->setCategory($selbunrui) //分類項目
								 ->setDescription($seldspymd); //コメント

	//一番最初のシートを選択
	$objPHPExcel->setActiveSheetIndex(0);

	//選択シートにアクセスを開始
	$sheet = $objPHPExcel->getActiveSheet();

	//シート全体のフォント
	$sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');
	$sheet->getDefaultStyle()->getFont()->setSize(9);

	//セルにデータセット
	$sheet->setCellValueByColumnAndRow(0 , 1, 'No.' );
	$sheet->setCellValueByColumnAndRow(1 , 1, '問合せ番号' );
	$sheet->setCellValueByColumnAndRow(2 , 1, '受付日' );
	$sheet->setCellValueByColumnAndRow(3 , 1, '受付時間' );
	$sheet->setCellValueByColumnAndRow(4 , 1, '状況' );
	$sheet->setCellValueByColumnAndRow(5 , 1, '完了日' );
	$sheet->setCellValueByColumnAndRow(6 , 1, '完了時間' );
	$sheet->setCellValueByColumnAndRow(7 , 1, '対応期間' );
	$sheet->setCellValueByColumnAndRow(8 , 1, '店舗' );
	$sheet->setCellValueByColumnAndRow(9 , 1, '分類' );
	$sheet->setCellValueByColumnAndRow(10, 1, '種別' );
	//罫線
	$sheet->getStyleByColumnAndRow(0 , 1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$sheet->getStyleByColumnAndRow(1 , 1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$sheet->getStyleByColumnAndRow(2 , 1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$sheet->getStyleByColumnAndRow(3 , 1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$sheet->getStyleByColumnAndRow(4 , 1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$sheet->getStyleByColumnAndRow(5 , 1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$sheet->getStyleByColumnAndRow(6 , 1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$sheet->getStyleByColumnAndRow(7 , 1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$sheet->getStyleByColumnAndRow(8 , 1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$sheet->getStyleByColumnAndRow(9 , 1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$sheet->getStyleByColumnAndRow(10, 1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);



	//DBｵｰﾌﾟﾝ
    $conn = db_connect();

	$Reccnt=0;
	$sql = "SELECT * From dcallhistory";
	$sql = $sql . " WHERE ukedate >=" . $selymdF;
	$sql = $sql . " AND   ukedate <=" . $selymdT;
	//状況の選択あり
	if( $seljyokyo != "ALL" ){
		$sql = $sql . " AND jyokyocode ='" . $seljyokyo . "'";
	}
	//分類の選択あり
	if( $selbunrui != "ALL" ){
		$sql = $sql . " AND bunruicode ='" . $selbunrui . "'";
	}
	$sql = $sql . " ORDER BY ukedate,uketime,toiawaseno";
	$sql = mb_convert_encoding( $sql, "SJIS-win", "SJIS-win");
	$result = $conn->prepare($sql);
	$result->execute();
	while ($rs = $result->fetch(PDO::FETCH_ASSOC))
	{
		$Reccnt = $Reccnt + 1;

		if($rs["ukedate"] == "" ){
			$uketuke = "";
		}else{
			$uketuke = substr($rs["ukedate"],0,4).'/'.substr($rs["ukedate"],4,2).'/'.substr($rs["ukedate"],6,2);
		}
		if($rs["uketime"] == "" ){
			$uketime = "";
		}else{
			$uketime = substr($rs["uketime"],0,2).':'.substr($rs["uketime"],2,2);
		}
		//完了日
		if($rs["kanryobi"] == "" ){
			$kanryobi = "";
		}else{
			$kanryobi = substr($rs["kanryobi"],0,4).'/'.substr($rs["kanryobi"],4,2).'/'.substr($rs["kanryobi"],6,2);
		}
		if($rs["kanryotime"] == "" ){
			$kanryotime = "";
		}else{
			$kanryotime = substr($rs["kanryotime"],0,2).':'.substr($rs["kanryotime"],2,2);
		}

		$wNo = $rs['toiawaseno'];
		$wNo +=0;

		//対応期間の表示
		$wtaioujikan = $rs['taioujikan']; //累計の分
		$wtaioujikanDay = $rs['taioujikanDay']; //日
		$wtaioujikanHour = $rs['taioujikanHour']; //時間
		$wtaioujikanTime = $rs['taioujikanTime']; //分

		$wDspTaioujikan = "";
		if( $wtaioujikan != 0 ){
			if ( $wtaioujikanDay >0 ){
				$wDspTaioujikan = $wtaioujikanDay . "日";
			}else{
				if ( $wtaioujikanHour >0 ){
					$wDspTaioujikan = $wtaioujikanHour . "時間";
				}
				if ( $wtaioujikanTime >0 ){
					$wDspTaioujikan = $wDspTaioujikan . $wtaioujikanTime . "分";
				}
			}
		}else{
			$wDspTaioujikan = " ";
		}

		//データセット
		$sheet->setCellValueByColumnAndRow(0 , $Reccnt+1, $Reccnt );
		$sheet->setCellValueByColumnAndRow(1 , $Reccnt+1, $wNo );
		$sheet->setCellValueByColumnAndRow(2 , $Reccnt+1, $uketuke );
		$sheet->setCellValueByColumnAndRow(3 , $Reccnt+1, $uketime );
		$sheet->setCellValueByColumnAndRow(4 , $Reccnt+1, mb_convert_encoding($rs['jyokyo'],"UTF-8","SJIS-win") );
		$sheet->setCellValueByColumnAndRow(5 , $Reccnt+1, $kanryobi );
		$sheet->setCellValueByColumnAndRow(6 , $Reccnt+1, $kanryotime );
		$sheet->setCellValueByColumnAndRow(7 , $Reccnt+1, $wDspTaioujikan );
		$sheet->setCellValueByColumnAndRow(8 , $Reccnt+1,  mb_convert_encoding($rs['storename'],"UTF-8","SJIS-win") );
		$sheet->setCellValueByColumnAndRow(9 , $Reccnt+1,  mb_convert_encoding($rs['bunrui'],"UTF-8","SJIS-win") );
		$sheet->setCellValueByColumnAndRow(10, $Reccnt+1, mb_convert_encoding($rs['syubetsu'],"UTF-8","SJIS-win") );


		//罫線
		$sheet->getStyleByColumnAndRow(0 , $Reccnt+1 )->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
		$sheet->getStyleByColumnAndRow(1 , $Reccnt+1 )->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
		$sheet->getStyleByColumnAndRow(2 , $Reccnt+1 )->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
		$sheet->getStyleByColumnAndRow(3 , $Reccnt+1 )->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
		$sheet->getStyleByColumnAndRow(4 , $Reccnt+1 )->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
		$sheet->getStyleByColumnAndRow(5 , $Reccnt+1 )->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
		$sheet->getStyleByColumnAndRow(6 , $Reccnt+1 )->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
		$sheet->getStyleByColumnAndRow(7 , $Reccnt+1 )->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
		$sheet->getStyleByColumnAndRow(8 , $Reccnt+1 )->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
		$sheet->getStyleByColumnAndRow(9 , $Reccnt+1 )->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
		$sheet->getStyleByColumnAndRow(10, $Reccnt+1 )->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);

	}
	$result = null;
	$conn = null;






	// ワークシート名のセット
	$objPHPExcel->getActiveSheet()->setTitle($modname);

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);


	// Redirect output to a client’s web browser (Excel2007)
	$outdate = date("Ymd_His");
	$outdate = $outdate."_topmenu.xlsx";

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//	header('Content-Disposition: attachment;filename="01simple.xlsx"');
	header("Content-Disposition: attachment;filename=$outdate");
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Thu, 01 Dec 1994 16:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');


?>
