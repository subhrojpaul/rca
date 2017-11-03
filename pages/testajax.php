<html>
<head>
</head>
<body>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="../assets/js/rcautils.js"></script>
<script type="text/javascript">
	function success(data,ip) {
		console.log(data);
		console.log(ip);
		console.log(data.message);
	}
	/*function error(jqXHR,textStatus,errorThrown) {
		console.log(textStatus,errorThrown);
	}
	*/
	ajax({method:'TEST'},success);
	ajax({method:'TEST1'},success);
	
</script>
</body>
</html>