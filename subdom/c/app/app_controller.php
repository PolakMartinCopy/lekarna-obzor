<?php
class AppController extends Controller {
	
	var $components = array('Auth', 'Session', 'Acl');
	
	var $monthNames = array(1 => 'Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec');
	
	var $user;
	
	function beforeFilter() {
		$this->disableCache();
		
		if ($_SERVER['REQUEST_URI'] == '/') {
			$this->redirect('/user/impositions/');
		}
		
		$this->Auth->authorize = 'actions';
		$this->Auth->loginAction = '/user/users/login';
		$this->Auth->loginRedirect = array('controller' => 'impositions', 'action' => 'index', 'prefix' => 'user');
		$this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login');

		$this->Auth->actionPath = 'controllers/';
		
		$this->Auth->authenticate = ClassRegistry::init('User');
		
		$this->Auth->fields = array('username' => 'login', 'password' => 'password');

		$this->Auth->loginError = 'Přihlášení selhalo. Neplatné heslo nebo přihlašovací jméno.';
		$this->Auth->authError = 'Neoprávněný přístup!';
		
		$this->user = $this->Auth->user();
		
		$this->set('acl', $this->Acl);
	}
	
	function beforeRender() {
		$this->set('logged_in_user', $this->user);
	}
	
	function user_xls_export() {
		$model = $this->modelNames[0];
		$data = unserialize($this->data['CSV']['data']);

		$export_fields = unserialize($this->data['CSV']['fields']);
		$this->$model->xls_export($data, $export_fields);
		$this->redirect('/' . $this->$model->export_file);
	}
	
