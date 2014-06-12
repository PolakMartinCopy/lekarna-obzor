function isEmailValid(email) {
	var emailRE = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	// email musi odpovidat definovanemu RE
	return emailRE.test(email);
}