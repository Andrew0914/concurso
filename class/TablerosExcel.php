<?php 
	//require_once dirname(__FILE__) . '/excel/PHPExcel.php';
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__) . '/TableroMaster.php';
	require_once dirname(__FILE__) . '/TableroPosiciones.php';
	require_once dirname(__FILE__) . '/TableroPasos.php';

	/*$objPHPExcel = new PHPExcel;
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
	$objSheet = $objPHPExcel->getSheet(0);

	$objSheet->setTitle('My sales report');
	$objSheet->getCell('A1')->setValue('Product');
	$objSheet->getCell('B1')->setValue('Quanity');

	$objSheet = $objPHPExcel->createSheet(1);
	$objSheet->setTitle('My sales report');
	$objSheet->getCell('A1')->setValue('ANdrew');
	$objSheet->getCell('B1')->setValue('Dany');

	$objWriter->save('test.xlsx');*/

 ?>