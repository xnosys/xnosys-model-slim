<?php
	
	return function () {
		
		extract(array_map(function ($import) { return $import(); }, array(
			'__object' => require(__DIR__.'/func-object.php'),
			'__schema' => require(__DIR__.'/func-schema.php'),
			'__table' => require(__DIR__.'/func-table.php')
		)));
		
		$load = function ($json) use ($__object, $__schema, $__table) {
			
			# - check if schema is chached / build and cache schema
			if (file_exists(__DIR__.'/.cache/'.hash('sha512', $json).'.json')) {
				list($error, $_schema) = $__schema['loadCached'](file_get_contents(__DIR__.'/.cache/'.hash('sha512', $json).'.json'));
				if (!!$error) { return array($error); }
			} else {
				list($error, $_schema) = $__schema['load']($json);
				if (!!$error) { return array($error); }
				file_put_contents(
					__DIR__.'/.cache/'.hash('sha512', $json).'.json',
					json_encode($_schema)
				);
			}
			
			$wrap = function ($attrs, $cast=true) use ($__object, $__schema, $__table, &$_schema) {
				$_attrs = $attrs;
				if ($cast) {
					list($error, $_attrs) = $__object['castAttrs']($_attrs, $_schema['fields']);
					if (!!$error) { return array($error); }
				}
				return array(
					'getAttrs' => function ($showHidden=false) use (&$_attrs, &$_schema) {
						if ($showHidden) {
							return $_attrs;
						} else {
							$attrs = array();
							foreach ($_schema['fields'] as $column => $field) {
								if (isset($_attrs[$column]) && !(isset($field['hidden']) && $field['hidden'])) {
									$attrs[$column] = $_attrs[$column];
								}
							}
							return $attrs;
						}
					},
					'getAttr' => function ($column) use (&$_attrs) {
						return (isset($_attrs[$column]) ? $_attrs[$column] : null);
					},
					'edit' => function ($newAttrs) use ($__object, &$_attrs, &$_schema) {
						list($error, $attrs, $query) = $__object['update']($_attrs, $newAttrs, $_schema);
						if (!!$error) { return array($error); }
						$_attrs = $attrs;
						return array(null, $query);
					},
					'destroy' => function () use ($__object, &$_attrs, &$_schema) {
						list($error, $query) = $__object['delete']($_attrs, $_schema);
						if (!!$error) { return array($error); }
						$_attrs = null;
						return array(null, null, $query);
					},
					'verify' => function ($attrs) use ($__object, &$_attrs, &$_schema) {
						foreach ($attrs as $column => $value) {
							if (!isset($_attrs[$column])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
							if (!isset($_schema['fields'][$column])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
							if (isset($_schema['fields'][$column]['filter']) && isset($_schema['fields'][$column]['filter']['type']) && in_array((string)$_schema['fields'][$column]['filter']['type'], array('password','hash'))) {
								if ($_schema['fields'][$column]['filter']['type'] === 'password' && !$__object['verify']['password']($value, $_attrs[$column])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
								if ($_schema['fields'][$column]['filter']['type'] === 'hash' && !$__object['verify']['hash']($value, $_attrs[$column])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
							} else {
								if ($value !== $_attrs[$column]) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
							}
						}
						return array(null);
					}
				);
			};
			
			return array(null, array(
				'new' => function ($attrs) use ($__object, &$_schema, $wrap) {
					list($error, $attrs, $query) = $__object['insert']($attrs, $_schema);
					if (!!$error) { return array($error); }
					return array(null, $wrap($attrs, false), $query);
				},
				'wrap' => $wrap,
				'table' => array(
					'getName' => function () use (&$_schema) {
						return $_schema['name'];
					},
					'getFields' => function () use (&$_schema) {
						return $_schema['fields'];
					},
					'create' => function () use ($__table, &$_schema) {
						list($error, $query) = $__table['create']($_schema);
						if (!!$error) { return array($error); }
						return array(null, $query);
					},
					'search' => function ($limit, $offset, $params) use ($__object, &$_schema) {
						list($error, $query, $limit, $offset, $params) = $__object['search']($limit, $offset, $params, $_schema);
						if (!!$error) { return array($error); }
						return array(null, $query, $limit, $offset, $params);
					}
				)
			));
			
		};
		
		return array(
			'load' => $load
		);
		
	};
	
?>