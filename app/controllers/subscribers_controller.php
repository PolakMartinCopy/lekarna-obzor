<?php 
class SubscribersController extends AppController {
	var $name = 'Subscribers';
	
	/**
	 * ajaxova metoda pro zalozeni ucastnika pro odber newsletteru
	 */
	function add() {
		if (!isset($this->data)) {
			$this->Session->setFlash('Nejsou nastavena data pro přihlášení k odebírání newsletteru.');
			$this->redirect(HP_URI);
		}
		
		$this->Subscriber->set($this->data);
		if ($this->Subscriber->validates()) {
			// to, ze je email validni, znamena, ze je ve validnim tvaru (pravidlo 'email') a ze je unikatni
			// unikatni znamena, ze neni v tabulce subscribers a ani neni v tabulce customers spolu s povolenym zasilanim newsletteru
			// nejdriv proto musim zkontrolovat, zda neni v tabulce customers se zakazanym posilanim newletteru a kdyztak ho povolit, pak nevkladam do subscribers
			App::import('Model', 'Customer');
			$this->Customer = new Customer;
			$customer = $this->Customer->find('first', array(
				'conditions' => array('email' => $this->data['Subscriber']['email']),
				'contain' => array(),
				'fields' => array('id')
			));
			// pokud jsem nenasel uzivatele s timto emailem, vlozim ho do tabulky subscribers
			if (empty($customer)) {
				$this->Subscriber->create();
				// nemusim validovat, protoze data uz prosla validaci
				$this->Subscriber->save($this->data, false);
			} else {
				// pokud jsem nasel uzivatele s timto emailem a zaroven mi prosla validace znamena, ze uzivatel nema prihlasen odber newsletteru, takze mu ho prihlasim
				$customer['Customer']['newsletter'] = true;
				$this->Customer->save($customer, false);
			}
			$this->Session->setFlash('Zadaná emailová adresa byla přihlášena k odběru novinek.');
			$this->redirect($this->data['Subscriber']['request_uri']);
		} else {
			// zasilani newsletteru UZ JE povoleno jednim ze 2 vyse popsanych zpusobu (email uz je v tabulce subscribers nebo je v customers s newsletter = true) nebo email neni ve validnim
			// tvaru
			$this->_persistValidation('Subscriber');
			$this->Session->setFlash('Přihlášení k odběru novinek se nezdařilo, opravte chyby ve formuláři a opakujte akci.');
			$this->redirect($this->data['Subscriber']['request_uri'] . '#subscription');
		}
	}
}
?>