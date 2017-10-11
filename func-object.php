<?php
	
	return function () {
		
		$prepareAndFilterSearchParamDatetime = function ($properties, $field) {
			$sort = (isset($properties['sort'])) ? (intval($properties['sort']) > -1 ? 1 : -1) : null;
			$eq = isset($properties['eq']) ? ''.$properties['eq'] : null;
			$gt = isset($properties['gt']) ? ''.$properties['gt'] : null;
			$lt = isset($properties['lt']) ? ''.$properties['lt'] : null;
			if (isset($eq)) {
				$gt = $lt = null;
				$eq = (preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $eq)) ? $eq : null;
			}
			if (isset($gt)) {
				$gt = (preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $gt)) ? $gt : null;
			}
			if (isset($lt)) {
				$lt = (preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $lt)) ? $lt : null;
			}
			return array(null, array('sort' => $sort, 'eq' => $eq, 'gt' => $gt, 'lt' => $lt));
		};
		
		$prepareAndFilterSearchParamNumber = function ($properties, $field) {
			$sort = (isset($properties['sort'])) ? (intval($properties['sort']) > -1 ? 1 : -1) : null;
			$eq = isset($properties['eq']) ? (isset($field['prepare']['integer']) && $field['prepare']['integer']) ? intval($properties['eq']) : floatval($properties['eq']) : null;
			$gt = isset($properties['gt']) ? (isset($field['prepare']['integer']) && $field['prepare']['integer']) ? intval($properties['gt']) : floatval($properties['gt']) : null;
			$lt = isset($properties['lt']) ? (isset($field['prepare']['integer']) && $field['prepare']['integer']) ? intval($properties['lt']) : floatval($properties['lt']) : null;
			if (isset($eq)) {
				$gt = $lt = null;
				if (isset($field['filter']['unsigned']) && $field['filter']['unsigned'] && isset($eq) && $eq < 0) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if ((!isset($field['filter']['unsigned']) || !$field['filter']['unsigned']) && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($eq) && $eq < -2147483648) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if ((!isset($field['filter']['unsigned']) || !$field['filter']['unsigned']) && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($eq) && $eq > 2147483647) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['min']) && isset($eq) && $eq < $field['filter']['min']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['max']) && isset($eq) && $eq > $field['filter']['max']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['values']) && isset($eq) && !in_array($eq, $field['filter']['values'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			if (isset($gt)) {
				if (isset($field['filter']['unsigned']) && $field['filter']['unsigned'] && isset($gt) && $gt < 0) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if ((!isset($field['filter']['unsigned']) || !$field['filter']['unsigned']) && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($gt) && $gt < -2147483648) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if ((!isset($field['filter']['unsigned']) || !$field['filter']['unsigned']) && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($gt) && $gt > 2147483647) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['min']) && isset($gt) && $gt < $field['filter']['min']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['max']) && isset($gt) && $gt > $field['filter']['max']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			if (isset($lt)) {
				if (isset($field['filter']['unsigned']) && $field['filter']['unsigned'] && isset($lt) && $lt < 0) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if ((!isset($field['filter']['unsigned']) || !$field['filter']['unsigned']) && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($lt) && $lt < -2147483648) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if ((!isset($field['filter']['unsigned']) || !$field['filter']['unsigned']) && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($lt) && $lt > 2147483647) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['min']) && isset($lt) && $lt < $field['filter']['min']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['max']) && isset($lt) && $lt > $field['filter']['max']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			if (isset($gt) && isset($lt)) {
				return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
			}
			return array(null, array('sort' => $sort, 'eq' => $eq, 'gt' => $gt, 'lt' => $lt));
		};
		
		$prepareAndFilterSearchParamBoolean = function ($properties, $field) {
			$eq = isset($properties['eq']) ? (is_bool($properties['eq']) ? $properties['eq'] : in_array((string)$properties['eq'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))) : null;
			return array(null, array('eq' => $eq));
		};
		
		$prepareAndFilterSearchParamString = function ($properties, $field) {
			$sort = (isset($properties['sort'])) ? (intval($properties['sort']) > -1 ? 1 : -1) : null;
			$eq = isset($properties['eq']) ? ''.$properties['eq'] : null;
			$like = isset($properties['like']) ? ''.$properties['like'] : null;
			$gt = isset($properties['gt']) ? ''.$properties['gt'] : null;
			$lt = isset($properties['lt']) ? ''.$properties['lt'] : null;
			if (isset($eq)) {
				$like = null;
				if (isset($field['prepare']['lowercase']) && $field['prepare']['lowercase']) {
					$eq = strtolower($eq);
				}
				if (isset($field['prepare']['uppercase']) && $field['prepare']['uppercase']) {
					$eq = strtoupper($eq);
				}
				if (isset($field['filter']['regex']) && !preg_match($field['filter']['regex'], $eq)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			if (isset($like)) {
				if (isset($field['prepare']['lowercase']) && $field['prepare']['lowercase']) {
					$like = strtolower($like);
				}
				if (isset($field['prepare']['uppercase']) && $field['prepare']['uppercase']) {
					$like = strtoupper($like);
				}
			}
			if (isset($gt)) {
				if (isset($field['prepare']['lowercase']) && $field['prepare']['lowercase']) {
					$gt = strtolower($gt);
				}
				if (isset($field['prepare']['uppercase']) && $field['prepare']['uppercase']) {
					$gt = strtoupper($gt);
				}
			}
			if (isset($lt)) {
				if (isset($field['prepare']['lowercase']) && $field['prepare']['lowercase']) {
					$lt = strtolower($lt);
				}
				if (isset($field['prepare']['uppercase']) && $field['prepare']['uppercase']) {
					$lt = strtoupper($lt);
				}
			}
			return array(null, array('sort' => $sort, 'eq' => $eq, 'like' => $like, 'gt' => $gt, 'lt' => $lt));
		};
		
		$prepareAndFilterSearchParam = function ($properties, $field) use ($prepareAndFilterSearchParamString, $prepareAndFilterSearchParamBoolean, $prepareAndFilterSearchParamNumber, $prepareAndFilterSearchParamDatetime) {
			switch (true) {
				case ($field['type'] === 'string'):
					return $prepareAndFilterSearchParamString($properties, $field);
				case ($field['type'] === 'boolean'):
					return $prepareAndFilterSearchParamBoolean($properties, $field);
				case ($field['type'] === 'number'):
					return $prepareAndFilterSearchParamNumber($properties, $field);
				case ($field['type'] === 'datetime'):
					return $prepareAndFilterSearchParamDatetime($properties, $field);
				default:
					return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
			}
		};
		
		$prepareAndFilterSearchParams = function ($array, $fields) use ($prepareAndFilterSearchParam) {
			$params = array();
			foreach ($array as $column => $properties) {
				if (isset($fields[$column]) && isset($fields[$column]['searchable']) && $fields[$column]['searchable']) {
					list($error, $param) = $prepareAndFilterSearchParam($properties, $fields[$column]);
					if (!!$error) { return array($error); }
					$params[$column] = $param;
				}
			}
			return array(null, $params);
		};
		
		$prepareAndFilterSearchLimit = function ($value, $limit) {
			if (isset($value) && !is_numeric($value)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			if (isset($value)) { $value = intval($value); }
			if (isset($value) && $value < 1) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			if (isset($value) && $value > $limit) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			if (!isset($value)) { $value = $limit; }
			return array(null, $value);
		};
		
		$cleanParams = function ($params) use (&$cleanParams) {
			$return = array();
			foreach ($params as $column => $param) {
				if (is_array($param)) {
					$return[$column] = $cleanParams($param);
				} else {
					if ($param !== null) { $return[$column] = $param; }
				}
			}
			return $return;
		};
		
		$prepareAndFilterSearch = function ($limit, $offset, $params, $schema) use($cleanParams, $prepareAndFilterSearchLimit, $prepareAndFilterSearchParams) {
			list($error, $limit) = $prepareAndFilterSearchLimit($limit, $schema['limit']);
			if (!!$error) { return array($error); }
			$offset = intval(abs(is_numeric($offset) ? $offset : 0));
			list($error, $params) = $prepareAndFilterSearchParams($params, $schema['fields']);
			if (!!$error) { return array($error); }
			return array(null, $limit, $offset, $cleanParams($params));
		};
		
		$castAttr = function ($value, $field) {
			switch (true) {
				case ($field['type'] === 'string'):
					return array(null, ''.$value);
				case ($field['type'] === 'boolean'):
					return array(null, (is_bool($value) ? $value : in_array((string)$value, array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))));
				case ($field['type'] === 'number'):
					if (isset($field['prepare']['integer']) && $field['prepare']['integer']) {
						return array(null, intval($value));
					} else {
						return array(null, floatval($value));
					}
				case ($field['type'] === 'datetime'):
					return array(null, ''.$value);
				default:
					return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
			}
		};
		
		$castAttrs = function ($array, $fields) use ($castAttr) {
			$attrs = array();
			foreach ($array as $column => $value) {
				if (isset($fields[$column])) {
					list($error, $attr) = $castAttr($value, $fields[$column]);
					if (!!$error) { return array($error); }
					$attrs[$column] = $attr;
				}
			}
			return array(null, $attrs);
		};
		
		$requiredAttrs = function ($attrs, $fields) {
			foreach ($fields as $column => $field) {
				if (isset($field['required']) && $field['required'] && !isset($attrs[$column])) { return false; }
			}
			return true;
		};
		
		$defaultAttrs = function ($attrs, $fields) use ($requiredAttrs) {
			if (!$requiredAttrs($attrs, $fields)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			foreach ($fields as $column => $field) {
				if (!isset($attrs[$column])) {
					if (isset($field['default']) && $field['default'] !== null) {
						$attrs[$column] = $field['default'];
					} else {
						switch (true) {
							case ($field['type'] === 'string'):
								$attrs[$column] = '';
								break;
							case ($field['type'] === 'boolean'):
								$attrs[$column] = 0;
								break;
							case ($field['type'] === 'number'):
								$attrs[$column] = 0;
								break;
							case ($field['type'] === 'datetime'):
								$attrs[$column] = '0000-00-00 00:00:00';
								break;
							default:
								return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
						}
					}
				}
			}
			return array(null, $attrs);
		};
		
		$prepareAndFilterAttrDatetime = function ($value, $field) {
			{ // syntax
				if (isset($value) && !is_string($value)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($value) && !preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $value)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			{ // postprepare (must come after prepare)
				if ((!isset($field['required']) || (isset($field['required']) && !$field['required'])) && !isset($value) && isset($field['default'])) {
					$value = $field['default'];
				}
			}
			{ // logical validation
				if (isset($field['required']) && $field['required'] && !isset($value)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			return array(null, $value);
		};
		
		$prepareAndFilterAttrNumber = function ($value, $field) {
			{ // syntax
				if (isset($value) && !is_numeric($value)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			{ // prepare
				if (isset($value)) {
					if (isset($field['prepare']['integer']) && $field['prepare']['integer']) {
						$value = intval($value);
					} else {
						$value = floatval($value);
					}
				}
			}
			{ // postprepare (must come after prepare)
				if ((!isset($field['required']) || (isset($field['required']) && !$field['required'])) && !isset($value) && isset($field['default'])) {
					if (isset($field['prepare']['integer']) && $field['prepare']['integer']) {
						$value = intval($field['default']);
					} else {
						$value = floatval($field['default']);
					}
				}
			}
			{ // logical validation
				if (isset($field['required']) && $field['required'] && !isset($value)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['unsigned']) && $field['filter']['unsigned'] && isset($value) && $value < 0) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if ((!isset($field['filter']['unsigned']) || !$field['filter']['unsigned']) && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($value) && $value < -2147483648) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if ((!isset($field['filter']['unsigned']) || !$field['filter']['unsigned']) && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($value) && $value > 2147483647) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['min']) && isset($value) && $value < $field['filter']['min']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['max']) && isset($value) && $value > $field['filter']['max']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['values']) && isset($value) && !in_array($value, $field['filter']['values'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			return array(null, $value);
		};
		
		$prepareAndFilterAttrBoolean = function ($value, $field) {
			{ // syntax
				if (isset($value) && !is_bool($value) && !in_array((string)$value, array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			{ // force typecast
				if (isset($value)) { $value = (is_bool($value) ? $value : in_array((string)$value, array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			}
			{ // postprepare (must come after prepare)
				if ((!isset($field['required']) || (isset($field['required']) && !$field['required'])) && !isset($value) && isset($field['default'])) {
					$value = $field['default'];
				}
			}
			{ // logical validation
				if (isset($field['required']) && $field['required'] && !isset($value)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			return array(null, $value);
		};
		
		$functionPassword = function ($value) {
			return password_hash($value, PASSWORD_BCRYPT, array('cost'=>16.5));
		};
		
		$functionComparePassword = function ($value, $hash) {
			return password_verify($value, $hash);
		};
		
		$functionHash = function ($value) {
			return hash('sha512', $value);
		};
		
		$functionCompareHash = function ($value, $hash) use ($functionHash) {
			return $functionHash($value) === $hash;
		};
		
		$prepareAndFilterAttrString = function ($value, $field) use ($functionPassword, $functionHash) {
			{ // syntax
				if (isset($value) && !is_string($value)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			{ // prepare
				if (isset($value) && isset($field['prepare']['lowercase']) && $field['prepare']['lowercase']) {
					$value = strtolower($value);
				}
				if (isset($value) && isset($field['prepare']['uppercase']) && $field['prepare']['uppercase']) {
					$value = strtoupper($value);
				}
			}
			{ // postprepare (must come after prepare)
				if ((!isset($field['required']) || (isset($field['required']) && !$field['required'])) && (!isset($value) || !strlen($value)) && isset($field['default'])) {
					$value = $field['default'];
				}
			}
			{ // logical validation
				if (isset($field['required']) && $field['required'] && (!isset($value) || !strlen($value))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['type']) && $field['filter']['type'] === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['regex']) && isset($value) && !preg_match($field['filter']['regex'], $value)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['minlen']) && isset($value) && strlen($value) < $field['filter']['minlen']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['maxlen']) && isset($value) && strlen($value) > $field['filter']['maxlen']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['values']) && isset($value) && !in_array((string)$value, $field['filter']['values'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			{ // run type specific filter functions
				if (isset($field['filter']) && isset($field['filter']['type']) && $field['filter']['type'] === 'password') {
					$value = $functionPassword($value);
				}
				if (isset($field['filter']) && isset($field['filter']['type']) && $field['filter']['type'] === 'hash') {
					$value = $functionHash($value);
				}
			}
			return array(null, $value);
		};
		
		$prepareAndFilterAttr = function ($value, $field) use ($prepareAndFilterAttrString, $prepareAndFilterAttrBoolean, $prepareAndFilterAttrNumber, $prepareAndFilterAttrDatetime) {
			switch (true) {
				case ($field['type'] === 'string'):
					return $prepareAndFilterAttrString($value, $field);
				case ($field['type'] === 'boolean'):
					return $prepareAndFilterAttrBoolean($value, $field);
				case ($field['type'] === 'number'):
					return $prepareAndFilterAttrNumber($value, $field);
				case ($field['type'] === 'datetime'):
					return $prepareAndFilterAttrDatetime($value, $field);
				default:
					return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
			}
		};
		
		$prepareAndFilterAttrs = function ($array, $fields) use ($prepareAndFilterAttr) {
			$attrs = array();
			foreach ($array as $column => $value) {
				if (isset($fields[$column])) {
					list($error, $attr) = $prepareAndFilterAttr($value, $fields[$column]);
					if (!!$error) { return array($error); }
					$attrs[$column] = $attr;
				}
			}
			return array(null, $attrs);
		};
		
		$prepareQueryParamsForMySQL = function ($params) {
			$p = array();
			foreach ($params as $key => $value) {
				$p[$key] = ((is_bool($value)) ? ($value === true ? 1 : 0) : $value);
			}
			return $p;
		};
		
		$updateQuery = function ($attrs, $newAttrs, $schema) use ($prepareQueryParamsForMySQL) {
			$q = 'UPDATE `'.$schema['name'].'` SET '.(call_user_func(function ($columns) {
				for ($i = 0, $n = count($columns), $q = ''; $i < $n; $i++) {
					$q .= ', `'.$columns[$i].'` = :'.$columns[$i];
				} return substr($q, 2);
			}, array_keys($newAttrs))).' WHERE '.(call_user_func(function ($primary) {
				if (is_array($primary)) {
					for ($i = 0, $n = count($primary), $q = ''; $i < $n; $i++) {
						$q .= ' AND `'.$primary[$i].'` = :'.$i;
					} return substr($q, 5);
				} else {
					return '`'.$primary.'` = :0';
				}
			}, $schema['indexes']['primary'])).' LIMIT 1';
			$p = array();
			foreach ($newAttrs as $column => $value) {
				$p[':'.$column] = $value;
			}
			if (is_array($schema['indexes']['primary'])) {
				for ($i = 0, $n = count($schema['indexes']['primary']); $i < $n; $i++) {
					$p[':'.$i] = $attrs[$schema['indexes']['primary'][$i]];
				}
			} else {
				$p[':0'] = $attrs[$schema['indexes']['primary']];
			}
			return array(null, array('q' => $q, 'p' => $prepareQueryParamsForMySQL($p)));
		};
		
		$update = function ($attrs, $newAttrs, $schema) use ($prepareAndFilterAttrs, $updateQuery) {
			list($error, $newAttrs) = $prepareAndFilterAttrs($newAttrs, $schema['fields']);
			if (!!$error) { return array($error); }
			list($error, $query) = $updateQuery($attrs, $newAttrs, $schema);
			if (!!$error) { return array($error); }
			return array(null, ($newAttrs + $attrs), $query);
		};
		
		$searchQuery = function ($limit, $offset, $params, $schema) use ($prepareQueryParamsForMySQL) {
			$q = 'SELECT * FROM `'.$schema['name'].'` WHERE TRUE'.(call_user_func(function ($params) {
				$_ = '';
				$i = 0;
				foreach ($params as $column => $properties) {
					if (isset($properties['eq'])) {
						$_ .= ' AND `'.$column.'` = :'.$i;
						$i++;
					} elseif (isset($properties['like'])) {
						$_ .= ' AND `'.$column.'` LIKE :'.$i;
						$i++;
					}
					if (isset($properties['gt'])) {
						$_ .= ' AND `'.$column.'` > :'.$i;
						$i++;
					}
					if (isset($properties['lt'])) {
						$_ .= ' AND `'.$column.'` < :'.$i;
						$i++;
					}
				}
				return $_;
			}, $params))
			.(call_user_func(function ($params) {
				$_ = '';
				foreach ($params as $column => $properties) {
					if (isset($properties['sort'])) {
						$_ .= ', `'.$column.'` '.(intval($properties['sort']) > -1 ? 'ASC' : 'DESC');
					}
				}
				return strlen($_) ? ' ORDER BY '.substr($_, 2) : '';
			}, $params))
			.' LIMIT '.$offset.', '.$limit;
			$p = (call_user_func(function ($params) {
				$_ = array();
				$i = 0;
				foreach ($params as $column => $properties) {
					if (isset($properties['eq'])) {
						$_[':'.$i] = $properties['eq'];
						$i++;
					} elseif (isset($properties['like'])) {
						$_[':'.$i] = $properties['like'];
						$i++;
					}
					if (isset($properties['gt'])) {
						$_[':'.$i] = $properties['gt'];
						$i++;
					}
					if (isset($properties['lt'])) {
						$_[':'.$i] = $properties['lt'];
						$i++;
					}
				}
				return $_;
			}, $params));
			return array(null, array('q' => $q, 'p' => $prepareQueryParamsForMySQL($p)));
		};
		
		$search = function ($limit, $offset, $params, $schema) use ($prepareAndFilterSearch, $searchQuery) {
			list($error, $limit, $offset, $params) = $prepareAndFilterSearch($limit, $offset, $params, $schema);
			if (!!$error) { return array($error); }
			list($error, $query) = $searchQuery($limit, $offset, $params, $schema);
			if (!!$error) { return array($error); }
			return array(null, $query, $limit, $offset, $params);
		};
		
		$insertQuery = function ($attrs, $name) use ($prepareQueryParamsForMySQL) {
			return array(null, array('q' => 'INSERT INTO `'.$name.'` ('.(call_user_func(function ($columns) {
				for ($i = 0, $n = count($columns), $q = ''; $i < $n; $i++) {
					$q .= ', `'.$columns[$i].'`';
				} return substr($q, 2);
			}, array_keys($attrs))).') VALUES ('.(call_user_func(function ($columns) {
				for ($i = 0, $n = count($columns), $q = ''; $i < $n; $i++) {
					$q .= ', :'.$columns[$i];
				} return substr($q, 2);
			}, array_keys($attrs))).');', 'p' => $prepareQueryParamsForMySQL((call_user_func(function ($attrs) {
				$p = array();
				foreach ($attrs as $column => $value) {
					$p[':'.$column] = $value;
				}
				return $p;
			}, $attrs)))));
		};
		
		$insert = function ($attrs, $schema) use ($prepareAndFilterAttrs, $defaultAttrs, $insertQuery) {
			list($error, $attrs) = $prepareAndFilterAttrs($attrs, $schema['fields']);
			if (!!$error) { return array($error); }
			list($error, $attrs) = $defaultAttrs($attrs, $schema['fields']);
			if (!!$error) { return array($error); }
			list($error, $query) = $insertQuery($attrs, $schema['name']);
			if (!!$error) { return array($error); }
			return array(null, $attrs, $query);
		};
		
		$deleteQuery = function ($attrs, $schema) use ($prepareQueryParamsForMySQL) {
			$q = 'DELETE FROM `'.$schema['name'].'` WHERE '.(call_user_func(function ($primary) {
				if (is_array($primary)) {
					for ($i = 0, $n = count($primary), $q = ''; $i < $n; $i++) {
						$q .= ' AND `'.$primary[$i].'` = :'.$i;
					} return substr($q, 5);
				} else {
					return '`'.$primary.'` = :0';
				}
			}, $schema['indexes']['primary']));
			$p = array();
			if (is_array($schema['indexes']['primary'])) {
				for ($i = 0, $n = count($schema['indexes']['primary']); $i < $n; $i++) {
					$p[':'.$i] = $attrs[$schema['indexes']['primary'][$i]];
				}
			} else {
				$p[':0'] = $attrs[$schema['indexes']['primary']];
			}
			return array(null, array('q' => $q, 'p' => $prepareQueryParamsForMySQL($p)));
		};
		
		$delete = function ($attrs, $schema) use ($deleteQuery) {
			list($error, $query) = $deleteQuery($attrs, $schema);
			if (!!$error) { return array($error); }
			return array(null, $query);
		};
		
		return array(
			'delete' => $delete,
			'insert' => $insert,
			'update' => $update,
			'search' => $search,
			'verify' => array(
				'password' => $functionComparePassword,
				'hash' => $functionCompareHash
			),
			'prepareAndFilterAttr' => $prepareAndFilterAttr,
			'prepareAndFilterAttrs' => $prepareAndFilterAttrs,
			'castAttr' => $castAttr,
			'castAttrs' => $castAttrs,
			'prepareAndFilterSearch' => $prepareAndFilterSearch
		);
		
	};
	
?>