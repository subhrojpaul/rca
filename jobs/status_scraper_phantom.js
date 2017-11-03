//urls and auth details
var ednrd={
	loginURL:"https://www.ednrd.ae/portal/pls/portal/INIMM_DB.DYN_PG_LINK.show?p_arg_names=_title&p_arg_values=YES",
	user:"ahlanrca",
	password:"Ahlan2017",
	loginCheckString:"Welcome&nbsp;AHLANRCA",
	appCheckURLPrefix:"https://www.ednrd.ae/portal/pls/portal/INIMM_DB.QUERY_RESULT_FOR_INQUIRYSINGLE.show?p_arg_names=app_id&p_arg_values=",
	appCheckURLSuffix:"&p_arg_names=spn&p_arg_values=8186"
}

//code do not change
var system = require('system');
var args = system.args;
var appIDs=args[1].split(',');


var page = new WebPage(), loginStepIdx = 0, loadInProgress = false, appIdx=0, loggedIn=false, appStepIdx=0;

var r={
	logMessage:[], errorMessage:[], data:{},
	log:function(msg) { this.logMessage.push(msg); return this; },
	showLog:function(){ console.log(this.logMessage.join('\n')) }
};

page.onLoadStarted = function() { 
	loadInProgress = true; 
	r.log('page load started '+stepIdx);
};
page.onLoadFinished = function() { 
	loadInProgress = false; 
	r.log('page load finished  '+stepIdx);
};

page.onConsoleMessage = function(msg) {
	var receivedMsg=JSON.parse(msg);
	if (receivedMsg.type=='LOGIN_SUCCESS') {
		r.log('Success: Login Success');
		loggedIn=true;
	}
	if (receivedMsg.type=='LOGIN_FAILURE') {
		r.log('Error: Login Failure');
		r.showLog();
		phantom.exit();
	}
	if (receivedMsg.type=='log') r.log(receivedMsg.payload);
	if (receivedMsg.type=='data') {
		var payload=receivedMsg.payload;
		r.data[payload.name]=payload.value;
	}
};

var loginSteps=[
	//open login page
	function() {
		r.log('Initiaing Login');
		page.open(ednrd.loginURL);
	},
	//submit login page
	function() {
		page.evaluate(function(ednrd) {
			console.log(JSON.stringify({type:'log',payload:'Login Page Loaded'}));
			inputs=document.querySelectorAll('form[name="frm"] input');
			inputs[2].value=ednrd.user;
			inputs[3].value=ednrd.password;
			inputs[4].checked = true;
			inputs[5].click();
		},ednrd);
	},
	//login completed
	function() {
		page.evaluate(function(ednrd) {
			docString=document.querySelectorAll('body')[0].innerHTML;
			if (docString.indexOf(ednrd.loginCheckString)>=0) console.log(JSON.stringify({type:'LOGIN_SUCCESS'}));
			else console.log(JSON.stringify({type:'LOGIN_FAILURE'}));
			//console.log(JSON.stringify({type:'log',payload:document.querySelectorAll('body')[0].innerHTML}));
		},ednrd);
	}
];


var appCheckSteps=[
	//open app check page
	function(appId) {
		r.log('Initiaing app check with url '+ednrd.appCheckURLPrefix+appId+ednrd.appCheckURLSuffix);
		page.open(ednrd.appCheckURLPrefix+appId+ednrd.appCheckURLSuffix);
	},
	//record the output against app id
	function (appId){
		page.evaluate(function(appId) {
			nr="No row returned";
			doc=document.querySelectorAll('.bgreglgrey')[0].innerHTML;
			if (doc.indexOf(nr)>0) appStatus="Invalid App Id";
			else appStatus=document.querySelectorAll('.bgreglgrey table')[1].querySelectorAll('tr')[1].querySelectorAll('td')[11].querySelectorAll('a')[0].innerHTML;
			console.log(JSON.stringify({
				type:'data',
				payload:{
					name:appId, 
					value:appStatus
				}
			}));
		},appId);
	}
];

interval = setInterval(
	function() {
		if (!loggedIn) {
			if (!loadInProgress && loginStepIdx<3) {	
				//console.log('calling login step '+loginStepIdx);
				loginSteps[loginStepIdx]();
				loginStepIdx++;
			}
		}
		
		if (loggedIn) {
			if (!loadInProgress) {
				if (appStepIdx>=2) {
					appStepIdx=0;
					appIdx++;
				}
				if (appIdx<appIDs.length) {
					//console.log('calling app step '+appStepIdx+' appIdx '+appIdx);
					appId=appIDs[appIdx];
					appCheckSteps[appStepIdx](appId);
					appStepIdx++;
				} else {
					//r.showLog();
					console.log(JSON.stringify(r.data));
					phantom.exit();
				}
			}
		}
	}, 
	200
);