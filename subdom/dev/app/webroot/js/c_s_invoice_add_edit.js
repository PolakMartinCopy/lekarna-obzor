	$(function() {
		var rowCount = $('.product_row').length;
		var DEFAULT_VAT = 15;
		var vat = DEFAULT_VAT;
		
		$("#CSInvoiceDueDate").datepicker({
			changeMonth: false,
			numberOfMonths: 1
		});

		$('#CSInvoiceBusinessPartnerName').autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/business_partners/autocomplete_list',
			select: function(event, ui) {
				$('#CSInvoiceBusinessPartnerName').val(ui.item.label);
				$('#CSInvoiceBusinessPartnerId').val(ui.item.value);
				// chci v alertu zobrazit poznamku k vybranemu OP
				$.ajax({
					'type': 'POST',
					'dataType': 'json',
					'url': '/user/business_partners/ajax_find_by_id',
					'data': {
						id: ui.item.value
					},
					'success': function(data) {
						if (data.success) {
							alert(data.message.BusinessPartner.note);
						} else {
							alert(data.message);
						}
					}
				});
				return false;
			}
		});

		$('table').delegate('.CSTransactionItemProductName', 'focusin', function() {
			if ($(this).is(':data(autocomplete)')) return;
			$(this).autocomplete({
				delay: 500,
				minLength: 2,
				source: '/user/product_variants/autocomplete_list/',
				select: function(event, ui) {
					var tableRow = $(this).closest('tr');
					var count = tableRow.attr('rel');
					$(this).val(ui.item.label);
					$('#CSTransactionItem' + count + 'ProductVariantId').val(ui.item.value);
					$('#CSTransactionItem' + count + 'ProductName').val(ui.item.name);
					vat = ui.item.vat;
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
			// globalni dph nastavim na zakladni sazbu
			vat = DEFAULT_VAT;
		});

		$('table').delegate('.removeRowButton', 'click', function(e) {
			e.preventDefault();
			var tableRow = $(this).closest('tr');
			tableRow.remove();
		});
		
		$('table').delegate('.price', 'blur', function(e) {
			e.preventDefault();
			var price = $(this).val();
			var price_vat = Math.round(100 * parseFloat(price) + Math.round(parseFloat(price) * parseFloat(vat))) / 100;
			if (!isNaN(price_vat)) {
				var price_id = $(this).attr('id');
				var price_vat_id = price_id + 'Vat';
				$('#' + price_vat_id).val(price_vat);
			}
		});
	});

	function productRow(count) {
		count++;
		var rowData = '<tr rel="' + count + '" class="product_row">';
		rowData += '<td>';
		rowData += '<input name="data[CSTransactionItem][' + count + '][product_name]" type="text" class="CSTransactionItemProductName" size="50" id="CSTransactionItem' + count + 'ProductName" />';
		rowData += '<input type="hidden" name="data[CSTransactionItem][' + count + '][product_variant_id]" id="CSTransactionItem' + count + 'ProductVariantId" />';
		rowData += '</td>';
		rowData += '<td><input name="data[CSTransactionItem][' + count + '][description]" type="text" size="50" id="CSTransactionItem' + count + 'Description"></td>';
		rowData += '<td><input name="data[CSTransactionItem][' + count + '][quantity]" type="text" size="2" maxlength="11" id="CSTransactionItem' + count + 'Quantity" /></td>';
		rowData += '<td><input name="data[CSTransactionItem][' + count + '][price]" type="text" size="5" maxlength="11" id="CSTransactionItem' + count + 'Price" class="price"/></td>';
		rowData += '<td><input name="data[CSTransactionItem][' + count + '][price_vat]" type="text" size="5" maxlength="11" id="CSTransactionItem' + count + 'PriceVat" /></td>';
		rowData += '<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>';
		rowData += '</tr>';
		return rowData;
	}