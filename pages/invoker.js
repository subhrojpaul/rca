var system = require('system');
var args = system.args;
var page = new WebPage(), testindex = 0, loadInProgress = false;
var r={message:'',data:{}};
page.onLoadStarted = function() { 
	loadInProgress = true; 
	r.message+='-->'+'load start';
};
page.onLoadFinished = function() { 
	loadInProgress = false; 
	r.message+='-->'+'load finished';
};
page.onConsoleMessage = function(msg) {
	var receivedMsg=JSON.parse(msg);
	if (receivedMsg.type=='log') console.log(receivedMsg.payload);
	if (receivedMsg.type=='data') {
		var payload=receivedMsg.payload;
		r.data[payload.name]=payload.value;
	}
};

var steps = [
	function() {
		r.message+='-->'+'step1';
		page.open("https://www.ednrd.ae/portal/pls/portal/INIMM_DB.DYN_PG_LINK.show?p_arg_names=_title&p_arg_values=YES");
	},
	function() {
		page.evaluate(function() {
			//console.log(JSON.stringify({type:'log',payload:document.querySelectorAll('body')[0].innerHTML}));
			inputs=document.querySelectorAll('form[name="frm"] input');
			inputs[2].value="sjp";
			inputs[3].value="sjp";
			inputs[4].checked = true;
		});
	},
	function() {
		page.evaluate(function() {
			inputs=document.querySelectorAll('form[name="frm"] input');
			console.log(JSON.stringify({type:'log',payload:inputs[2].value}));
			inputs[5].click();
		});
	},	
	function() {
		page.evaluate(function() {
			console.log(JSON.stringify({type:'log',payload:document.querySelectorAll('body')[0].innerHTML}));
		});
	}
];
interval = setInterval(function() {
	if (!loadInProgress && typeof steps[testindex] == "function") {
		steps[testindex]();
		testindex++;
	}
	if (typeof steps[testindex] != "function") {
		console.log(JSON.stringify(r));
		phantom.exit();
	}
}, 50);