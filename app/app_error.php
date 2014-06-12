<?php
class AppError extends ErrorHandler {
	function __construct($method, $messages) {
		parent::__construct($method, $messages);
	}

	function __outputMessage($template) {
		$this->controller->layout = 'empty_page';
		parent::__outputMessage($template);
	}
}  
?>