	/**
	 * Handles automatic pagination of model records.
	 *
	 * @param mixed $object Model to paginate (e.g: model instance, or 'Model', or 'Model.InnerModel')
	 * @param mixed $scope Conditions to use while paginating
	 * @param array $whitelist List of allowed options for paging
	 * @return array Model query results
	 * @access public
	 * @link http://book.cakephp.org/view/1232/Controller-Setup
	 */
	function paginate($object = null, $scope = array(), $whitelist = array()) {
		if (is_array($object)) {
			$whitelist = $scope;
			$scope = $object;
			$object = null;
		}
		$assoc = null;
	
		if (is_string($object)) {
			$assoc = null;
			if (strpos($object, '.')  !== false) {
				list($object, $assoc) = pluginSplit($object);
			}
	
			if ($assoc && isset($this->{$object}->{$assoc})) {
				$object =& $this->{$object}->{$assoc};
			} elseif (
					$assoc && isset($this->{$this->modelClass}) &&
					isset($this->{$this->modelClass}->{$assoc}
			)) {
				$object =& $this->{$this->modelClass}->{$assoc};
			} elseif (isset($this->{$object})) {
				$object =& $this->{$object};
			} elseif (
					isset($this->{$this->modelClass}) && isset($this->{$this->modelClass}->{$object}
			)) {
				$object =& $this->{$this->modelClass}->{$object};
			}
		} elseif (empty($object) || $object === null) {
			if (isset($this->{$this->modelClass})) {
				$object =& $this->{$this->modelClass};
			} else {
				$className = null;
				$name = $this->uses[0];
				if (strpos($this->uses[0], '.') !== false) {
					list($name, $className) = explode('.', $this->uses[0]);
				}
				if ($className) {
					$object =& $this->{$className};
				} else {
					$object =& $this->{$name};
				}
			}
		}
	
		if (!is_object($object)) {
			trigger_error(sprintf(
			__('Controller::paginate() - can\'t find model %1$s in controller %2$sController',
			true
			), $object, $this->name
			), E_USER_WARNING);
			return array();
		}

		$options = array_merge($this->params, $this->params['url'], $this->passedArgs);

		if (isset($this->paginate[$object->alias])) {
			$defaults = $this->paginate[$object->alias];
		} else {
			$defaults = $this->paginate;
		}
	
		if (isset($options['show'])) {
			$options['limit'] = $options['show'];
		}

		if (isset($options['sort']) && !empty($options['sort'])) {
			$direction = null;
			if (isset($options['direction']) && !empty($options['direction'])) {
				$direction = strtolower($options['direction']);
			}
			if ($direction != 'asc' && $direction != 'desc') {
				$direction = 'asc';
			}
			$options['order'] = array($options['sort'] => $direction);
		}

		if (!empty($options['order']) && is_array($options['order'])) {
			$alias = $object->alias ;
			$key = $field = key($options['order']);
	
			if (strpos($key, '.') !== false) {
				list($alias, $field) = explode('.', $key);
			}
			$value = $options['order'][$key];
			unset($options['order'][$key]);

			if ($object->hasField($field)) {
				$options['order'][$alias . '.' . $field] = $value;
				if (in_array($alias, array('DeliveryNote', 'Sale', 'Transaction')) && $field == 'date') {
					$options['order'][$alias . '.time'] = $value;
				}
			} elseif ($object->hasField($field, true)) {
				$options['order'][$field] = $value;
			} elseif (isset($object->{$alias}) && $object->{$alias}->hasField($field)) {
				$options['order'][$alias . '.' . $field] = $value;
				// aby radilo i podle celkovych nakladu spocitanych pomoci sum v sql dotazu
			} elseif ($field == 'celkem') {
				$options['order']['celkem'] = $value;
			} elseif ($field == 'full_name') {
				$options['order']['full_name'] = $value;
			}
		}

		$vars = array('fields', 'order', 'limit', 'page', 'recursive');
		$keys = array_keys($options);
		$count = count($keys);
	
		for ($i = 0; $i < $count; $i++) {
			if (!in_array($keys[$i], $vars, true)) {
				unset($options[$keys[$i]]);
			}
			if (empty($whitelist) && ($keys[$i] === 'fields' || $keys[$i] === 'recursive')) {
				unset($options[$keys[$i]]);
			} elseif (!empty($whitelist) && !in_array($keys[$i], $whitelist)) {
				unset($options[$keys[$i]]);
			}
		}
		$conditions = $fields = $order = $limit = $page = $recursive = null;
	
		if (!isset($defaults['conditions'])) {
			$defaults['conditions'] = array();
		}
	
		$type = 'all';
	
		if (isset($defaults[0])) {
			$type = $defaults[0];
			unset($defaults[0]);
		}
	
		$options = array_merge(array('page' => 1, 'limit' => 20), $defaults, $options);
		$options['limit'] = (int) $options['limit'];
		if (empty($options['limit']) || $options['limit'] < 1) {
			$options['limit'] = 1;
		}
		extract($options);
	
		if (is_array($scope) && !empty($scope)) {
			$conditions = array_merge($conditions, $scope);
		} elseif (is_string($scope)) {
			$conditions = array($conditions, $scope);
		}
		if ($recursive === null) {
			$recursive = $object->recursive;
		}
	
		$extra = array_diff_key($defaults, compact(
				'conditions', 'fields', 'order', 'limit', 'page', 'recursive'
		));
		if ($type !== 'all') {
			$extra['type'] = $type;
		}
	
		if (method_exists($object, 'paginateCount')) {
			$count = $object->paginateCount($conditions, $recursive, $extra);
		} else {
			$parameters = compact('conditions');
			if ($recursive != $object->recursive) {
				$parameters['recursive'] = $recursive;
			}
			$count = $object->find('count', array_merge($parameters, $extra));
		}
		$pageCount = intval(ceil($count / $limit));
	
		if ($page === 'last' || $page >= $pageCount) {
			$options['page'] = $page = $pageCount;
		} elseif (intval($page) < 1) {
			$options['page'] = $page = 1;
		}
		$page = $options['page'] = (integer)$page;
	
		if (method_exists($object, 'paginate')) {
			$results = $object->paginate(
					$conditions, $fields, $order, $limit, $page, $recursive, $extra
			);
		} else {
			$parameters = compact('conditions', 'fields', 'order', 'limit', 'page');
			if ($recursive != $object->recursive) {
				$parameters['recursive'] = $recursive;
			}
			$results = $object->find($type, array_merge($parameters, $extra));
		}
		$paging = array(
				'page'		=> $page,
				'current'	=> count($results),
				'count'		=> $count,
				'prevPage'	=> ($page > 1),
				'nextPage'	=> ($count > ($page * $limit)),
				'pageCount'	=> $pageCount,
				'defaults'	=> array_merge(array('limit' => 20, 'step' => 1), $defaults),
				'options'	=> $options
		);
		$this->params['paging'][$object->alias] = $paging;
	
		if (!in_array('Paginator', $this->helpers) && !array_key_exists('Paginator', $this->helpers)) {
			$this->helpers[] = 'Paginator';
		}
		return $results;
	}
	
}
?>