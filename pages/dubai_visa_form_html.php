<?php 
	if(empty($md_class)) $md_class = "col-md-6"; 
?>
<div class="form-group <?php echo $md_class; ?>">
	<label>PASSPORT NO<span class="req">*</span></label>
	<input type="text" class="form-control" name="passport-no" required>
</div>
<div class="form-group display-all <?php echo $md_class; ?>" >
	<label >PASSPORT TYPE<span class="req">*</span></label></label>
	<!--<input type="text" class="form-control" name="passport-type" >-->
	<select class="form-control" name="passport-type" required>
		<?php 
		$passport_type_res = get_passport_types_list($dbh);
		foreach ($passport_type_res as $key => $passport_type) {
			echo "<option>".$passport_type["passport_type_name"]."</option>", "<br>";
		}
		?>
	</select>
</div>
<div class="form-group display-all <?php echo $md_class; ?>" >
	<label>SURNAME<span class="req">*</span></label>
	<input type="text" class="form-control" name="surname" required>
</div>
<div class="form-group display-all <?php echo $md_class; ?>" >
	<label >GIVEN NAMES<span class="req">*</span></label>
	<input type="text" class="form-control" name="given-names" required>
	</div>
<div class="form-group display-all <?php echo $md_class; ?>" >
	<label >NATIONALITY<span class="req">*</span></label>
	<input type="text" class="form-control" name="nationality" required>
</div>
<div class="form-group display-all <?php echo $md_class; ?>" >
	<!--<label >GENDER<span class="req">*</span></label>
	<select class="form-control" name="sex" required>-->
	<label >GENDER<span class="req">*</span></label>
	<select class="form-control" name="sex" required>
		<option value="">Select Gender</option>
		<option value="Male">Male</option>
		<option value="Female">Female</option>
		<option value="Other">Other</option>
	</select>
</div>
<div class="form-group display-all <?php echo $md_class; ?>" >
	<!--<label >DATE OF BIRTH<span class="req">*</span></label>
	<input type="text" class="form-control" name="date-of-birth" required>-->
	<label >DATE OF BIRTH<span class="req">*</span></label>
	<input type="text" class="form-control" name="date-of-birth" required>
</div>
<div class="form-group display-all <?php echo $md_class; ?>" >
	<label >PLACE OF BIRTH<span class="req">*</span></label>
	<input type="text" class="form-control" name="place-of-birth" required></div>
<div class="form-group display-none <?php echo $md_class; ?>" >
	<label >PLACE OF ISSUE<span class="req">*</span></label>
	<input type="text" class="form-control" name="place-of-issue" required>
</div>
<div class="form-group display-all <?php echo $md_class; ?>" >
	<label >DATE OF ISSUE<span class="req">*</span></label>
	<input type="text" class="form-control" name="date-of-issue" required>
</div>
<div class="form-group display-all <?php echo $md_class; ?>" >
	<!--<label >DATE OF EXPIRY<span class="req">*</span></label>
	<input type="text" class="form-control" name="date-of-expiry" required>-->
	<label >DATE OF EXPIRY<span class="req">*</span></label>
	<input type="text" class="form-control" name="date-of-expiry" required>
</div>
<div class="form-group display-all <?php echo $md_class; ?>" >
	<label >MARITAL STATUS<span class="req">*</span></label>
	<!--<input type="text" class="form-control" name="marital-status" >-->
<!--
	<select class="form-control" name="marital-status">
		<option value="S">Single</option>
		<option value="M">Married</option>
		<option value="D">Divorced</option>
	</select>
-->
	<select class="form-control" name="marital-status" required>
		<?php 
		$marital_sts_res = get_marital_status_list($dbh);
		foreach ($marital_sts_res as $key => $marital_sts) {
			echo "<option>".$marital_sts["ednrd_marital_status_name"]."</option>", "<br>";
		}
		?>
	</select>
</div>
<div class="form-group display-regular-visa <?php echo $md_class; ?>" >
	<label >RELIGION<span class="req">*</span></label>
	<!--<input type="text" class="form-control" name="religion" >-->
	<select class="form-control" name="religion" required>
        <option value="">Select Religion</option>
		<?php 
		$rel_res = get_religion_list($dbh);
		foreach ($rel_res as $key => $rel) {
			echo "<option>".$rel["ednrd_religion_name"]."</option>", "<br>";
		}
		?>
	</select>
