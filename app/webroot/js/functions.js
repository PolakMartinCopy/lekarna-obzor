function countPrice(differ){
	_withoutTax = document.getElementById("ProductPriceWithoutTax");
	_withTax = document.getElementById("ProductPrice");
	_tax = document.getElementById("ProductTaxClassId");
	
	taxValue = 0.19;
	if ( _tax.value == 2 ){
		taxValue = 0.09;
	}

	if ( differ == 'with' ){
		/* spoctu cenu s dph */
		cenasdph = parseFloat(_withoutTax.value) + parseFloat(_withoutTax.value * taxValue);

		/* odeseknu si desetinnou cast ceny z dph */
		cenasdphfloor = Math.floor(cenasdph);

		/* spoctu si desetinnou cast ceny s dph */
		cenasdphdesetiny = cenasdph - cenasdphfloor;
		cenasdphdesetiny = cenasdphdesetiny * 100;
		cenasdphdesetiny = Math.ceil(cenasdphdesetiny);
		if ( cenasdphdesetiny <= 50 ){
			cenasdph = cenasdphfloor + 0.5;
		} else {
			cenasdph = cenasdphfloor + 1;
		}

		_withTax.value = cenasdph;
	}

	return (true);
}