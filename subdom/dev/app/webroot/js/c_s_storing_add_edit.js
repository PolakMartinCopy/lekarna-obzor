$(function() {
	// pamatuje si pocet radku ve formulari pro vlozeni/editaci naskladneni
	var rowCount = $('.product_row').length;
	
	$("#CSStoringDate").datepicker({
		changeMonth: false,
		numberOfMonths: 1
	});

	$('table').delegate('.CSTransactionItemProductName', 'focusin', function() {
		if ($(this).is(':data(autocomplete)')) return;
		$(this).autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/product_variants/autocomplete_list',
			select: function(event, ui) {
				var tableRow = $(this).closest('tr');
				var count = tableRow.attr('rel');
				$(this).val(ui.item.label);
				$('#CSTransactionItem' + count + 'ProductVariantId').val(ui.item.value);
				$('#CSTransactionItem' + count + 'ProductName').val(ui.item.label);
				return false;
			}
		});
	});

	$('table').delegate('.CSTransactionItemBusinessPartnerName', 'focusin', function() {
		if ($(this).is(':data(autocomplete)')) return;
		$(this).autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/business_partners/autocomplete_list',
			select: function(event, ui) {
				var tableRow = $(this).closest('tr');
				var count = tableRow.attr('rel');
				$(this).val(ui.item.label);
				$('#CSTransactionItem' + count + 'BusinessPartnerId').val(ui.item.value);
				$('#CSTransactionItem' + count + 'BusinessPartnerName').val(ui.item.label);
				return false;
			}
		});
	});
	
	$('table').delegate('.addRowButton', 'click', function(e) {
		e.preventDefault();
		// pridat radek s odpovidajicim indexem na konec tabulky s addRowButton
		var tableRow = $(this).closest('tr');
		tableRow.after(productRow(rowCount));
		// zvysim pocitadlo radku
		rowCount++;
		
		$(".new_product_link").fancybox({
			'scrolling'		: 'no',
			'titleShow'		: false,
			'onClosed'		: function() {
			    $("#login_error").hide();
			}
		});
	});

	$('table').delegate('.removeRowButton', 'click', function(e) {
		e.preventDefault();
		var tableRow = $(this).closest('tr');
		tableRow.remove();
	});
	
	// promenna, kde si zapamatuju cislo radku, do ktereho chci vlozit novy produkt
	var row = null;
	
	$('.new_product_link').fancybox({
		'scrolling'		: 'no',
		'titleShow'		: false,
		'onClosed'		: function() {
		    $("#login_error").hide();
		}
	});

	$('table').delegate('.new_product_link', 'click', function(e) {
		row = $(this).closest('tr').attr('rel');
	});

	$("#new_product_form").bind("submit", function(e) {
		e.preventDefault();
		// validate
		if ($("#ProductName").val().length < 1) {
		    $("#login_error").show();
		    $.fancybox.resize();
		    return false;
		}

		$.fancybox.showActivity();

		// save
		$.ajax({
			type : 'POST',
			cache : false,
			url : '/user/product_variants/ajax_add',
			data : $(this).serializeArray(),
			dataType: 'json',
			success: function(data) {
				alert(data.message);
				// pokud se vlozeni do ciselniku podarilo, naplnim formularova pole vlozenymi hodnotami
				if (data.success) {
					var productVariantId = data.productVariantId;
					$('#CSTransactionItem' + row + 'ProductVariantId').val(productVariantId);
					$('#CSTransactionItem' + row + 'ProductName').val($('#ProductName').val());
				}
			}
		});
		
		$.fancybox.close();

		return false;
	});
});

function productRow(count) {
	count++;
	var rowData = '<tr rel="' + count + '" class="product_row">';
	rowData += '<td nowrap>';
	rowData += '<input name="data[CSTransactionItem][' + count + '][product_name]" type="text" class="CSTransactionItemProductName" size="30" id="CSTransactionItem' + count + 'ProductName" />';
	rowData += '<input type="hidden" name="data[CSTransactionItem][' + count + '][product_variant_id]" id="CSTransactionItem' + count + 'ProductVariantId" />';
	rowData += '<a href="#new_product_form" class="new_product_link"><img src="/images/icons/add.png" alt="Novy" /></a>';
	rowData += '</td>';
	rowData += '<td><input name="data[CSTransactionItem][' + count + '][description]" type="text" size="25" id="CSTransactionItem' + count + 'Description"></td>';
	rowData += '<td>';
	rowData += '<input name="data[CSTransactionItem][' + count + '][business_partner_name]" type="text" class="CSTransactionItemBusinessPartnerName" size="30" id="CSTransactionItem' + count + 'BusinessPartnerName" />';
	rowData += '<input type="hidden" name="data[CSTransactionItem][' + count + '][business_partner_id]" id="CSTransactionItem' + count + 'BusinessPartnerId" />';
	rowData += '</td>';
	rowData += '<td><input name="data[CSTransactionItem][' + count + '][quantity]" type="text" size="2" maxlength="11" id="CSTransactionItem' + count + 'Quantity" /></td>';
	rowData += '<td><input name="data[CSTransactionItem][' + count + '][price]" type="text" size="5" maxlength="11" id="CSTransactionItem' + count + 'Price" /></td>';
	rowData += '<td>';
	rowData += '<select name="data[CSTransactionItem][' + count + '][currency_id]" id="CSTransactionItem' + count + 'CurrencyId">';
	jQuery.each(currencies, function(index, value) {
		rowData += '<option value="' + index + '">' + value + '</option>';
		
	});
	rowData += '</select>';
	rowData += '<td><input name="data[CSTransactionItem][' + count + '][exchange_rate] type="text" size="3" maxlength="8" id="CSTransactionItem' + count + 'ExchangeRate' + '"  value="1"/></td>';
	rowData += '<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>';
	rowData += '</tr>';
	return rowData;
}