var taValidationRules={

	"surname":{ type: 'alphaonly' },

	"given-names": { type: 'alphaonly' },

	"passport-no":{ type: 'alphanumeric' },
	
	"dob": { type:'date', dateFormat:'DD/MM/YYYY',
		maxDate: { base:'today', offset:'Y0,M0,D0', message: 'should not be in the future' },
		minDate: { base:'today', offset:'Y-100', message: 'should not more than 100 Years ago' } /*this is for test only, remove*/
	},

	"place-of-birth": { type: 'alphaonly' },

	"place-of-issue": { type: 'alphaonly' },

	"date-of-issue": { type:'date', dateFormat:'DD/MM/YYYY',
		maxDate: { base:'today', offset:'Y0,M0,D0', message: 'should not be in the future' },
		
	},

	"date-of-expiry": { type:'date', dateFormat:'DD/MM/YYYY',
		minDate: { base:'today', offset:'M3', message: 'should be later than 3 Months' } /*this is for test only, remove*/
	},

	"fathers-name": { type: 'alphaonly' },

	"mothers-name": { type: 'alphaonly' },

	"spouses-name": { type: 'alphaonly' },

	"city": { type: 'alphaonly' },

	"telephone": { type: 'numericonly', numDigits:15, message:'should not be more than 15 digit number' }

}
