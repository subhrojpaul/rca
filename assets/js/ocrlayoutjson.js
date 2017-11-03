/*ocr related*/
var overlay_templates = [
	{
		template_id : "pp-p1",
		template_fields :
			[
				{	
					name:"passport-no",
					coords:{left:450,top:60,width:140,height:35},
					type:"alphanumeric",
					fieldType:"text"
				}, 
				{
					name:"surname",
					coords:{left:210,top:87,width:370, height:22},
					type:"alpha",
					fieldType:"text"
					
				}, 
				{
					name:"given-names",
					coords:{left:210,top:125,width:370, height:22},
					type:"alpha",
					fieldType:"text"
				}, 
				{
					name:"nationality",
					coords:{left:210,top:162,width:130, height:22},
					type:"alpha",
					fieldType:"select"
				},
				{
					name:"sex",
					coords:{left:360,top:162,width:60, height:22},
					type:"alpha",
					fieldType:"select",
					selectOptions:["M",,"F"]
				},
				{
					name:"date-of-birth",
					coords:{left:430,top:162,width:150, height:22},
					type:"date",
					fieldType:"text"
				},
				{
					name:"place-of-birth",
					coords:{left:250,top:197,width:300, height:22},
					type:"alpha",
					fieldType:"text"
				},
				{
					name:"place-of-issue",
					coords:{left:250,top:237,width:300, height:22},
					type:"alpha",
					fieldType:"text"
				},
				{
					name:"date-of-issue",
					coords:{left:250,top:274,width:150, height:22},
					type:"date",
					fieldType:"text"
				},
				{
					name:"date-of-expiry",
					coords:{left:420,top:274,width:150, height:22},
					type:"date",
					fieldType:"text"
				}
			],
	},
	{
		template_id : "pp-p2",
		template_fields :
			[
				{
					name:"fathers-name",
					coords:{left:25,top:70,width:475,height:23},
					type:"alpha",
					fieldType:"text"
				}, 
				{
					name:"mothers-name",
					coords:{left:25,top:110,width:475,height:23},
					type:"alpha",
					fieldType:"text"
				}, 
				{
					name:"spouses-name",
					coords:{left:25,top:150,width:475,height:23},
					type:"alpha",
					fieldType:"text"
				}, 
				{
					name:"address-line1",
					coords:{left:25,top:185,width:475,height:23},
					type:"alpha",
					fieldType:"text"
				}, 
				{
					name:"address-line2",
					coords:{left:25,top:216,width:475,height:35},
					type:"alpha",
					fieldType:"text"
				}, 
				{
					name:"address-line3",
					coords:{left:25,top:255,width:475,height:35},
					type:"alpha",
					fieldType:"text"
				}, 
				{
					name:"old-passport-details",
					coords:{left:25,top:305,width:475,height:23},
					type:"alpha",
					fieldType:"text"
				},
				{
					name:"file-details",
					coords:{left:25,top:340,width:475,height:23},
					type:"alpha",
					fieldType:"text"
				},				
			],
	}	
]
;