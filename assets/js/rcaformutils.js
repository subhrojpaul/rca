
var RCAFormValidator=function() {
}

RCAFormValidator.validate=function($sel,rulesJSON,customFunctions) {
	var valid=true, elements=[],messages={};
	//return {valid:valid,elements:elements,messages:messages};
	rulesJSON = rulesJSON || {};
	var err=function(eName,message) {
		valid=false;
		if (elements.indexOf(eName)<0) {
			elements.push(eName);
			messages[eName]=[message];
		} else {
			this.messages[eName].push(message);
		}
	}
	$sel.each(function(){
		var $e=$(this), eName=$e.attr('name'), eValue=$e.nvl();
		console.log($e, eValue);
		//required validation
		if ($e.prop('required') && eValue=='') err(eName,'is required');
	
		if(eValue!='' && rulesJSON.hasOwnProperty(eName)) {
			var rule=rulesJSON[eName], res;
			if (rule.type=='date') res=RCAFormValidator.validateDate(eValue,rule);
			if (rule.type=='numericonly') res=RCAFormValidator.validateNumericOnly(eValue,rule);
			if (rule.type=='alphaonly') res=RCAFormValidator.validateAlphaOnly(eValue,rule);
			if (rule.type=='alphanumeric') res=RCAFormValidator.validateAlphaNumeric(eValue,rule);
			res.messages.forEach(function(m){
				err(eName,m);
			});
		}
		if (customFunctions.hasOwnProperty(eName)) {
			var cf=customFunctions[eName];
			if (typeof cf.constructor!==Array) cf=[cf];
			cf.forEach(function(f){
				var x=f();
				if (typeof x==='string') err(eName,x);
			});
		}
	});
	return {valid:valid,elements:elements,messages:messages};
}
RCAFormValidator.validateNumericOnly=function(eValue,rule) {
	var valid=true, messages=[];
	var reg=/^\d+$/;
	if (!reg.test(eValue)) {
		valid=false;
		messages.push(rule.hasOwnProperty('message')?rule.message:'should be number only')
	} else {
		if (rule.hasOwnProperty('numDigits') && rule.numDigits!=eValue.length) {
			valid=false;
			messages.push(rule.hasOwnProperty('message')?rule.message:'should be '+rule.numDigits+' digits')
		}
	}
	return {valid:valid,messages:messages};
}
RCAFormValidator.validateAlphaOnly=function(eValue,rule) {
	console.log('validateAlphaOnly');
	var valid=true, messages=[];
	var reg=/^[a-zA-Z\s]+$/;
	if (!reg.test(eValue)) {
		valid=false;
		messages.push(rule.hasOwnProperty('message')?rule.message:'should be letters only')
	} 
	return {valid:valid,messages:messages};
}
RCAFormValidator.validateAlphaNumeric=function(eValue,rule) {
	var valid=true, messages=[];
	var reg=/^[a-zA-Z0-9\s]+$/;
	if (!reg.test(eValue)) {
		valid=false;
		messages.push(rule.hasOwnProperty('message')?rule.message:'should be letters and/or numbers only')
	} 
	return {valid:valid,messages:messages};
}
RCAFormValidator.validateDate=function(eValue,rule) {
	var valid=true, messages=[], dateFormat=rule.dateFormat||'DD/MM/YYYY';
	var getBaseValue=function(ruleComp) {
		if (ruleComp.hasOwnProperty('base')){
			var base=ruleComp.base, baseElem=$('input[name="'+base+'"]');
			return (baseElem.length>0?moment(baseElem.val(),dateFormat):moment());
		}
		return moment();
	}
	var getOffset=function(ruleComp) {
		var offset={Y:0,M:0,D:0};
		if (ruleComp.hasOwnProperty('offset')){
			ruleComp.offset.split(',').forEach(function(p) {
				offset[p.substring(0,1)]=Number(p.substring(1));
			});
		}
		return offset;
	}
	if (!moment(eValue,dateFormat).isValid()) {
		valid=false;
		messages.push('Invalid Date Format');
		return {valid:valid,messages:messages};
	}
	if (rule.hasOwnProperty('maxDate')) {
		var md=rule.maxDate, baseDate=getBaseValue(md), offset=getOffset(md), compDate=baseDate.add({d:offset.D,M:offset.M,y:offset.Y});
		if (moment(eValue,dateFormat)>compDate) {
			valid=false;
			messages.push(md.hasOwnProperty('message')?md.message:'should not be greater than '+compDate.format(dateFormat));
		}
	}
	if (rule.hasOwnProperty('minDate')) {
		var md=rule['minDate'], baseDate=getBaseValue(md), offset=getOffset(md), compDate=baseDate.add({d:offset.D,M:offset.M,y:offset.Y});
		if (moment(eValue,dateFormat)<compDate) {
			valid=false;
			messages.push(md.hasOwnProperty('message')?md.message:'should not be less than '+compDate.format(dateFormat))
		}
	}
	return {valid:valid,messages:messages};
}
