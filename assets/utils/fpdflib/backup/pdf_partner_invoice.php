																																																																																																																																																																								<?php
require('fpdf.php');
class PDF extends FPDF
{
protected $B = 0;
protected $I = 0;
protected $U = 0;
protected $HREF = '';
function FancyTable($header,$data)
{
//Colors, line width and bold font
// $this->SetFillColor(255,0,0);
// $this->SetTextColor(255);
// $this->SetDrawColor(128,0,0);
// $this->SetLineWidth(.3);
// $this->SetFont('','B');
//Header
$w=array(20,20,20,30,15,15,20,20,20,20);
$i = 0;
$fill=false;
$x0=$x = $this->GetX();
$y = $this->GetY();
foreach($header as $headerrow)
{

for ($i=0; $i<10; $i++) //Avoid very lengthy texts

{ 

$headerrow[$i]=substr($headerrow[$i],0,160);

}

$yH=30; //height of the row
$this->SetXY($x, $y);
//$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
$this->Cell($w[0], $yH, "",1,0,'',$fill);
$this->SetXY($x, $y);
$this->MultiCell($w[0],6,$headerrow[0],0,'L'); 


$this->SetXY($x + $w[0], $y);
$this->Cell($w[1], $yH, "",1,0,'',$fill); 
$this->SetXY($x + $w[0], $y);
$this->MultiCell($w[1],6,$headerrow[1],0,'L'); 


$x =$x+$w[0];
$this->SetXY($x + $w[1], $y);
$this->Cell($w[2], $yH, "",1,0,'',$fill); 
$this->SetXY($x + $w[1], $y);
$this->MultiCell($w[2],6,$headerrow[2],0,'L'); 

$x =$x+$w[1];
$this->SetXY($x + $w[2], $y);
$this->Cell($w[3], $yH, "",1,0,'',$fill); 
$this->SetXY($x + $w[2], $y); 
$this->MultiCell($w[3],6,$headerrow[3],0,'L'); 

$x =$x+$w[2];
$this->SetXY($x + $w[3], $y);
$this->Cell($w[4], $yH, "",1,0,'',$fill); 
$this->SetXY($x + $w[3], $y); 
$this->MultiCell($w[4],6,$headerrow[4],0,'L'); 

$x =$x+$w[3]; 
$this->SetXY($x + $w[4],$y);
$this->Cell($w[5], $yH, "",1,0,'',$fill); 
$this->SetXY($x + $w[4], $y); 
$this->MultiCell($w[5],6,$headerrow[5],0,'L'); 

$x =$x+$w[4]; 
$this->SetXY($x + $w[5],$y);
$this->Cell($w[6], $yH, "",1,0,'',$fill); 
$this->SetXY($x + $w[5], $y); 
$this->MultiCell($w[6],6,$headerrow[6],0,'L'); 

$x =$x+$w[5]; 
$this->SetXY($x + $w[6],$y);
$this->Cell($w[7], $yH, "",1,0,'',$fill); 
$this->SetXY($x + $w[6], $y); 
$this->MultiCell($w[7],6,$headerrow[7],0,'L'); 

$x =$x+$w[6]; 
$this->SetXY($x + $w[7],$y);
$this->Cell($w[8], $yH, "",1,0,'',$fill); 
$this->SetXY($x + $w[7], $y); 
$this->MultiCell($w[8],6,$headerrow[8],0,'L'); 

$x =$x+$w[7]; 
$this->SetXY($x + $w[8],$y);
$this->Cell($w[9], $yH, "",1,0,'',$fill); 
$this->SetXY($x + $w[8], $y); 
$this->MultiCell($w[9],6,$headerrow[9],0,'L'); 

$y=$y+$yH; //move to next row
$x=$x0; //start from firt column
$fill=!$fill;
}
$this->Ln();
//Color and font restoration
$this->SetFillColor(224,235,255);
$this->SetTextColor(0);
$this->SetFont('');
//Data
$fill=false;

$i = 0;


$x0=$x = $this->GetX();
$y = $this->GetY();
foreach($data as $row)
{

for ($i=0; $i<10; $i++) //Avoid very lengthy texts

{ 

$row[$i]=substr($row[$i],0,160);

}

$yH=30; //height of the row
$this->SetXY($x, $y);
$this->Cell($w[0], $yH, "", 'LRB',0,'',$fill);
$this->SetXY($x, $y);
$this->MultiCell($w[0],6,$row[0],0,'L'); 


$this->SetXY($x + $w[0], $y);
$this->Cell($w[1], $yH, "", 'LRB',0,'',$fill); 
$this->SetXY($x + $w[0], $y);
$this->MultiCell($w[1],6,$row[1],0,'L'); 


$x =$x+$w[0];
$this->SetXY($x + $w[1], $y);
$this->Cell($w[2], $yH, "", 'LRB',0,'',$fill); 
$this->SetXY($x + $w[1], $y);
$this->MultiCell($w[2],6,$row[2],0,'L'); 

$x =$x+$w[1];
$this->SetXY($x + $w[2], $y);
$this->Cell($w[3], $yH, "", 'LRB',0,'',$fill); 
$this->SetXY($x + $w[2], $y); 
$this->MultiCell($w[3],6,$row[3],0,'L'); 

$x =$x+$w[2];
$this->SetXY($x + $w[3], $y);
$this->Cell($w[4], $yH, "", 'LRB',0,'',$fill); 
$this->SetXY($x + $w[3], $y); 
$this->MultiCell($w[4],6,$row[4],0,'L'); 

$x =$x+$w[3]; 
$this->SetXY($x + $w[4],$y);
$this->Cell($w[5], $yH, "", 'LRB',0,'',$fill); 
$this->SetXY($x + $w[4], $y); 
$this->MultiCell($w[5],6,$row[5],0,'L'); 

$x =$x+$w[4]; 
$this->SetXY($x + $w[5],$y);
$this->Cell($w[6], $yH, "", 'LRB',0,'',$fill); 
$this->SetXY($x + $w[5], $y); 
$this->MultiCell($w[6],6,$row[6],0,'L'); 

$x =$x+$w[5]; 
$this->SetXY($x + $w[6],$y);
$this->Cell($w[7], $yH, "", 'LRB',0,'',$fill); 
$this->SetXY($x + $w[6], $y); 
$this->MultiCell($w[7],6,$row[7],0,'L'); 

$x =$x+$w[6]; 
$this->SetXY($x + $w[7],$y);
$this->Cell($w[8], $yH, "", 'LRB',0,'',$fill); 
$this->SetXY($x + $w[7], $y); 
$this->MultiCell($w[8],6,$row[8],0,'L'); 

$x =$x+$w[7]; 
$this->SetXY($x + $w[8],$y);
$this->Cell($w[9], $yH, "", 'LRB',0,'',$fill); 
$this->SetXY($x + $w[8], $y); 
$this->MultiCell($w[9],6,$row[9],0,'L'); 

$y=$y+$yH; //move to next row
$x=$x0; //start from firt column
$fill=!$fill;
}

}
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
function ImprovedTable($header, $data)
{
	// Column widths
	$w = array(20, 30, 20, 25, 25, 25, 25, 25, 25, 25);
	// Header
	for($i=0;$i<count($header);$i++)
		$this->Cell($w[$i],20,$header[$i],1,0,'LR');
	$this->Ln();
	// Data
	foreach($data as $row)
	{
		$this->Cell($w[0],6,$row[0],'LR');
		$this->Cell($w[1],6,$row[1],'LR');
		$this->Cell($w[2],6,$row[2],'LR');
		$this->Cell($w[3],6,$row[3],'LR');
		$this->Cell($w[4],6,$row[4],'LR');
		$this->Cell($w[5],6,$row[5],'LR');
		$this->Cell($w[6],6,$row[6],'LR');
		$this->Cell($w[7],6,$row[7],'LR');

		$this->Cell($w[8],6,$row[8],'LR');
		$this->Cell($w[9],6,$row[9],'LR');
		//$this->Cell($w[10],6,$row[10],'LR');
		// $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R');
		// $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R');
		$this->Ln();
	}
	// Closing line
	$this->Cell(array_sum($w),0,'','T');
}

function BasicTable($header, $data)
{
	// Header
	foreach($header as $col){
		//$this->WordWrap($col,20);
		$this->MultiCell(20,20,$col,1);
		
	}$this->Ln();
	// Data
	foreach($data as $row)
	{
		foreach($row as $col)
			$this->Cell(20,6,$col,1);
		$this->Ln();
	}
}
function WordWrap(&$text, $maxwidth)
{
    $text = trim($text);
    if ($text==='')
        return 0;
    $space = $this->GetStringWidth(' ');
    $lines = explode("\n", $text);
    $text = '';
    $count = 0;

    foreach ($lines as $line)
    {
        $words = preg_split('/ +/', $line);
        $width = 0;

        foreach ($words as $word)
        {
            $wordwidth = $this->GetStringWidth($word);
            if ($wordwidth > $maxwidth)
            {
                // Word is too long, we cut it
                for($i=0; $i<strlen($word); $i++)
                {
                    $wordwidth = $this->GetStringWidth(substr($word, $i, 1));
                    if($width + $wordwidth <= $maxwidth)
                    {
                        $width += $wordwidth;
                        $text .= substr($word, $i, 1);
                    }
                    else
                    {
                        $width = $wordwidth;
                        $text = rtrim($text)."\n".substr($word, $i, 1);
                        $count++;
                    }
                }
            }
            elseif($width + $wordwidth <= $maxwidth)
            {
                $width += $wordwidth + $space;
                $text .= $word.' ';
            }
            else
            {
                $width = $wordwidth + $space;
                $text = rtrim($text)."\n".$word.' ';
                $count++;
            }
        }
        $text = rtrim($text)."\n";
        $count++;
    }
    $text = rtrim($text);
    return $count;
}

}
 //print_r($get_invoice_dtl_res);die;

