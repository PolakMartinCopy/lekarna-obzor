<?php
class CommentsController extends AppController {
	var $name = 'Comments';

	function admin_index() {
		$contain = array();
		$conditions = array();
		$order = array('created' => 'desc');
		
		$comments = $this->Comment->find('all', array(
			'conditions' => $conditions,
			'contain' => $contain,
			'order' => $order
		));
		
		for ( $i = 0; $i < count($comments); $i++ ){
			if ( $this->Comment->is_spam($comments[$i]['Comment']['body']) ){
				$comments[$i]['Comment']['subject'] .= ' <strong style="color:red;">SPAM!</strong>';
			}
		}
		
		$this->set('comments', $comments);
	}
	
	function admin_edit($id = null) {
		$this->Comment->id = $id;
		if (!isset($this->data)) {
			$this->data = $this->Comment->read(null, $id);
		} else {
			$this->data['Comment']['administrator_id'] = $this->Session->read('Administrator.id');
			if ($this->Comment->save($this->data, false)) {
				$this->Session->setFlash('Komentář byl upraven');
				$this->redirect(array('controller' => 'comments', 'action' => 'view', $id));
			} else {
				$this->Session->setFlash('Editace se nezdařila, zkuste to prosím znovu');
			}
		}
	}
	
	function admin_view($id = null){
		$comment = $this->Comment->read(null, $id);
		if ( !empty($comment) ){
			$this->set('comment', $comment);
		} else {
			$this->Session->setFlash('Neexistující komentář.');
			$this->redirect(array('controller' => 'comments', 'action' => 'index'), null, true);
		}
		
	}

	function admin_notify($id = null){
		$this->Comment->recursive = -1;
		$comment = $this->Comment->read(null, $id);
		if ( !empty($comment) ){
			if ( !empty($comment['Comment']['reply']) ){
				$this->Comment->notify_answer($comment);
				$this->Comment->save(array('sent' => '1'), false);
				$this->Session->setFlash('Odpověď byla zaslána dotazovateli.');
				$this->redirect(array('controller' => 'comments', 'action' => 'index'));
			} else {
				$this->Session->setFlash('U komentáře ještě není žádná odpověď, nelze ji proto odeslat dotazovateli.');
				$this->redirect(array('controller' => 'comments', 'action' => 'view', $id));
			}
		} else {
			$this->Session->setFlash('Neexistující komentář.');
			$this->redirect(array('controller' => 'comments', 'action' => 'view', $id));
		}
	}
	
	function admin_confirm($id = null) {
		if (isset($id)) {
			$this->Comment->id = $id;
			$comment = $this->Comment->read(null, $id);
			$comment['Comment']['confirmed'] = 1;
			if ($this->Comment->save($comment, false)) {
				$this->Session->setFlash('Komentář byl schválen.');
				$this->redirect(array('controller' => 'comments', 'action' => 'view', $id));
			} else {
				$this->Session->setFlash('Úprava se nezdařila, zkuste to prosím znovu.');
			}
		}
	}
	
	function admin_unconfirm($id = null) {
		if (isset($id)) {
			$this->Comment->id = $id;
			$comment = $this->Comment->read(null, $id);
			$comment['Comment']['confirmed'] = 0;
			if ($this->Comment->save($comment)) {
				$this->Session->setFlash('Komentář byl zakázán.');
				$this->redirect(array('controller' => 'comments', 'action' => 'view', $id));
			} else {
				$this->Session->setFlash('Úprava se nezdařila, zkuste to prosím znovu.');
			}
		}
	}
	
	function admin_delete($id = null) {
		if ($this->Comment->delete($id)) {
			$this->Session->setFlash('Komentář byl smazán');
		} else {
			$this->Session->setFlash('Komentář nemohl být smazán, zkuste to prosím později');
		}
		$this->redirect(array('controller' => 'comments', 'action' => 'index'));
	}
	
	function add() {
		if (isset($this->data)) {
			if ($this->Comment->is_spam($this->data['Comment']['body'])) {
				$this->Session->setFlash('Váš komentář obsahuje zakázaná slova a je proto považován za SPAM. Kometář nebyl uložen.');
			} else {
				$this->Comment->create();
				$this->data['Comment']['created'] = date('Y-m-d H:i:s');
				if ($this->Comment->save($this->data)) {
					$this->Session->setFlash('Váš kometář byl uložen ke zpracování. Po schválení se bude zobrazovat.');
				} else {
					$this->_persistValidation('Comment');
					$this->Session->setFlash('Chyba při ukládání, zkontrolujte formulář a zkuste to prosím znovu.');
				}
			}
			$this->redirect($this->data['Comment']['request_uri']);
		}
		$this->redirect('/');
	}

	/**
	 * metoda pro vlozeni komentare ajaxem
	 */
	function ajax_add() {
		$result = array(
			'success' => false,
			'message' => 'default message'
		);
		
		if (isset($_POST)) {
			if (isset($_POST['author']) && isset($_POST['email']) && isset($_POST['subject']) && isset($_POST['body']) && isset($_POST['productId'])) {
				$author = $_POST['author'];
				$email = $_POST['email'];
				$subject = $_POST['subject'];
				$body = $_POST['body'];
				$product_id = $_POST['productId'];

				if ($this->Comment->is_spam($body)) {
					$result['message'] = 'Váš komentář obsahuje zakázaná slova a je proto považován za SPAM. Kometář nebyl uložen.';
				} else {
					$comment = array(
						'Comment' => array(
							'author' => $author,
							'email' => $email,
							'subject' => $subject,
							'body' => $body,
							'product_id' => $product_id,
							'created' => date('Y-m-d H:i:s')
						)
					);
					$this->Comment->create();
					if ($this->Comment->save($comment)) {
						// komentar byl vlozen, notifikace adminu o novem dotazu
						//$this->Comment->notify_new_comment();
						
						$result['message'] = 'Váš kometář byl uložen ke zpracování. Po schválení se bude zobrazovat.';
						$result['success'] = true;
					} else {
						$result['message'] = 'Chyba při ukládání, zkontrolujte formulář a zkuste to prosím znovu.';
					}
				}
			} else {
				$result['message'] = 'Nejsou známá všechna potřebná formulářová pole.';
			}
		} else {
			$result['message'] = 'Neznám POST data';
		}
		
		echo json_encode($result);
		die();
	}
}
?>