</div>

<div class="form-group display-regular-visa <?php echo $md_class; ?>" >
	<label >LANGUAGES SPOKEN<span class="req">*</span></label>
	<!--<input type="text" class="form-control" name="religion" >-->
	<select class="form-control" name="lang-spoken" required>
		<option value="">Select Language</option>
		<?php 
		$lang_res = get_language_list($dbh);
		foreach ($lang_res as $key => $lang) {
			echo "<option value=\"".$lang["ednrd_lang_code"]."\">".$lang["ednrd_lang_name"]."</option>", "<br>";
		}
		?>
	</select>
</div>

<div class="form-group display-all <?php echo $md_class; ?>" >
	<label >PROFESSION<span class="req">*</span></label>
	<select class="form-control" name="profession" required data-placeholder="Select Profession">
		<option value="">Select Profession</option>
		<?php 
		$res = get_profession_list($dbh);
		foreach ($res as $key => $prof) {
			echo '<option value="'.$prof["ednrd_profession_code"].'">'.$prof["ednrd_profession_name"]."</option>", "<br>";
		}
		?>
	</select>
</div>

<div class="form-group display-regualar-visa <?php echo $md_class; ?>" >
	<label >TELEPHONE<span class="req">*</span></label>
	<input type="text" class="form-control" name="telephone" required>
</div>
<div class="form-group display-all <?php echo $md_class; ?>" >
	<label >BIRTH COUNTRY<span class="req">*</span></label>
	<!--<input type="text" class="form-control" name="birth-country" >-->
	<select class="form-control" name="birth-country" required>
		<?php 
		if(empty($ctry_res)) $ctry_res = get_country_list($dbh);
		foreach ($ctry_res as $key => $country) {
			echo "<option>".$country["country_name"]."</option>", "<br>";
		}
		?>
	</select>
</div>
<div class="form-group display-all <?php echo $md_class; ?>" >
	<label >PASSPORT ISSUING COUNTRY<span class="req">*</span></label>
	<!--<input type="text" class="form-control" name="birth-country" >-->
	<select class="form-control" name="passport-issuing-country" required>
		<?php 
		if(empty($ctry_res)) $ctry_res = get_country_list($dbh);
		foreach ($ctry_res as $key => $country) {
			echo "<option>".$country["country_name"]."</option>", "<br>";
		}
		?>
	</select>
</div>
<div class="form-group display-regular-visa <?php echo $md_class; ?>" >
	<!--<label >FATHERS NAME<span class="req">*</span><span class="req">*</span></label>
	<input type="text" class="form-control" name="fathers-name" required>-->
	<label >FATHERS NAME<span class="req">*</span></label>
	<input type="text" class="form-control" name="fathers-name" required>
</div>
<div class="form-group display-regular-visa <?php echo $md_class; ?>" >
	<label >MOTHERS NAME<span class="req">*</span></label>
	<input type="text" class="form-control" name="mothers-name" required>
</div>
<div class="form-group display-regular-visa <?php echo $md_class; ?>" >
	<label >HUSBANDS NAME</label>
	<input type="text" class="form-control" name="spouses-name">
</div>
<div class="form-group display-regular-visa <?php echo $md_class; ?>" >
	<label >ADDRESS LINE1<span class="req">*</span></label>
	<input type="text" class="form-control" name="address-line1" required>
</div>
<div class="form-group display-regular-visa <?php echo $md_class; ?>" >
	<label >ADDRESS LINE2<span class="req">*</span></label>
	<input type="text" class="form-control" name="address-line2" required>
</div>
<div class="form-group display-all <?php echo $md_class; ?>" >
	<label >CITY<span class="req">*</span></label>
	<input type="text" class="form-control" name="city" required>
</div>
<div class="form-group display-all <?php echo $md_class; ?>" >
	<label >COUNTRY<span class="req">*</span></label>
	<!--<input type="text" class="form-control" name="country" >-->
	<select class="form-control" name="country" required>
		<?php 
		if(empty($ctry_res)) $ctry_res = get_country_list($dbh);
		foreach ($ctry_res as $key => $country) {
			echo "<option>".$country["country_name"]."</option>", "<br>";
		}
		?>
	</select>
