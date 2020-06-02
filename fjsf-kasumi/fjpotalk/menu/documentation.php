<?php
/**
 * Demo pChart 
 */

require_once "../pChart/pChart/pData.class";
require_once "../pChart/pChart/pChart.class";

/**
 * �ץ�åȥǡ������� 
 */

// �ץ�åȥǡ������Ѱ�
$pdata = new pData();
$pdata->AddPoint(array(8, 4, 6, 9, 25), '����A');
$pdata->AddPoint(array(2, 0, 9, 12, 8), '����B');

// X����٥���Ѱ�
$pdata->AddPoint(range(1,5), 'Xlabels');

// �ץ�åȤ�����������
$pdata->AddSerie('����A');
$pdata->AddSerie('����B');

// X����٥������
$pdata->SetAbsciseLabelSerie("Xlabels");


/**
 * ����պ��� 
 */

// ����ս���� (����������������: width, height)
$pchart = new pChart(900, 300);

// ��٥�˻Ȥ�Font����
$pchart->setFontProperties('../pChart/pChart/Fonts/TakaoExGothic.ttf', 8);

// �����Τ���������դ����褹���ϰϤ�����
$pchart->setGraphArea(80, 20, 680, 280);

// ������ΰ������
$pchart->drawGraphArea(255, 255, 255,false);

// ����������
$pchart->drawScale($pdata->GetData(), $pdata->GetDataDescription(), SCALE_NORMAL, 30, 30, 30, true, 0, 2);

// ����åɤ�����
$pchart->drawGrid(4, true, 235, 235, 235, 50);

// �ǡ�������ץ�å�
$pchart->drawPlotGraph($pdata->GetData(), $pdata->GetDataDescription());

// �ǡ��������Ƿ��
$pchart->drawLineGraph($pdata->GetData(), $pdata->GetDataDescription());

// ���������
$pchart->drawLegend(690, 20, $pdata->GetDataDescription(), 255, 255, 255);

// ����դ�֥饦��������
$pchart->Stroke();