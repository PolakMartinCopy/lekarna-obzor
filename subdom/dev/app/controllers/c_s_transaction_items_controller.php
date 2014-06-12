<?php 
class CSTransactionItemsController extends AppController {
	var $name = 'CSTransactionItems';
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána položka, kterou chcete odstranit.');
			$this->redirect(array('controller' => 'c_s_storings', 'action' => 'index'));
		}
		
		$transaction_item = $this->CSTransactionItem->find('first', array(
			'conditions' => array('CSTransactionItem.id' => $id),
			'contain' => array()	
		));

		if ($this->CSTransactionItem->delete($id)) {
			// musim vedet, jestli je polozka z naskladneni, faktury nebo dobropisu a prepocitat celkove ceny
			if (isset($transaction_item['CSTransactionItem']['c_s_storing_id']) && !empty($transaction_item['CSTransactionItem']['c_s_storing_id'])) {
				if (!$this->CSTransactionItem->hasAny(array('CSTransactionItem.c_s_storing_id' => $transaction_item['CSTransactionItem']['c_s_storing_id']))) {
					$this->CSTransactionItem->CSStoring->delete($transaction_item['CSTransactionItem']['c_s_storing_id']);
				}
			} elseif (isset($transaction_item['CSTransactionItem']['c_s_invoice_id']) && !empty($transaction_item['CSTransactionItem']['c_s_invoice_id'])) {
				if (!$this->CSTransactionItem->hasAny(array('CSTransactionItem.c_s_invoice_id' => $transaction_item['CSTransactionItem']['c_s_invoice_id']))) {
					$this->CSTransactionItem->CSInvoice->delete($transaction_item['CSTransactionItem']['c_s_invoice_id']);
				} else {
					$invoice = $this->CSTransactionItem->CSInvoice->find('first', array(
						'conditions' => array('CSInvoice.id' => $transaction_item['CSTransactionItem']['c_s_invoice_id']),
						'contain' => array('CSTransactionItem')	
					));
					// celkova cena se mi prepocita pri ulozeni faktury / dobropisu
					if (!empty($invoice)) {
						$this->CSTransactionItem->CSInvoice->save($invoice);
					}
				}
			} elseif (isset($transaction_item['CSTransactionItem']['c_s_credit_note_id']) && !empty($transaction_item['CSTransactionItem']['c_s_credit_note_id'])) {
				if (!$this->CSTransactionItem->hasAny(array('CSTransactionItem.c_s_credit_note_id' => $transaction_item['CSTransactionItem']['c_s_credit_note_id']))) {
					$this->CSTransactionItem->CSCreditNote->delete($transaction_item['CSTransactionItem']['c_s_credit_note_id']);
				} else {
					$credit_note = $this->CSTransactionItem->CSCreditNote->find('first', array(
						'conditions' => array('CSCreditNote.id' => $transaction_item['CSTransactionItem']['c_s_credit_note_id']),
						'contain' => array('CSTransactionItem')
					));
					if (!empty($credit_note)) {
						$this->CSTransactionItem->CSCreditNote->save($credit_note);
					}
				}
			}
			$this->Session->setFlash('Položka byla odstraněna.');
		} else {
			$this->Session->setFlash('Položku se nepodařilo odstranit');
		}
		
		$controller = 'c_s_invoices';
		if (isset($transaction_item['CSTransactionItem']['c_s_storing_id']) && !empty($transaction_item['CSTransactionItem']['c_s_storing_id'])) {
			$controller = 'c_s_storings';
		} elseif (isset($transaction_item['CSTransactionItem']['c_s_credit_note_id']) && !empty($transaction_item['CSTransactionItem']['c_s_credit_note_id'])) {
			$controller = 'c_s_credit_notes';
		}
		
		$this->redirect(array('controller' => $controller, 'action' => 'index'));
	}
}
?>