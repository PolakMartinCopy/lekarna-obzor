<?
class Status extends AppModel {

	var $name = 'Status';
	
	var $actsAs = array('Containable');

	var $hasMany = array('Order');
	
	var $belongsTo = array('MailTemplate');
	

	var $validate = array(
		'name' => array(
			'minLength' => array(
				'rule' => array('minLength', 1),
				'required' => true,
				'message' => 'Vyplňte prosím název statusu.'
			),
			'isUnique' => array(
				'rule' => array('isUnique', 'name'),
				'required' => true,
				'message' => 'Tento status již existuje! Zvolte prosím jiný název stavu.'
			)
		)
	);

	function change_notification($order_id, $status_id){
		// nejdrive overim, jestli ma dany status nadefinovany nejaky template
		$this->recursive = -1;
		$status = $this->read(null, $status_id);
		
		if ( !empty($status) && $status['Status']['mail_template_id'] != 0 ){
			// nactu si detaily z objednavky
			$this->Order->recursive = -1;
			$order = $this->Order->read(null, $order_id);
			
			// nactu si mail template
			$template = $this->MailTemplate->read(null, $status['Status']['mail_template_id']);

			// template musim zpracovat, najdu si promenne
			$matches = '';
			$matches2 = '';
			preg_match_all("/%(.*)%/U", $template['MailTemplate']['content'], $matches, PREG_SET_ORDER);
			preg_match_all("/%(.*)%/U", $template['MailTemplate']['subject'], $matches2, PREG_SET_ORDER);
			
			// spojim matches
			$matches = array_merge($matches, $matches2);
			
			// vysbiram si jednotlive objekty, ktere potrebuju nacist
			$objects = array();
			
			// objekty, ktere mam uz nactene, nebudu nacitat znovu
			$skipped_objects = array('Order', 'Status');

			// projdu si "matche"
			foreach ( $matches as $match ){
				$o = explode(".", $match[1]);

				// jestli ho jeste nemam nacteny a zaroven nepatri do "skipped"
				if ( !in_array($o[0], $objects) && !in_array($o[0], $skipped_objects) ){
					$objects[] = $o[0];
				}
			}
			
			// potrebuju nahradit promenne
			// projdu jeste jednou matches
			foreach ( $matches as $match ){
				// musim si sestavit promennou, kterou to budu nahrazovat
				$column = explode( '.', $match[1] );
				
				// nazev promenne
				$varname = strtolower($column[0]);

				// prvni index
				$index = $column[0];
				
				// nazev promenne
				$column = $column[1];
				
				// sestavim si retezec, kterym nahradim needle
				$replace = ${$varname}[$index][$column];
				// jedna-li se o datum, musim ho pocestit
				if ( $column == 'created' || $column == 'modified' ){
					$replace = text_date(${$varname}[$index][$column]);
				}
				
				// nahradim to
				$template['MailTemplate']['content'] = str_replace($match[0], $replace, $template['MailTemplate']['content']);
				$template['MailTemplate']['subject'] = str_replace($match[0], $replace, $template['MailTemplate']['subject']);
			}
			
			// mam sestaveny mail template s daty
			// musim ho odeslat
			$this->Order->Company->recursive = -1;
			$company = $this->Order->Company->read(null, $order['Order']['company_id']);
			
			// natahnu si mailovaci skript
			App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
			$ppm = &new phpmailer;
			$ppm->CharSet = 'UTF-8';
			$ppm->Hostname = 'lekarny.lekarna-obzor.cz';
			$ppm->Sender = 'no-reply@lekarna-obzor.cz';
			
			$ppm->From = 'no-reply@lekarna-obzor.cz';
			$ppm->FromName = 'Automatické potvrzení';
			$ppm->ReplyTo = 'no-reply@lekarna-obzor.cz';
			
			$ppm->Body = $template['MailTemplate']['content'];
			$ppm->Subject = $template['MailTemplate']['subject'];
			$ppm->AddAddress($company['Company']['person_email'], $company['Company']['person_first_name'] . ' ' . $company['Company']['person_last_name']);
			
			return $ppm->Send();
		} else {
			return false;
		}
	}

	function has_requested($status_id){
		$return = false;
		$status = $this->read(array('requested_fields'), $status_id);
		$rfs = array();
		if ( !empty($status['Status']['requested_fields']) ){
			$rf = explode("\n", $status['Status']['requested_fields']);
			$count = count($rf);
			for( $i =0; $i < $count; $i = $i + 2 ){
				$rfs[trim($rf[$i])] = $rf[$i + 1]; 
			}
			$return = $rfs;
		}
		return $return;
	}
}
?>