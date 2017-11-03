																																																																																																																																																																								<?php
require('fpdf.php');
class PDF extends FPDF
{
protected $B = 0;
protected $I = 0;
protected $U = 0;
protected $HREF = '';

function WriteHTML($html)
{
	// HTML parser
	$html = str_replace("\n",' ',$html);
	$a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach($a as $i=>$e)
	{
		if($i%2==0)
		{
			// Text
			if($this->HREF)
				$this->PutLink($this->HREF,$e);
			else
				$this->Write(5,$e);
		}
		else
		{
			// Tag
			if($e[0]=='/')
				$this->CloseTag(strtoupper(substr($e,1)));
			else
			{
				// Extract attributes
				$a2 = explode(' ',$e);
				$tag = strtoupper(array_shift($a2));
				$attr = array();
				foreach($a2 as $v)
				{
					if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
						$attr[strtoupper($a3[1])] = $a3[2];
				}
				$this->OpenTag($tag,$attr);
			}
		}
	}
}

function OpenTag($tag, $attr)
{
	// Opening tag
	if($tag=='B' || $tag=='I' || $tag=='U')
		$this->SetStyle($tag,true);
	if($tag=='A')
		$this->HREF = $attr['HREF'];
	if($tag=='BR')
		$this->Ln(5);
}

function CloseTag($tag)
{
	// Closing tag
	if($tag=='B' || $tag=='I' || $tag=='U')
		$this->SetStyle($tag,false);
	if($tag=='A')
		$this->HREF = '';
}

function SetStyle($tag, $enable)
{
	// Modify style and select corresponding font
	$this->$tag += ($enable ? 1 : -1);
	$style = '';
	foreach(array('B', 'I', 'U') as $s)
	{
		if($this->$s>0)
			$style .= $s;
	}
	$this->SetFont('',$style);
}

function PutLink($URL, $txt)
{
	// Put a hyperlink
	$this->SetTextColor(0,0,255);
	$this->SetStyle('U',true);
	$this->Write(5,$txt,$URL);
	$this->SetStyle('U',false);
	$this->SetTextColor(0);
}
}

$html = '<div>
<div style="width:50%;float:left;">Seshat Technologies Pvt. Ltd.                                   
<br>
</div>
<br><br><br><br>
<div align="center"><h3><b>                                       Payment Receipt</b></h3></div><br><br>
<div>Order No- '.$cd_order_id.'                               Date- '.$transaction_date.'<div>
<br><br>
<div>Received with thanks from '.$txn_orginiator_name.' the sum of <br> Rupees '.$transaction_price.' ('.convertNumber($transaction_price).')* through '.$transaction_method.' with reference no. '.$transaction_proc_ref_no.'<br> towards subscription of '.$package_name.' Package.  
</div>
<br><br><br><br>
<div>*Subject to realization of Payment.</div>

<br><br><br><br><br><br>
<div><b>Note : This is a computer generated receipt, therefore no<br> signature is required.</b></div>
</div>';


$pdf = new PDF();
// First page
$pdf->AddPage();
$pdf->SetFont('Arial','',20);
//$pdf->Write(5,"To find out what's new in this tutorial, click ");
$pdf->SetFont('','U');
$link = $pdf->AddLink();
//$pdf->Write(5,'here',$link);
$pdf->SetFont('');
// Second page
//$pdf->AddPage();
$pdf->SetLink($link);
//$pdf->Image('logo.png',10,12,30,0,'','http://www.fpdf.org');
$image1 = "http://collegedoors.com/pages/img/logo.jpg";
$pdf->Image($image1, 150, 10, 40, 30);
$pdf->SetLeftMargin(25);
$pdf->SetFontSize(14);
$pdf->WriteHTML($html);
//$pdf->Output();
?>