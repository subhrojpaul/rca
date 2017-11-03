<?php

function renderFormMethod($method){
	if ($method=='') {
		echo ' method="post"';
	} 
	else {
		echo ' method="'.$method.'"';
	}
}

function renderFormAction($action){
	if ($action=='') {
		echo ' ';
	}
	else {
		echo ' action="'.$action.'"';
	}
}

function renderFormText($element) {

//echo "<div>";
//echo "render Form  Text Session var for: ", $element->name, " is: ", $_SESSION[''.$element->name];
//echo "<br>";
//echo "</div>";
	echo '<div class="control-group">'."\n";
	echo '<label class="control-label" for="'.$ename.'">'.$element->label;
	
	echo '</label>'."\n";
	echo '<div class="controls" >'."\n";
	echo ' <p>';
	if ($_SESSION[''.$element->name]!='') {
		echo $_SESSION[''.$element->name];
		$_SESSION[''.$element->name] = '';
	}
	echo '</p>'."\n";
	echo '</div>'."\n";
	echo '</div>'."\n";
}

function renderText($element) {
//echo "<div>";
//echo "render Text Session var for: ", $element->name, " is: ", $_SESSION[''.$element->name];
//echo "<br>";
//echo "</div>";
	echo '<div class="control-group">'."\n";
	echo '<label class="control-label" for="'.$ename.'">'.$element->label;
	
	if ($element->mandatory=='yes') {
		echo '<font color=red size=+1>*</font>';
	}
	
	echo '</label>'."\n";
	echo '<div class="controls">'."\n";
	echo '<input type="'.$element->type.'" class="input-'.$element->size.'"';
	echo ' id="'.$element->name.'" name="'.$element->name.'"';
// a quick fix for date format, used the placeholder
// our xml should have date type and we should have a seperate render
	if($element->placeholder == 'DD/MM/YYYY'){
		echo ' data-date-format="dd/mm/yyyy"';
	}
	if ($element->placeholder!='') {
		echo ' placeholder="'.$element->placeholder.'"';
	}
	if ($_SESSION[''.$element->name]!='') {
		echo ' value="'.$_SESSION[''.$element->name].'"';
		$_SESSION[''.$element->name] = '';
	}
	if ($element->readonly=='yes') {
		echo ' readonly="readonly"';
	}
	echo '>'."\n";
/*
	if ($element->addlnlink =='' ) {
		echo "\n";
	} else {
		echo '<a href="'.$element->addlnlink.'">'.$element->addlnlinktext.'</a>'."\n";
	}
*/
	if ($element->additional=='') {
		echo "\n";
	} else {
		echo '<br><font size=-1><i>'.$element->additional.'</i></font>'."\n";
	}
	echo '</div>'."\n";
	echo '</div>'."\n";
}

function renderTextArea($element) {

//echo "<div>";
//echo "render Text Area Session var for: ", $element->name, " is: ", $_SESSION[''.$element->name];
//echo "<br>";
//echo "</div>";
	echo '<div class="control-group">'."\n";
	echo '<label class="control-label" for="'.$ename.'">'.$element->label;
	
	if ($element->mandatory=='yes') {
		echo '<font color=red size=+1>*</font>';
	}
	
	echo '</label>'."\n";
	echo '<div class="controls">'."\n";
	echo '<textarea class="input-'.$element->size.'"';
	echo ' id="'.$element->name.'" name="'.$element->name.'"';

	echo ' rows="'.$element->rows.'" ';

	if ($element->placeholder!='') {
		echo ' placeholder="'.$element->placeholder.'"';
	}
	if ($_SESSION[''.$element->name]!='') {
		echo ' value="'.$_SESSION[''.$element->name].'"';
		$_SESSION[''.$element->name] = '';
	}
	if ($element->readonly=='yes') {
		echo ' readonly="readonly"';
	}
	echo '>'."\n";
	if ($element->additional=='') {
		echo "\n";
	} else {
		echo '<br><font size=-1><i>'.$element->additional.'</i></font>'."\n";
	}
	echo '</textarea>'."\n";
	echo '</div>'."\n";
	echo '</div>'."\n";
}

