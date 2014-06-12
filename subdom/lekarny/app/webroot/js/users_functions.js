function copy_payment_as_delivery(){
	CompanyPaymentName = document.getElementById("CompanyPaymentName");
	CompanyDeliveryName = document.getElementById("CompanyDeliveryName");
	CompanyDeliveryName.value = CompanyPaymentName.value;

	CompanyPaymentStreet = document.getElementById("CompanyPaymentStreet");
	CompanyDeliveryStreet = document.getElementById("CompanyDeliveryStreet");
	CompanyDeliveryStreet.value = CompanyPaymentStreet.value;

	CompanyPaymentStreetNumber = document.getElementById("CompanyPaymentStreetNumber");
	CompanyDeliveryStreetNumber = document.getElementById("CompanyDeliveryStreetNumber");
	CompanyDeliveryStreetNumber.value = CompanyPaymentStreetNumber.value;

	CompanyPaymentPostalCode = document.getElementById("CompanyPaymentPostalCode");
	CompanyDeliveryPostalCode = document.getElementById("CompanyDeliveryPostalCode");
	CompanyDeliveryPostalCode.value = CompanyPaymentPostalCode.value;

	CompanyPaymentCity = document.getElementById("CompanyPaymentCity");
	CompanyDeliveryCity = document.getElementById("CompanyDeliveryCity");
	CompanyDeliveryCity.value = CompanyPaymentCity.value;

	return true;
}