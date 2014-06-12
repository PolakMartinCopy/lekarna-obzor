<?php 
class DocumentsController extends AppController {
	var $name = 'Documents';
	
	var $left_menu_list = array();
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_add() {
		if (isset($this->data['Document']['imposition_id'])) {
			$imposition_id = $this->data['Document']['imposition_id'];
			$imposition = $this->Document->Imposition->find('first', array(
				'conditions' => array('Imposition.id' => $imposition_id),
				'contain' => array()
			));
			
			if (empty($imposition)) {
				$this->Session->setFlash('Zvolený úkol neexistuje.');
				$this->redirect(array('controller' => 'impositions', 'action' => 'index'));
			}
			
			$impositions_user = $this->Document->Imposition->ImpositionsUser->find('first', array(
				'conditions' => array(
					'imposition_id' => $imposition_id,
					'user_id' => $this->user['User']['id']
				),
				'contain' => array()
			));
			
			// nahravat dokumenty k ukolum mohou zadavatele i resitele
			if (!$this->Document->checkUser($this->user, $imposition['Imposition']['user_id']) && empty($impositions_user)) {
				$this->Session->setFlash('Neoprávněný přístup. Nemůžete nahrát dokument k úkolu, kde nejste zadavatelem ani řešitelem.');
				$this->redirect(array('controller' => 'impositions', 'action' => 'index'));
			}
			
			$redirect = array('controller' => 'impositions', 'action' => 'view', $imposition_id);

		} elseif (isset($this->data['Document']['business_partner_id'])) {
			$business_partner_id = $this->data['Document']['business_partner_id'];
			
			$business_partner = $this->Document->BusinessPartner->find('first', array(
				'conditions' => array('BusinessPartner.id' => $business_partner_id),
				'contain' => array()
			));
						
			if (empty($business_partner)) {
				$this->Session->setFlash('Zvolený obchodní partner neexistuje');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'index'));
			}
			