$html= '<div>
<div>
<b>'.$get_invoice_dtl_res['fname'].'</b><br>
'.$get_invoice_dtl_res['user_add_city'].'<br>
Tel:'.$get_invoice_dtl_res['mob_phone'].'<br>
Email:'.$get_invoice_dtl_res['email'].'
</div>
<br><br><br>
<div>
To,<br>
Seshat Technologies Pvt. Ltd.                                	                <b>Invoice No # : '.$get_invoice_dtl_res['invoice_no'].'</b><br>
602, IJMIMA Complex, Behind Goregaon Sports Club,	  				             Date : '.$invoice_date.'<br>
Off Link Road, Malad (West), Mumbai - 400064<br>
Tel : (+91) 22-28815043, Fax : (+91) 22-28815043<br>
Email : support@CollegeDoors.com<br>
Web:  www.CollegeDoors.com<br>

</div>


</div><br><br>';

$pdf = new PDF();

$header[0] = array("Order ".$pdf->Ln()." No.", 'Email ID', 'Transaction type', '
Transaction date',"Package".$pdf->Ln()." Name",'List Price (A)','Price Benefit and Taxes (B)','Net Revenue (N=A-B)',
'Revenue Share % (R)',"Revenue Share".$pdf->Ln()."  Amount (N*R)"
);
$data  = array();
$data2  = array();
$total = 0;

