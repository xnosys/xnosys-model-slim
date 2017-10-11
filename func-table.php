<?php
	
	return function () {
		
		$createQueryIndex = function ($key, $index) {
			$query = '';
			switch (true) {
				case ($key === 'primary'):
					$query .= 'PRIMARY KEY ('; break;
				default:
					$query .= 'KEY `INDEX '.$key.'` ('; break;
			}
			if (is_array($index)) {
				for ($i = 0, $n = count($index); $i < $n; $i++) {
					$query .= (($i > 0) ? ',' : '').'`'.$index[$i].'`';
				}
			} else {
				$query .= '`'.$index.'`';
			}
			return array(null, $query.')');
		};
		
		$createQueryIndexes = function ($indexes) use ($createQueryIndex) {
			$query = '';
			$i = 0;
			foreach ($indexes as $key => $index) {
				list($error, $index) = $createQueryIndex($key, $index);
				if (!!$error) { return array($error); }
				$query .= ($i++ === 0 ? '' : ', ').$index;
			}
			return array(null, $query);
		};
		
		$createQueryFieldDatetime = function ($field) {
			return array(null, 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"');
		};
		
		$createQueryFieldNumber = function ($field) {
			if (isset($field['prepare']['integer']) && $field['prepare']['integer']) {
				return array(null, 'int(11)'.((isset($field['filter']['unsigned']) && $field['filter']['unsigned']) ? ' unsigned': '').' NOT NULL DEFAULT "0"');
			} else {
				return array(null, 'float(15,4)'.((isset($field['filter']['unsigned']) && $field['filter']['unsigned']) ? ' unsigned': '').' NOT NULL DEFAULT "0"');
			}
		};
		
		$createQueryFieldBoolean = function ($field) {
			return array(null, 'tinyint(1) unsigned NOT NULL DEFAULT "0"');
		};
		
		$createQueryFieldString = function ($field) {
			switch (true) {
				case (isset($field['filter']['type']) && $field['filter']['type'] === 'email'):
					return array(null, 'varchar(255) NOT NULL DEFAULT ""');
				case (isset($field['filter']['type']) && $field['filter']['type'] === 'password'):
					return array(null, 'varchar(60) NOT NULL DEFAULT ""');
				case (isset($field['filter']['type']) && $field['filter']['type'] === 'hash'):
					return array(null, 'varchar(128) NOT NULL DEFAULT ""');
				case (isset($field['filter']['maxlen']) && $field['filter']['maxlen'] < 256):
					return array(null, 'varchar('.$field['filter']['maxlen'].') NOT NULL DEFAULT ""');
				default:
					return array(null, 'text');
			}
		};
		
		$createQueryField = function ($field) use ($createQueryFieldString, $createQueryFieldBoolean, $createQueryFieldNumber, $createQueryFieldDatetime) {
			switch (true) {
				case ($field['type'] === 'string'):
					return $createQueryFieldString($field);
				case ($field['type'] === 'boolean'):
					return $createQueryFieldBoolean($field);
				case ($field['type'] === 'number'):
					return $createQueryFieldNumber($field);
				case ($field['type'] === 'datetime'):
					return $createQueryFieldDatetime($field);
				default:
					return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
			}
		};
		
		$createQueryFields = function ($fields) use ($createQueryField) {
			$query = '';
			foreach ($fields as $column => $field) {
				list($error, $field) = $createQueryField($field);
				if (!!$error) { return array($error); }
				$query .= '`'.$column.'` '.$field.', ';
			}
			return array(null, $query);
		};
		
		$createQuery = function ($schema) use ($createQueryFields, $createQueryIndexes) {
			list($error, $fields) = $createQueryFields($schema['fields']);
			if (!!$error) { return array($error); }
			list($error, $indexes) = $createQueryIndexes($schema['indexes']);
			if (!!$error) { return array($error); }
			return array(null, array('q' => 'CREATE TABLE IF NOT EXISTS `'.$schema['name'].'` ('.$fields.$indexes.') ENGINE='.$schema['engine'].' DEFAULT CHARSET='.$schema['charset'].';', 'p' => array()));
		};
		
		$create = function ($schema) use ($createQuery) {
			list($error, $query) = $createQuery($schema);
			if (!!$error) { return array($error); }
			return array(null, $query);
		};
		
		return array(
			'create' => $create
		);
		
	};
	
?>