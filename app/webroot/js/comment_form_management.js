$(document).ready(function() {
	
	// defaultne je aktivni tab s detaily produktu
	$('#button_details').attr('class', 'button_active');
	$('#button_comments').attr('class', 'button_noactive');
	$('#product_comments_wrapper').hide();
	
	$('#button_comments').click(function() {
		$('#button_details').attr('class', 'button_noactive');
		$('#button_comments').attr('class', 'button_active');
		$('#product_details_wrapper').hide();
		$('#product_comments_wrapper').show();
	});

	$('#button_details').click(function() {
		$('#button_details').attr('class', 'button_active');
		$('#button_comments').attr('class', 'button_noactive');
		$('#product_details_wrapper').show();
		$('#product_comments_wrapper').hide();
	});
	
	$('#askProductLink').click(function() {
		$('#button_details').attr('class', 'button_noactive');
		$('#button_comments').attr('class', 'button_active');
		$('#product_details_wrapper').hide();
		$('#product_comments_wrapper').show();
	});

	$(document).on('submit', '#CommentAddForm', function(e) {
		e.preventDefault();

		var authorId = '#CommentAuthor';
		var emailId = '#CommentEmail';
		var subjectId = '#CommentSubject';
		var bodyId = '#CommentBody';
		var productIdId = '#CommentProductId';

		var author = $(authorId).val();
		var email = $(emailId).val();
		var subject = $(subjectId).val();
		var body = $(bodyId).val();
		var productId = $(productIdId).val();

		var validAuthor = true;
		var validEmail = true;
		var validSubject = true;
		var validBody = true;

		var validAuthorMessage = '';
		var validEmailMessage = '';
		var validSubjectMessage = '';
		var validBodyMessage = '';

		// validace jmena autora
		var authorErrorsElement = $($(authorId).parent().parent()).find('.formErrors');
		// jmeno musi byt neprazdne
		if (author === "") {
			validAuthor = false;
			validAuthorMessage = 'Zadejte vaše jméno, nebo přezdívku.';
			$(authorId).attr('style', 'background-color:red');
			authorErrorsElement.html(validAuthorMessage)
		} else {
			$(authorId).attr('style', 'background-color:none');
			authorErrorsElement.empty();
		}

		// validace emailu
		var emailErrorsElement = $($(emailId).parent().parent()).find('.formErrors');
		if (!isEmailValid(email)) {
			validEmail = false;
			validEmailMessage = 'Vyplňte prosím existující emailovou adresu, abychom Vám mohli odeslat odpověď na mail.';
			$(emailId).attr('style', 'background-color:red');
			emailErrorsElement.html(validEmailMessage)
		} else {
			$(emailId).attr('style', 'background-color:none');
			emailErrorsElement.empty();
		}
		
		// validace predmetu zpravy
		var subjectErrorsElement = $($(subjectId).parent().parent()).find('.formErrors');
		// jmeno musi byt neprazdne
		if (subject === "") {
			validSubject = false;
			validSubjectMessage = 'Zadejte předmět komentáře / dotazu.';
			$(subjectId).attr('style', 'background-color:red');
			subjectErrorsElement.html(validSubjectMessage)
		} else {
			$(subjectId).attr('style', 'background-color:none');
			subjectErrorsElement.empty();
		}
		
		// validace obsahu komentare
		var bodyErrorsElement = $($(bodyId).parent().parent()).find('.formErrors');
		// jmeno musi byt neprazdne
		if (body === "") {
			validBody = false;
			validBodyMessage = 'Zadejte tělo komentáře / dotazu.';
			$(bodyId).attr('style', 'background-color:red');
			bodyErrorsElement.html(validBodyMessage)
		} else {
			$(bodyId).attr('style', 'background-color:none');
			bodyErrorsElement.empty();
		}
		
		if (validAuthor && validEmail && validSubject && validBody){ 
			$.ajax({
				url: '/comments/ajax_add',
				dataType: 'json',
				type: 'post',
				data: {
					author: author,
					email: email,
					subject: subject,
					body: body,
					productId: productId
				},
				success: function(data) {
					if (data.success) {
						// vyprazdnim formular
						$(authorId).val('');
						$(emailId).val('');
						$(subjectId).val('');
						$(bodyId).val('');
					}
					// vypisu hlasku
					alert(data.message);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});
		}
	});
});