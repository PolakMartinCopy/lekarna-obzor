<?php
class RecommendationsController extends AppController {
	var $name = 'Recommendations';
	
	function send() {
		if (!isset($this->data)) {
			$this->Session->setFlash('Nejsou nastavena data pro odeslání doporučujícího formuláře.');
			$this->redirect(HP_URI);
		}
		
		$this->layout = 'content';
		
		require_once('recaptchalib.php');
		$privatekey = "6LdMatsSAAAAAB1mZf1rxm_qbwBF88DkmCiqkkv3";
		
		$challenge = (isset($_POST['recaptcha_challenge_field']) ? $_POST['recaptcha_challenge_field'] : '');
		$response = (isset($_POST['recaptcha_response_field']) ? $_POST['recaptcha_response_field'] : '');
		$resp = recaptcha_check_answer($privatekey, $_SERVER['REMOTE_ADDR'], $challenge, $response);
		
		$this->Recommendation->set($this->data);
		$recommendation_valid = $this->Recommendation->validates();
		$recaptcha_valid = $resp->is_valid;
		if ($recommendation_valid && $recaptcha_valid) {
			if ($this->Recommendation->send($this->data['Recommendation']['source_name'], $this->data['Recommendation']['source_email'], $this->data['Recommendation']['target_email'], $this->data['Recommendation']['request_uri'])) {
				$this->Session->setFlash('Vaše doporučení bylo odesláno na zadanou emailovou adresu.');
				$this->redirect($this->data['Recommendation']['request_uri']);					
			} else {
				$this->Session->setFlash('Vaše doporučení se nepodařilo odeslat na zadanou emailovou adresu.');
			}
		} else {
			$message = array();
			if (!$recommendation_valid) {
				$message[] = 'Formulář obsahuje chyby. Opravte je prosím a opakujte akci.';
			}
			if (!$recaptcha_valid) {
				$message[] = 'Nesprávně opsaný kontrolní kód z obrázku, opakujte prosím akci';
			}
			$this->Session->setFlash(implode('<br/>', $message));
		}
	}
	
/* 	function ajax_send() {
		$result = array(
			'success' => false,
			'message' => ''
		);
		if ($_POST) {
			if (isset($_POST['sourceName']) && isset($_POST['sourceEmail']) && isset($_POST['targetEmail']) && isset($_POST['challenge']) && isset($_POST['response']) && isset($_POST['backtraceUri'])) {
				require_once('recaptchalib.php');
				$privatekey = "6LdMatsSAAAAAB1mZf1rxm_qbwBF88DkmCiqkkv3";
				$resp = recaptcha_check_answer ($privatekey,
					$_SERVER['REMOTE_ADDR'],
					$_POST['challenge'],
					$_POST['response']
				);
				if ($resp->is_valid) {
					// Your code here to handle a successful verification
					$source_name = $_POST['sourceName'];
					$source_email = $_POST['sourceEmail'];
					$target_email = $_POST['targetEmail'];
					$backtrace_uri = $_POST['backtraceUri'];
					
					if ($this->Recommendation->send($source_name, $source_email, $target_email, $backtrace_uri)) {
						$result['success'] = true;
						$result['message'] = 'Vaše doporučení bylo odesláno na zadanou emailovou adresu.';
					} else {
						$result['message'] = 'Vaše doporučení se nepodařilo odeslat na zadanou emailovou adresu.';
					}
				} else {
					$this->render('/recommendations/send');
					$result['message'] = 'Nesprávně opsaný kontrolní kód z obrázku, opakujte prosím akci';
				}
			} else {
				$result['message'] = 'Nejsou definována všechna potřebná POST data';
			}
		} else {
			$result['message'] = 'Nejsou nastavena POST data';
		}
		echo json_encode($result);
		die();
	} */
}
?>