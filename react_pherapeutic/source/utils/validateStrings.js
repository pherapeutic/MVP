
export const validatePhoneNumber = (param) => {
	console.log("validating string ", param);
	const phoneNumberRegex = /^([0-9]{1,100})+$/

	if (param) {
		if (phoneNumberRegex.test(param)) {
			if (param.length < 8) {
				return "Phone number should be atleast 8 digits."
			} else {
				return false;
			}
		} else {
			return "Please enter a valid phone number."
		}
	} else {
		return 'Please enter your mobile number.'
	}
};


export const validateNumber = (cc, number) => {
	console.log("validating number ", number);
	const numberRegex = /^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]\d{3}[\s.-]\d{4}$/

	var minLength;
	var maxLength;

	switch (cc) {
		case "+84":
			minLength = 8;
			maxLength = 8;
			break;
		case "+86":
			minLength = 11;
			maxLength = 11;
			break;
		case "+62":
			minLength = 8;
			maxLength = 12;
			break;
		case "+60":
			minLength = 10;
			maxLength = 10;
			break;
		case "+63":
			minLength = 10;
			maxLength = 10;
			break;
		case "+65":
			minLength = 8;
			maxLength = 8;
			break;
	}

	if (number) {
		if (numberRegex.test(number)) {
			if (number.length < minLength || number.length > maxLength) {
				return `Phone number should be atleast ${minLength} digit.`
			} else {
				return false;
			}
		} else {
			return 'Enter a valid phone number.'
		}
	}
};

export const validateEmail = (param) => {
	console.log("email validate is called ", param)
	const emailRegex = /^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/
	if (param) {
		if (emailRegex.test(param)) {
			return false
		} else {
			return "Please enter a valid email."
		}
	}
}