function renderTexteMail($element) {

//echo "<div>";
//echo "render Text  email Session var for: ", $element->name, " is: ", $_SESSION[''.$element->name];
//echo "<br>";
//echo "</div>";
	echo '<div class="control-group">'."\n";
	echo '<label class="control-label" for="'.$ename.'">'.$element->label;
	
	if ($element->mandatory=='yes') {
		echo '<font color=red size=+1>*</font>';
	}
	
	echo '</label>'."\n";
	echo '<div class="controls">'."\n";
	echo '<input type="'.$element->type.'" class="input-'.$element->size.'"';
	echo ' id="'.$element->name.'" name="'.$element->name.'"';
	if ($element->placeholder!='') {
		echo ' placeholder="'.$element->placeholder.'"';
	}
	if ($_SESSION[''.$element->name]!='') {
		echo ' value="'.$_SESSION[''.$element->name].'"';
		$_SESSION[''.$element->name] = '';
	}
	if ($element->readonly=='yes') {
		echo ' readonly="readonly"';
	}
	echo '>'."\n";
	if ($element->addlnlink =='' ) {
		echo "\n";
	} else {
		echo '<a href="'.$element->addlnlink.'">'.$element->addlnlinktext.'</a>'."\n";
	}
	if ($element->additional=='') {
		echo "\n";
	} else {
		echo '<br><font size=-1><i>'.$element->additional.'</i></font>'."\n";
	}
	echo '</div>'."\n";
	echo '</div>'."\n";
}

function renderCaptcha($element) {
	echo '<div class="control-group">'."\n";
	echo '<label class="control-label" >'.$element->label;
	echo '</label>'."\n";
	echo '<div class="controls" >'."\n";
	echo '<img src="'.$element->src.'" id="'.$element->name.'">';
	echo '</div>'."\n";
	echo '</div>'."\n";
}

function renderHidden($element) {
//echo "<div>";
//echo "render Hidden  Session var for: ", $element->name, " is: ", $_SESSION[''.$element->name];
//echo "<br>";
//echo "</div>";
	echo '<input type = "hidden" name="'.$element->name.'" id="'.$element->name.'"';
	if ($_SESSION[''.$element->name]!='') {
		echo ' value="'.$_SESSION[''.$element->name].'"';
		$_SESSION[''.$element->name] = '';
	} else {
		echo ' value="'.$element->value.'"';
	}
	echo ">";
}

function renderConstText($element) {
	echo '<div>'."\n";
	echo '<p>&nbsp;'.$element->label.'</p>';
	echo '</div>'."\n";
}

function renderAhref($element) {
	echo '<div>'."\n";
	echo '<p> <a href="'.$element->target.'">'.$element->label.'</a> </p>';
	echo '</div>'."\n";
}

function renderPwd($element) {
	echo '<div class="control-group">'."\n";
	echo '<label class="control-label" for="'.$element->name.'">'.$element->label;
	if ($element->mandatory=='yes') {
		echo '<font color=red size=+1>*</font>';
	}
	echo '</label>'."\n";
	echo '<div class="controls">'."\n";
	echo '<input type="'.$element->type.'" class="input-'.$element->size.'" id="'.$element->name.'" name="'.$element->name.'">'."\n";
	if ($element->forgetpwdlink =='' ) {
		echo "\n";
	} else {
		echo '<a href="'.$element->forgetpwdlink.'">Forgot Password?</a>'."\n";
	}
	if ($element->additional=='') {
		echo "\n";
	} else {
		echo '<br><font size=-1><i>'.$element->additional.'</i></font>'."\n";
	}
	echo '</div>'."\n";
	echo '</div>'."\n";
}

