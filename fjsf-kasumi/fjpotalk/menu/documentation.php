<?php
/**
 * Demo pChart 
 */

require_once "../pChart/pChart/pData.class";
require_once "../pChart/pChart/pChart.class";

/**
 * プロットデータ設定 
 */

// プロットデータの用意
$pdata = new pData();
$pdata->AddPoint(array(8, 4, 6, 9, 25), '系列A');
$pdata->AddPoint(array(2, 0, 9, 12, 8), '系列B');

// X軸ラベルの用意
$pdata->AddPoint(range(1,5), 'Xlabels');

// プロットする系列の設定
$pdata->AddSerie('系列A');
$pdata->AddSerie('系列B');

// X軸ラベルの設定
$pdata->SetAbsciseLabelSerie("Xlabels");


/**
 * グラフ作成 
 */

// グラフ初期化 (画像サイズを設定: width, height)
$pchart = new pChart(900, 300);

// ラベルに使うFont設定
$pchart->setFontProperties('../pChart/pChart/Fonts/TakaoExGothic.ttf', 8);

// 画像のうち、グラフを描画する範囲を設定
$pchart->setGraphArea(80, 20, 680, 280);

// グラフ領域を描画
$pchart->drawGraphArea(255, 255, 255,false);

// 目盛を描画
$pchart->drawScale($pdata->GetData(), $pdata->GetDataDescription(), SCALE_NORMAL, 30, 30, 30, true, 0, 2);

// グリッドを描画
$pchart->drawGrid(4, true, 235, 235, 235, 50);

// データ点をプロット
$pchart->drawPlotGraph($pdata->GetData(), $pdata->GetDataDescription());

// データを線で結ぶ
$pchart->drawLineGraph($pdata->GetData(), $pdata->GetDataDescription());

// 凡例を描画
$pchart->drawLegend(690, 20, $pdata->GetDataDescription(), 255, 255, 255);

// グラフをブラウザに描画
$pchart->Stroke();