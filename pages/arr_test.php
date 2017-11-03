<?php

$str = "../uploads/RCA01-27-Feb-2017-175/VikasPPFrontEdited_58b3c90f96f74.jpg";
$strpos1 = strpos($str, "/", -1);
var_dump($strpos1);
$strpos1 = strrpos($str, "/", -1);
var_dump($strpos1);
echo $str, "<br>";
echo substr($str, 0, $strpos1);
echo "<br>";

$a1[]=array("subject"=>"P", "chapter"=>"c1", "qid" => rand(1,100));
$a1[]=array("subject"=>"P", "chapter"=>"c2", "qid" => rand(1,100));
$a1[]=array("subject"=>"P", "chapter"=>"c3", "qid" => rand(1,100));
$a1[]=array("subject"=>"P", "chapter"=>"c4", "qid" => rand(1,100));
$a1[]=array("subject"=>"C", "chapter"=>"c1", "qid" => rand(1,100));
$a1[]=array("subject"=>"C", "chapter"=>"c2", "qid" => rand(1,100));
$a1[]=array("subject"=>"C", "chapter"=>"c3", "qid" => rand(1,100));
$a1[]=array("subject"=>"C", "chapter"=>"c4", "qid" => rand(1,100));
$a1[]=array("subject"=>"C", "chapter"=>"c5", "qid" => rand(1,100));
$a1[]=array("subject"=>"M", "chapter"=>"c1", "qid" => rand(1,100));
$a1[]=array("subject"=>"M", "chapter"=>"c2", "qid" => rand(1,100));
$a1[]=array("subject"=>"M", "chapter"=>"c3", "qid" => rand(1,100));
$a1[]=array("subject"=>"M", "chapter"=>"c4", "qid" => rand(1,100));
$a1[]=array("subject"=>"M", "chapter"=>"c5", "qid" => rand(1,100));
echo "<pre>";
print_r($a1);
/*
echo "\n", "Find Chemistry ", "\n";
$a2 = array_keys($a1["subject"], "C", true);
print_r($a2);
array_splice($a1, 3,2);
echo "spliced array..", "<br>";
print_r($a1);
echo "unset the new element 5, chapter value" , "<br>";
unset($a1[5]["chapter"]);
print_r($a1);
*/

$x = array("P" => 1, "C" => 3, "M" => 2);
$a2 = $a1;
shuffle($a2);
$a3=$a2;
usort($a3, "cmp");
echo "<table>";
echo "<tr>";
echo "<th>#</th>";
echo "<th>Qid</th>";
echo "<th>Sub</th>";
echo "<th>Chap</th>";
echo "<th>Qid</th>";
echo "<th>Sub</th>";
echo "<th>Chap</th>";
echo "<th>Qid</th>";
echo "<th>Sub</th>";
echo "<th>Chap</th>";
echo "</tr>";
for($i=0;$i<count($a1);$i++){

	echo "<tr>";
	echo "<td>$i</td>";
	echo "<td>", $a1[$i]["qid"], "</td>";
	echo "<td>", $a1[$i]["subject"], "</td>";
	echo "<td>", $a1[$i]["chapter"], "</td>";
	echo "<td>", $a2[$i]["qid"], "</td>";
	echo "<td>", $a2[$i]["subject"], "</td>";
	echo "<td>", $a2[$i]["chapter"], "</td>";
	echo "<td>", $a3[$i]["qid"], "</td>";
	echo "<td>", $a3[$i]["subject"], "</td>";
	echo "<td>", $a3[$i]["chapter"], "</td>";
	echo "</tr>";
}
echo "</table>";
function cmp($a, $b){
	global $x;
	return($x[$a["subject"]] - $x[$b["subject"]]);

}


echo "--- testing i array partial ----", "<br>";
$words_array = array("love", "sex", "dhoka");
$input_string1 = "This string contains love";
in_array_check($words_array, $input_string1);
$input_string1 = "This string contains love and dhoka ";
in_array_check($words_array, $input_string1);

$input_string1 = "This string contains crap ";
in_array_check($words_array, $input_string1);
$input_string1 = "love and sex sometimes leads to dhoka ";
in_array_check($words_array, $input_string1);
$input_string1 = "Love and dhoka leads to dhoka ";
in_array_check($words_array, $input_string1);
$input_string1 = "LOVE and dhoka leads to dhoka ";
in_array_check($words_array, $input_string1);
$input_string1 = "sexx sometimes leads to more xes ";
in_array_check($words_array, $input_string1);
function in_array_check($p_words_array, $p_input_string){
	echo "input string is: ", $p_input_string, "<br>";
	echo "words array: ";
	print_r($p_words_array);
/*
	if(in_array($p_input_string, $p_words_array, true)) {
		echo "In array - looking for input string within words array was TRUE";
	} else {
		echo "In array - looking for input string within words array was FALSE";
	}
	echo "<br>";
	echo "Now going to look for words in the string one by one", "<br>";
*/
	foreach($p_words_array as $key => $word){
		//if(in_array($word, $p_input_string, true)) {
		$found = false;
		if(strpos($p_input_string, $word) === false) {
			//echo "In array - looking for $word in the  input string was FALSE";
		} else {
			//echo "In array - looking for $word in the  input string was TRUE";
			$found = true;
			break;
		}
		echo "<br>";

	}
	echo "The input sting ", $found?"had a":"did not have any", " banned word..", "<br>";
}
?>