			if (!$this->Document->checkUser($this->user, $business_partner['BusinessPartner']['user_id'])) {
				$this->Session->setFlash('Neoprávněný přístup. Nemůžete nahrát dokument ke zvolenému obchodnímu partnerovi.');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'index'));
			}
			
			$redirect = array('controller' => 'business_partners', 'action' => 'view', $business_partner_id);

		} elseif (isset($this->data['Document']['offer_id'])) {
			$offer_id = $this->data['Document']['offer_id'];
			
			$offer = $this->Document->Offer->find('first', array(
				'conditions' => array('Offer.id' => $offer_id),
				'contain' => array('BusinessSession')
			));
			
			if (empty($offer)) {
				$this->Session->setFlash('Zvolená nabídka neexistuje.');
				$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
			}
			
			if (!$this->Document->checkUser($this->user, $offer['BusinessSession']['user_id'])) {
				$this->Session->setFlash('Neoprávněný přístup. Nemůžete nahrát dokument k této nabídce.');
				$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
			}
			
			$redirect = array('controller' => 'offers', 'action' => 'view', $offer_id);
		} else {
			$this->Session->setFlash('Není zadána entita, ke které chcete dokument nahrát');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			$flash = array();
			for ( $i = 0; $i < $this->data['Document']['document_fields']; $i++ ) {
				if (is_uploaded_file($this->data['Document']['document' . $i]['tmp_name'])) {
					$this->data['Document']['document' . $i]['name'] = strip_diacritic($this->data['Document']['document' . $i]['name'], true);
					$this->data['Document']['document' . $i]['name'] = $this->Document->checkName('files/documents/' . $this->data['Document']['document' . $i]['name']);
					if ( move_uploaded_file($this->data['Document']['document' . $i]['tmp_name'], $this->data['Document']['document' . $i]['name']) ){
						// potrebuju zmenit prava u dokumentu
						chmod($this->data['Document']['document' . $i]['name'], 0644);
						
						$document_name = explode('/', $this->data['Document']['document' . $i]['name']);
						$document_name = $document_name[count($document_name) - 1];

						$save['Document'] = array(
							'name' => $document_name,
							'title' => $this->data['Document']['document' . $i]['title']
						);
						if (empty($save['Document']['title'])) {
							$save['Document']['title'] = $save['Document']['name'];
						}
						if (isset($imposition_id)) {
							$save['Document']['imposition_id'] = $imposition_id;
						} elseif (isset($business_partner_id)) {
							$save['Document']['business_partner_id'] = $business_partner_id;
						} elseif (isset($offer_id)) {
							$save['Document']['offer_id'] = $offer_id;
						}

						$this->Document->create();
						if ($this->Document->save($save)) {
							$flash[] = 'Dokument ' . $save['Document']['name'] . ' byl nahrán';
						} else {
							$flash[] = 'Dokument ' . $save['Document']['name'] . ' se nepodařilo nahrát, opakujte prosím akci';
							// smazu chybne nahrany dokument z disku
							unlink('files/documents/' . $this->data['Document']['document' . $i]['name']);
						}
					} else {
						$flash[] = 'Dokument ' . $this->data['Document']['document' . $i]['name'] . ' se nepodařilo přesunout do složky /files/documents';
					}
				} else {
					$flash[] = 'Dokument ' . $this->data['Document']['document' . $i]['name'] . ' se nepodařilo nahrát na server';
				}
			}
		}
		$this->Session->setFlash(implode('<br/>', $flash));
		$this->redirect($redirect);
	}
	
	function user_add_from_web() {
		if (isset($this->data)) {
			
			// nastavim si redirect
			$redirect = array();
			if (isset($this->data['Document']['imposition_id'])) {
				$redirect = array('controller' => 'impositions', 'action' => 'view', $this->data['Document']['imposition_id']);
				$entity = array('imposition_id' => $this->data['Document']['imposition_id']);
			} elseif (isset($this->data['Document']['business_partner_id'])) {
				$redirect = array('controller' => 'business_partners', 'action' => 'view', $this->data['Document']['business_partner_id']);
				$entity = array('business_partner_id' => $this->data['Document']['business_partner_id']);
			} elseif (isset($this->data['Document']['offer_id'])) {
				$redirect = array('controller' => 'offers', 'action' => 'view', $this->data['Document']['offer_id']);
				$entity = array('offer_id' => $this->data['Document']['offer_id']);
			}
			
			// inicializace flash
			$flash = array();
			// projdu data z formulare a pokusim se nahrat pozadovane soubory
			foreach ($this->data['Document']['data'] as $index => $document) {
				if (empty($document['url'])) {
					$flash[] = 'Není zadáno URL dokumentu';
					continue;
				}
				
				// dogeneruju informace, pokud nejsou zadane
				if (empty($document['name'])) {
					$flash[] = 'Prázdné jméno souboru - ' . $document['url'];
					continue;
				}
				
				$document['name'] = strip_diacritic($document['name'], true);
				
				// zkontroluju validitu nazvu souboru
				if (!preg_match('/(?:$|(.+?)(?:(\.[^.]*$)|$))/', $document['name'])) {
					$flash[] = 'Špatné jméno dokumentu - ' . $document['name'];
					continue;
				}
				
				if (empty($document['title'])) {
					$document['title'] = $document['name'];
				}
				
				$document['name'] = $this->Document->checkName('files/documents/' . $document['name']);
	
				// natahnu dokument na zadane adrese a pojmenuju ho podle zadaneho jmena
				$document_content = file_get_contents($document['url']);
				
				if (file_put_contents($document['name'], $document_content)) {
					$document['name'] = str_replace('files/documents/', '', $document['name']);
					$document = array_merge($document, $entity);
					$this->Document->create();
					if ($this->Document->save($document)) {
						$flash[] = 'Dokument ' . $document['name'] . ' byl uložen.';
					} else {
						$flash[] = 'Dokument ' . $document['name'] . ' se nepodařilo uložit do db, opakujte prosím akci';
						// smazu natazeny dokument z disku
						unlink('files/documents/' . $document['name']);
					}
				} else {
					$flash[] = 'Dokument ' . $document['name'] . ' se nepodařilo uložit na disk';
				}
			}
		}
		$this->Session->setFlash(implode('<br/>', $flash));
		$this->redirect($redirect);
	}
	
	function user_rename($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán dokument, který chcete přejmenovat');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		$document = $this->Document->find('first', array(
			'conditions' => array('Document.id' => $id),
			'contain' => array()
		));
		
		if (empty($document)) {
			$this->Session->setFlash('Požadovaný dokument neexistuje');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		$this->set('document', $document);
		
		if (isset($this->data)) {
			if ($this->Document->save($this->data)) {
				// prejmenuju na disku
				rename('files/documents/' . $document['Document']['name'], 'files/documents/' . $this->data['Document']['name']);				
				$this->Session->setFlash('Dokument byl upraven');
				if (!empty($document['Document']['imposition_id'])) {
					$redirect = array('controller' => 'impositions', 'action' => 'view', $document['Document']['imposition_id']);
				} elseif (!empty($document['Document']['business_partner_id'])) {
					$redirect = array('controller' => 'business_partners', 'action' => 'view', $document['Document']['business_partner_id']);
				} elseif (!empty($document['Document']['offer_id'])) {
					$redirect = array('controller' => 'offers', 'action' => 'view', $document['Document']['offer_id']);
				}
				$this->redirect($redirect);
			} else {
				$this->Session->setFlash('Dokument se nepodařilo přejmenovat, opravte chyby ve formuláři a opakujte prosím akci');
			}
		} else {
			$this->data = $document;
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán dokument, který chcete smazat');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		$document = $this->Document->find('first', array(
			'conditions' => array('Document.id' => $id),
			'contain' => array()
		));
		
		if (empty($document)) {
			$this->Session->setFlash('Požadovaný dokument neexistuje');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		if ($this->Document->delete($id)) {
			if (file_exists('files/documents/' . $document['Document']['name'])) {
				unlink('files/documents/' . $document['Document']['name']);
			}
			$this->Session->setFlash('Dokument byl odstraněn');
		} else {
			$this->Session->setFlash('Dokument se nepodařilo odstranit, opakujte prosím akci');
		}
		if (!empty($document['Document']['imposition_id'])) {
			$redirect = array('controller' => 'impositions', 'action' => 'view', $document['Document']['imposition_id']);
		} elseif (!empty($document['Document']['business_partner_id'])) {
			$redirect = array('controller' => 'business_partners', 'action' => 'view', $document['Document']['business_partner_id']);
		} elseif (!empty($document['Document']['offer_id'])) {
			$redirect = array('controller' => 'offers', 'action' => 'view', $document['Document']['offer_id']);
		}
		$this->redirect($redirect);
	}
}
?>