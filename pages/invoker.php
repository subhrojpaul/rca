<?php
$result= json_decode(exec('/usr/local/bin/phantomjs /var/www/html/ocr/rca/pages/invoker.js'),true);
echo '<pre>';
print_r($result);
echo '</pre>';
mail("subhrojpaul@gmail.com","new lot created","hi new lot created");
?>