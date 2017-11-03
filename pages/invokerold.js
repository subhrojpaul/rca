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
	r.data=JSON.parse(msg);
};

var steps = [
	function() {
		r.message+='-->'+'step1';
		page.open("http://dev.redcarpetassist.com/ocr/rca/pages/sp1.php");
	},
	function() {
		page.evaluate(function() {
			document.getElementById("f1").submit();
		});
	}, 
	function() {
		page.evaluate(function() {
			var response={};
			response.data=document.querySelectorAll('body')[0].innerHTML;
			console.log(JSON.stringify(response));
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