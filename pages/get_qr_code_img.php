<?php
/*
this file will be in pages/handlers
qrlib will be in assets utils, so will all the other stuff as well.
relative to this page, there will be
../qr_temp directory
*/    
    //set it to writable location, a place for temp generated PNG files
    //$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    //echo "we are inside get_qr_code_img.php.. 1", "<br>";
    $PNG_TEMP_DIR = "../qr_temp/";
    //html PNG location prefix
    $PNG_WEB_DIR = 'temp/';
    $PNG_WEB_DIR = '../qr_temp/';

    //include "qrlib.php";
    include "../assets/utils/phpqrlib/qrlib.php";
    
    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
    
    
    $filename = $PNG_TEMP_DIR.'test.png';
    
    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'L';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    

    $matrixPointSize = 4;
    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);
/*
    echo "get_qr_code_img.php.. 2", "<br>";
    echo "<pre>";
    print_r($_REQUEST);
    echo "</pre>";
*/
    if (isset($_REQUEST['data'])) { 
    
        //it's very important!
        if (trim($_REQUEST['data']) == '') die('data cannot be empty!');
        // user data
        $filename = $PNG_TEMP_DIR.'test'.md5($_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        QRcode::png($_REQUEST['data'], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        //echo "get_qr_code_img.php.. 3, qr is generated..", "<br>";
    } else {
        die("Must provide data to generate QR code");
    }    
        
    //display generated file
    //echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" />';  
    header("Content-type: image/png");
    readfile($PNG_WEB_DIR.basename($filename));
?>

    