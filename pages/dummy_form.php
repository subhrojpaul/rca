<form>
<?php
for ($i=0; $i < 50; $i++) { 
	echo $i, " : "
	?>
	<input name="<?php echo $i?>" type= "text"> <br>
	<?php
}
?>
</form>