$count_other_dtls = count($get_invoice_pdf_other_dtls_res);

foreach($get_invoice_pdf_other_dtls_res as $key=>$dtls_val){
	if($key < 5){
		$price_benefit = $dtls_val['transaction_tax'] - $dtls_val['transaction_discount'];
		$net_revenue = $dtls_val['transaction_price'] - $dtls_val['transaction_tax'];
		$revenue_share = $dtls_val['value']*100;
		$data[] = array($dtls_val['cd_order_no'],$dtls_val['txn_orginiator_email'],$dtls_val['transaction_type'],
			$dtls_val['transaction_date'],$dtls_val['package_name'],$dtls_val['unit_price'],$price_benefit,
			$net_revenue, $revenue_share,$dtls_val['txn_amount']
			);
		$total = $total + $dtls_val['txn_amount'];
	}

}

if($count_other_dtls > 4){

	foreach($get_invoice_pdf_other_dtls_res as $key=>$dtls_val){
		if($key > 4){
			$price_benefit = $dtls_val['transaction_tax'] - $dtls_val['transaction_discount'];
			$net_revenue = $dtls_val['transaction_price'] - $dtls_val['transaction_tax'];
			$revenue_share = $dtls_val['value']*100;
			$data2[] = array($dtls_val['cd_order_no'],$dtls_val['txn_orginiator_email'],$dtls_val['transaction_type'],
				$dtls_val['transaction_date'],$dtls_val['package_name'],$dtls_val['unit_price'],$price_benefit,
				$net_revenue, $revenue_share,$dtls_val['txn_amount']
				);
			$total = $total + $dtls_val['txn_amount'];
		}

	}

}

$grand_total = $total + $get_invoice_dtl_res['invoice_tax'];
$html2 = '<br><br><br><br><br><br><br><br><br><br><div>
<div>PAN: '.$get_invoice_dtl_res['user_pan'].'                                                                                 Sub Total: Rs.'.$total.'</div><br>
<div>Service Tax No: '.$get_invoice_dtl_res['user_st_no'].'                                                                    Service Tax including cess: '.$get_invoice_dtl_res['invoice_tax'].'<div><br>
<div>                                                                                				                           Grand Total: Rs. '.$grand_total.'</div><br>
</div>';
//print_r($html2);die;



// First page
$pdf->AddPage();
$pdf->SetFont('Arial','',10);
//$pdf->Write(5,"To find out what's new in this tutorial, click ");
$pdf->SetFont('','U');
$link = $pdf->AddLink();
//$pdf->Write(5,'here',$link);
$pdf->SetFont('');
// Second page
//$pdf->AddPage();
$pdf->SetLink($link);
//$pdf->Image('logo.png',10,12,30,0,'','http://www.fpdf.org');
// $image1 = "http://collegedoors.com/pages/img/logo.jpg";
// $pdf->Image($image1, 150, 10, 40, 30);
$pdf->SetLeftMargin(10);
$pdf->SetFont('Arial','',10);
$pdf->WriteHTML($html);

$pdf->SetLeftMargin(10);
$pdf->SetFont('Arial','',10);


// $data[0] = array('234', 'ravitiwari0701@gmail.com', 'Test type', '2/12/2014','Test Package Name','39',
// 	'40','50',
// '50%','5000'
// );
// $data[1] = array('234', "Bill To:"."\n"."Person's Address", 'Test type', '2/12/2014','Test Package Name','39',
// 	'40','50',
// '50%','5000'
// );
// Data loading
//$data = $pdf->LoadData('countries.txt');

//$pdf->AddPage();
$pdf->FancyTable($header,$data);

if($count_other_dtls > 4){
	$pdf->AddPage();
	$pdf->FancyTable($header,$data2);
}
//$pdf->Output();

$pdf->SetLeftMargin(10);
$pdf->SetFont('Arial','',10);
$pdf->WriteHTML($html2);


?>