</div>
<div class="form-group display-regular-visa <?php echo $md_class; ?>" >
	<label >ADDRESS LINE3<span class="req">*</span></label>
	<input type="text" class="form-control" name="address-line3" required>
</div>

<?php
if(($form_visa_type == "96HR") || ($form_visa_type == "ALL")) {
	?>
<!--	
	<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
		<label >Arrival Airlines<span class="req">*</span></label>
		<input type="text" class="form-control" name="arr-airline" >
	</div>
-->
<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
	<label >Arrival Airlines<span class="req">*</span></label>
	<select class="form-control" name="arr-airline" required>
		<option value="">Please Select</option>
		<?php 
		$res = get_airline_list($dbh);
		foreach ($res as $key => $airl) {
			echo '<option value="'.$airl["ednrd_airline_code"].'">'.$airl["ednrd_airline_name"]."</option>", "<br>";
		}
		?>
	</select>
</div>	
	<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
		<label >Arrival Flight No<span class="req">*</span></label>
		<input type="text" class="form-control" name="arr-flight-no" required>
	</div>
<!--
	<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
		<label >Arrival Coming from<span class="req">*</span></label>
		<input type="text" class="form-control" name="arr-coming-from" >
	</div>
-->
	<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
		<label >Arrival Coming From<span class="req">*</span></label>
		<select class="form-control" name="arr-coming-from" required>
		<option value="">Please Select</option>
			<?php 
			$res = get_airport_list($dbh);
			foreach ($res as $key => $airp) {
				echo '<option value="'.$airp["ednrd_airport_code"].'">'.$airp["ednrd_airport_name"]."</option>", "<br>";
			}
			?>
		</select>
	</div>
	<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
		<label >Arrival Date<span class="req">*</span></label>
		<input type="text" class="form-control" name="arr-date" required>
	</div>

	<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
		<label >Arrival Time (Hour)<span class="req">*</span></label>
		<input type="text" class="form-control" name="arr-time-hrs" required>
	</div>

	<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
		<label >Arrival Time (Min)<span class="req">*</span></label>
		<input type="text" class="form-control" name="arr-time-min" required>
	</div>

<!--
	<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
		<label >Departure Airlines<span class="req">*</span></label>
		<input type="text" class="form-control" name="dep-airline" >
	</div>
-->

<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
	<label >Departure Airlines<span class="req">*</span></label>
	<select class="form-control" name="dep-airline" required>
	<option value="">Please Select</option>
		<?php 
		$res = get_airline_list($dbh);
		foreach ($res as $key => $airl) {
			echo '<option value="'.$airl["ednrd_airline_code"].'">'.$airl["ednrd_airline_name"]."</option>", "<br>";
		}
		?>
	</select>
</div>

	<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
		<label >Departure Flight No<span class="req">*</span></label>
		<input type="text" class="form-control" name="dep-flight-no" required>
	</div>
<!--
	<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
		<label >Departure Leaving To<span class="req">*</span></label>
		<input type="text" class="form-control" name="dep-leaving-to" >
	</div>
-->
	<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
		<label >Depart Leaving To<span class="req">*</span></label>
		<select class="form-control" name="dep-leaving-to" required>
		<option value="">Please Select</option>
			<?php 
			$res = get_airport_list($dbh);
			foreach ($res as $key => $airp) {
				echo '<option value="'.$airp["ednrd_airport_code"].'">'.$airp["ednrd_airport_name"]."</option>", "<br>";
			}
			?>
		</select>
	</div>	
	<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
		<label >Departure Date<span class="req">*</span></label>
		<input type="text" class="form-control" name="dep-date" required>
	</div>

	<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
		<label >Departure Time (Hour)<span class="req">*</span></label>
		<input type="text" class="form-control" name="dep-time-hr" required>
	</div>

	<div class="form-group display-96hr-visa <?php echo $md_class; ?>" >
		<label >Departure Time (Min)<span class="req">*</span></label>
		<input type="text" class="form-control" name="dep-tim-min" required>
	</div>
	<?php
}
?>
