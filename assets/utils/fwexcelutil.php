<?php
require_once dirname(__FILE__).'/../Classes/PHPExcel.php';
date_default_timezone_set('Asia/Kolkata');

class fwExcel {
	function generate($detail,$header,$reportname) {
		$X=new PHPExcel();
		$X->getProperties()->setCreator("RCA")->setLastModifiedBy("RCA")->setTitle($reportname)->setSubject($reportname)->setDescription($reportname);
		$X->setActiveSheetIndex(0);
		$S=$X->getActiveSheet();

		$xlrow=1;
		$xlcol=1;
		foreach($header as $hk => $hd) {
			$S->getStyle($this->colName($xlcol).$xlrow)->applyFromArray(
	    		array(
	        		'fill' => array(
	            		'type' => PHPExcel_Style_Fill::FILL_SOLID,
	            		'color' => array('rgb' => 'CCCCCC')
	            	)
	    		)
			);
			$S->setCellValue($this->colName($xlcol).$xlrow,$hd['display_name']);
			$xlcol++;
		}
		$xlrow++;
		$xlcol=1;
		foreach($detail as $drk => $dr) {
			foreach($dr as $dck => $d) {
				$S->setCellValue($this->colName($xlcol).$xlrow,$d);
				$xlcol++;
			}
			$xlrow++;
			$xlcol=1;
		}

		foreach(range($this->colName(1),$this->colName(count($header))) as $columnID) {
    		$S->getColumnDimension($columnID)->setAutoSize(true);
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$reportname.'.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($X, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
	function colName($num){
		$num=$num-1;
    	$alphabet =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
    	$alpha_flip = array_flip($alphabet);
    	if($num <= 25){
			return strtoupper($alphabet[$num]);
    	}
    	elseif($num > 25){
      		$dividend = ($num + 1);
      		$alpha = '';
      		$modulo;
      		while ($dividend > 0) {
        		$modulo = ($dividend - 1) % 26;
        		$alpha = $alphabet[$modulo] . $alpha;
        		$dividend = floor((($dividend - $modulo) / 26));
      		} 
      		return strtoupper($alpha);
    	}
	}
}

?>