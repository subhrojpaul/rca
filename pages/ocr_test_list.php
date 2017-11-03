<?php
include "../assets/utils/fwdbutil.php";
$dbh = setupPDO();

if(isset($_POST['submit'])){
        $correcttext=$_POST['correcttext'];
        $correcttextid=$_POST['correcttextid'];
        
     
        foreach ($correcttext as $key => $value) {
            $updt_appl_qry = "update ocr_test_results
					 set correct_text_manual = ? where ocr_test_result_id = ?
				";
            $updt_appl_params = array($value, $correcttextid[$key]);
        
            runUpdate($dbh, $updt_appl_qry, $updt_appl_params);
            //echo $correcttextid[$key].'<br>';
            
        }
}


$ocr_list_qry = "select ocr_test_result_id, image_file_path, image_file_name, local_tess_ocr_text, google_ocr_text, ocr_space_text, correct_text_manual
					from ocr_test_results
				   where enabled = 'Y'
				";
$ocr_list_res = runQueryAllRows($dbh, $ocr_list_qry, array());
?>
<form name="frm" method="post">
    <table>
            <tr>
                    <td>#</td>
                    <td>Image</td>
                    <td>Local OCR</td>
                    <td>Google OCR</td>
                    <td>OCR Space</td>
                    <td>Correct text (Manual)</td>
            </tr>
            <?php
                foreach ($ocr_list_res as $key => $ocr_rec) {
            ?>
            
            <tr>
                    <td><?php echo $ocr_rec["ocr_test_result_id"]?></td>
                    <td><img src="<?php echo $ocr_rec["image_file_path"].$ocr_rec["image_file_name"]?>"></td>
                    <td><?php echo $ocr_rec["local_tess_ocr_text"]?></td>
                    <td><?php echo $ocr_rec["google_ocr_text"]?></td>
                    <td><?php echo $ocr_rec["ocr_space_text"]?></td>
                    <td><input type="text" name="correcttext[]" value="<?php echo $ocr_rec["correct_text_manual"]?>"><input type="hidden" name="correcttextid[]" value="<?php echo $ocr_rec["ocr_test_result_id"]?>"></td>
            </tr>
            <?php
                }
            ?>
            <tr><td colspan="6" align="right"><input type="submit" name="submit" value="Submit"></td></tr>
    </table>
</form>