function renderSelect($element) {
//echo "<div>";
//echo "render select Session var for: ", $element->name, " is: ", $_SESSION[''.$element->name];
//echo "<br>";
//echo "</div>";
	echo '<div class="control-group">'."\n";
	
	echo '<label class="control-label" for="'.$element->name.'">'.$element->label;
	if ($element->mandatory=='yes') {
		echo '<font color=red size=+1>*</font>';
	}
	echo '</label>'."\n";
	echo '<div class="controls">'."\n";

	echo '<select class="input-'.$element->size.'" id="'.$element->name.'" name="'.$element->name.'"';
	if ($_SESSION[''.$element->name]!='') {
		echo ' value="'.$_SESSION[''.$element->name].'"';
		$curr_option = $_SESSION[''.$element->name];
		$_SESSION[''.$element->name] = '';
	} else {
		if ($element->selectedoption!='') {
			echo ' value="'.$element->selectedoption.'"';
			$curr_option = $element->selectedoption;
		}
	}
	echo '>'."\n";
	//$curr_option = 'Vietnam';
	foreach($element->option as $option) {
		if(''.$option == $curr_option){
			echo '<option selected>'.$option.' </option>'."\n"; 
		} else {
			echo '<option>'.$option.' </option>'."\n";
		}
	}
	echo '</select>'."\n";
	echo '</div>'."\n";
	echo '</div>'."\n";
}

function renderRadio($element) {
	
//echo "<div>";
//echo "render Radio Session var for: ", $element->name, " is: ", $_SESSION[''.$element->name];
//echo "<br>";
//echo "</div>";
	/*
	echo "<div>";
	echo "session variable: ", $_SESSION[''.$element->name];
	echo "</div>";
	*/

	echo '<div class="control-group">'."\n";
	echo '<label class="control-label" for="'.$element->name.'">'.$element->label;
	if ($element->mandatory=='yes') {
		echo '<font color=red size=+1>*</font>';
	}
	echo '</label>'."\n";
	echo '<div class="btn-group" data-toggle-name="'.$element->name.'" data-toggle="buttons-radio">'."\n";
	foreach($element->option as $option) {
		echo '<button type="button" value="'.$option.'" id="'.$option.'"';
		//$opt_str .= $option;
		if ($_SESSION[''.$element->name]!='') {
			//$sess_var = 'Y';
			if ($option==$_SESSION[''.$element->name]) {
				//$sess_var_matched = 'Y';
				echo ' class="btn active"';
			} else {
				echo ' class="btn"';
			}
		} else {
			//$non_sess_var = 'Y';
			if ($option.value==$element->selectedoption.value) {
				//$non_sess_var_matched = 'Y';
				echo ' class="btn active"';
			} else {
				echo ' class="btn"';
			}
		}
		echo ' data-toggle="button">'.$option.'</button>'."\n";
	}
	$_SESSION[''.$element->name] = '';
	echo '</div>'."\n";
	echo '</div>'."\n";
	/*
	echo "<div>";
	echo "process vars, sess_var: ", $sess_var, " sess matched: ", $sess_var_matched;
	echo "process vars, non_sess_var: ", $non_sess_var, " non sess matched: ", $non_sess_var_matched;
	echo " option str: ".$opt_str;
	echo "selected option: ", $element->selectedoption;
	echo "</div>";
	*/
}

function renderRadio2($element) {
	echo '<div class="control-group">'."\n";
	echo '<label class="control-label" >'.$element->label;
	if ($element->mandatory=='yes') {
		echo '<font color=red size=+1>*</font>';
	}
	echo '</label>'."\n";
	echo '<div class="controls" >'."\n";
	foreach($element->option as $option) {
		echo '<input type="radio" style="vertical-align: middle;margin: 0px;" name="'.$element->name.'" id="'.$option.'" value="'.$option.'"';
		if ($_SESSION[''.$element->name]!='') {
			if ($option.value==$_SESSION[''.$element->name]) {
				echo ' checked="checked"';
			}
			$_SESSION[''.$element->name] = '';
		} else {
			if ($option.value==$element->selectedoption.value) {
				echo ' checked="checked"';
			} 
		}
		//echo ' ><label style="display: inline; padding-left: 5px; text-align:bottom;" for="'.$option.'">'.$option.'</label>'."\n&nbsp;";
		echo '>'.$option."\n&nbsp;";
	}
	echo '</div>'."\n";
	echo '</div>'."\n";
}

function renderCheckBoxes($element) {
//echo "<div>";
//echo "render check box Session var for: ", $element->name, " is: ", $_SESSION[''.$element->name];
//echo "<br>";
//echo "</div>";
	echo '<div class="control-group">'."\n";
	echo '<label class="control-label" for="'.$element->name.'">'.$element->label.'</label>'."\n";
	echo '<div class="controls">'."\n";
	foreach($element->option as $option) {
		echo '<label class="checkbox"><input type="checkbox" name="'.$option->name.'">'.$option->label.'</label>';
	}
	echo '</div>'."\n";
	echo '</div>'."\n";
}

