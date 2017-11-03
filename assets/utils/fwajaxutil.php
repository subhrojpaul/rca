<?php

class fwAjaxResponse {
	var $response=array("error"=>false,"message"=>"", "data"=>array());
	var $name;
	function er($msg) {
		$this->response["error"] = true;
		$this->response["message"] = $this->response["message"].$msg;
		return $this;
	}
	function ex() {
		echo json_encode($this->response);
		exit();
	}
	function data($name,$value){
		$this->response["data"][$name]=$value;
		return $this;
	}
	function msg($msg) {
		$this->response["message"] = $this->response["message"].$msg;
		return $this;
	}
}

?>