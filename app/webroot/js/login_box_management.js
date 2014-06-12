$(document).ready(function() {
	
	$('#menu_cart').show();
	$('#menu_login').hide();
	$('#menu_about').hide();
	
	$('#cartLink').click(function(e) {
		$('.prihlaseni').removeClass('active');
		$('.kosik').addClass('active');
		$('.o_lekarne').removeClass('active');
		e.preventDefault();
		$('#menu_cart').show();
		$('#menu_login').hide();
		$('#menu_about').hide();
	});

	$('#loginLink').click(function(e) {
		e.preventDefault();
		$('.prihlaseni').addClass('active');
		$('.kosik').removeClass('active');
		$('.o_lekarne').removeClass('active');
		$('#menu_login').show();
		$('#menu_cart').hide();
		$('#menu_about').hide();
	});
	
	$('#aboutLink').click(function(e) {
		$('.prihlaseni').removeClass('active');
		$('.kosik').removeClass('active');
		$('.o_lekarne').addClass('active');
		e.preventDefault();
		$('#menu_login').hide();
		$('#menu_cart').hide();
		$('#menu_about').show();
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