function renderDivider($element) {
//form legend
	echo '<div class="control-group">'."\n";
	echo '<legend class="sublegend">&nbsp;'.$element->label.'</small></legend>';
	echo '</div>'."\n";
}

function renderInputs($formxml) {
	//render each input element
	foreach($formxml->inputelements->children() as $element) {

		// render hidden variable
		if ($element->type=='hidden') renderHidden($element);
		// render line of text
		if ($element->type=='const_text') renderConstText($element);

		// render hyperlink
		if ($element->type=='ahref') renderAhref($element);
		//render text as input field
		if ($element->type=='form_text') renderFormText($element);
		//render images 
		if ($element->type=='captcha') renderCaptcha($element);
		
		//render text as input field
		if ($element->type=='text') renderText($element);

		//render text area as input field
		if ($element->type=='textarea') renderTextArea($element);

		//render email as input field
// still to do
		if ($element->type=='email') renderTexteMail($element);
		
		//render password as input field
		if ($element->type=='password') renderPwd($element);
		
		//render select 
		if ($element->type=='select') renderSelect($element);
		
		//render radio
		if ($element->type=='radio') renderRadio($element);
		
		//render checkboxes
		if ($element->type=='checkbox') renderCheckboxes($element);
		
		//render divider
		if ($element->type=='divider') renderDivider($element);

		//render file field
		if ($element->type=='file') renderFileButton($element);
		
	}
}


function renderButton($button) {
	
	//start button tag
	echo '<button ';
	
	if ($button->type=='submit') {
		echo 'type="'.$button->type.'"';
	} 
	
	//no action
	if ($button->action=='') {
		echo ' ';
	}
	//action url
	else {
		echo ' formaction="'.$button->action.'"';
	}
	
		//no action
	if ($button->id=='') {
		echo ' ';
	}
	//action url
	else {
		echo ' id="'.$button->id.'"';
	}

	//button style
	echo ' class="btn '.$button->subtype.'">';
	//button label
	echo $button->label;
	//close button
	echo '</button>'."\n";
	

}

function renderFileButton($button) {
	
	//start button tag
	echo '<div style="position:relative;">';
	echo '<a class=\'btn btn-primary\' href=\'javascript:;\'>';
	echo $button->label;
	echo '<input type="file"'; 
	echo 'style=\'position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;\' ';
	echo 'name="', $button->name, '" size="40"  onchange=\'$("#', $button->id, '").html($(this).val());\'';
	if ($button->multiple == 'Y') echo ' multiple>';
	else echo '>';
	echo '</a>';
	echo '<span class=\'label label-info\' id="', $button->id, '"></span>';
	echo '</div>';	
}


function renderActions($formxml){
	//render form actions
	//actions div
	echo '<div class="form-actions">'."\n";
	
	//action buttons
	foreach($formxml->actionbuttons->children() as $button) {
		//render button
		renderButton($button);
	}
	
	//finishing actions div
	echo '</div>'."\n";
}

function renderform($xmlformdef) {
	//Load the form xml
	$formxml=simplexml_load_file($xmlformdef);

	//start rendering the form
	echo '<form class="form-horizontal"';
	
	//formmethod
	renderFormMethod($formxml->method);

	//formaction
	renderFormAction($formxml->action);
	if(isset($formxml->hasfile)) {
		if($formxml->hasfile=='true') {
			echo ' enctype="multipart/form-data" ';
		}
	}
	
	//form name	
	echo ' name="'.$formxml->name.'" ';
	
	//form name	
	echo ' id="'.$formxml->name.'">'."\n";

	//form legend
	echo '<legend>&nbsp;'.$formxml->legend.'</legend>';
	
	//render the form fields
	echo '<fieldset>'."\n";

	//render each input element (text, password, select)
	renderInputs($formxml);
	
	//render actions
	renderActions($formxml);
	
	//finishing fieldset
	echo '</fieldset>'."\n";
	
	//finishing form
	echo '</form>'."\n";
}
?>
