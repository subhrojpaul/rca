
	var json_arr=[];
	//current
	json_arr["VISA"]={
		"visa" : { 
			"visa-types" : {
				"type" : "dropdown" , 
				"values" :[
					{"visa_type_code": "14Day", "visa_type_name" : "14 Day Tourist"}, 
					{"visa_type_code": "30Day", "visa_type_name" : "30 Day Tourist"}, 
					{"visa_type_code": "90Day", "visa_type_name" : "90 Day Tourist"}, 
					{"visa_type_code": "96Hr", "visa_type_name" : "96 Hour Transit"} 
				], 
				"priced" : "Yes"
			}
		}
	};
	json_arr["MNA"]={
		"m_n_a" : {
			"m_n_a-types" : {
				"type" : "dropdown" , 
				"values" :[
					{"m_n_a_type_code": "Premium", "m_n_a_type_name" : "Premium"}, 
					{"m_n_a_type_code": "Standard", "m_n_a_type_name" : "Standard"} 
				], 
				"priced" : "Yes"
			}, 
			"flower_service" : { "type" : "checkbox", "name" : "Flower Bouquet", "priced" : "Yes"}
			"wheelchair_service" : { "type" : "checkbox", "name" : "Wheelchair", "priced" : "No"} 
		}
	};

	//Subhro Proposed
	json_arr["VISA"]={
		"types" : {
			"type" : "dropdown" , 
			"values" :[
				{"type_code": "14Day", "type_name" : "14 Day Tourist"}, 
				{"type_code": "30Day", "type_name" : "30 Day Tourist"}, 
				{"type_code": "90Day", "type_name" : "90 Day Tourist"}, 
				{"type_code": "96Hr", "type_name" : "96 Hour Transit"} 
			], 
			"priced" : "Yes"
		}
	};
	json_arr["MNA"]= {
		"types" : {
			"type" : "dropdown" , 
			"values" :[
				{"type_code": "Premium", "type_name" : "Premium"}, 
				{"type_code": "Standard", "type_name" : "Standard"} 
			], 
			"priced" : "Yes"
		},
		"flower_service" : { "type" : "checkbox", "name" : "Flower Bouquet", "priced" : "Yes" },
		"wheelchair_service" : { "type" : "checkbox", "name" : "Wheelchair", "priced" : "No" }
	};