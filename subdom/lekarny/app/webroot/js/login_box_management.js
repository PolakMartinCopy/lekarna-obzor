$(document).ready(function() {
	$('#menu_login').show();

	$('#loginLink').click(function(e) {
		e.preventDefault();
		$('.prihlaseni').addClass('active');
		$('.kosik').removeClass('active');
		$('.o_lekarne').removeClass('active');
		$('#menu_login').show();
		$('#menu_cart').hide();
		$('#menu_about').hide();
	});
	
	// vysypani kosiku
	$('#dump').click(function(e) {
		e.preventDefault();
		$.ajax({
			url: '/carts/ajax_dump',
			type: 'post',
			dataType: 'json',
			success: function(data) {
				alert(data.message);
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert(errorThrown);
			}
		});
	});
	
	// po kliknuti na pole login se oznaci jeho obsah
	$('#loginUsername').click(function() {
		// se vycisti jeho obsah
		$(this).select();
	});
});