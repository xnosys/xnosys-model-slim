<?php
	
	return function () {
		
		$prepareAndFilterLimit = function ($number) {
			$limit = intval($number);
			return ($limit > 0 && $limit < 1001) ? array(null, $limit) : array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
		};
		
		$prepareAndFilterIndex = function ($index, $columns) {
			{ // syntax
				if (!is_string($index) && !is_array($index)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (is_array($index)) {
					foreach ($index as $column) {
						if (!is_string($column)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
					}
				}
			}
			{ // prepare
				if (is_array($index)) {
					foreach ($index as &$column) {
						$column = preg_replace('/[^a-z0-9_]/', '', strtolower($column));
					} unset($column);
				} else {
					$index = preg_replace('/[^a-z0-9_]/', '', strtolower($index));
				}
			}
			{ // postprepare (must come after prepare)
				if (is_array($index)) {
					$index = array_unique($index);
				}
			}
			{ // logical validation
				if (is_string($index) && !strlen($index)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (is_array($index) && !count($index)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (is_string($index) && !in_array((string)$index, $columns)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (is_array($index)) {
					foreach ($index as $column) {
						if (!in_array((string)$column, $columns)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
					}
				}
			}
			return array(null, $index);
		};
		
		$prepareAndFilterKey = function ($string) {
			$key = preg_replace('/[^a-z0-9_]/', '', strtolower($string));
			return (strlen($key) && strlen(preg_replace('/[0-9]/', '', $key))) ? array(null, $key) : array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
		};
		
		$prepareAndFilterIndexes = function ($indexes, $columns) use ($prepareAndFilterKey, $prepareAndFilterIndex) {
			$array = array();
			foreach ($indexes as $key => $index) {
				list($error, $key) = $prepareAndFilterKey($key);
				if (!!$error) { return array($error); }
				list($error, $index) = $prepareAndFilterIndex($index, $columns);
				if (!!$error) { return array($error); }
				$array[$key] = $index;
			}
			if (!isset($array['primary'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			return array(null, $array);
		};
		
		$prepareAndFilterFieldDatetime = function ($field) {
			{ // formatting
				if (!isset($field['filter'])) { $field['filter'] = array(); }
				if (!isset($field['prepare'])) { $field['prepare'] = array(); }
			}
			{ // syntax
				if (isset($field['required']) && !is_bool($field['required']) && !in_array((string)$field['required'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['hidden']) && !is_bool($field['hidden']) && !in_array((string)$field['hidden'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['searchable']) && !is_bool($field['searchable']) && !in_array((string)$field['searchable'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['default']) && !is_string($field['default'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['default']) && !preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $field['default'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			{ // force typecast
				if (isset($field['required'])) { $field['required'] = (is_bool($field['required']) ? $field['required'] : in_array((string)$field['required'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
				if (isset($field['hidden'])) { $field['hidden'] = (is_bool($field['hidden']) ? $field['hidden'] : in_array((string)$field['hidden'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
				if (isset($field['searchable'])) { $field['searchable'] = (is_bool($field['searchable']) ? $field['searchable'] : in_array((string)$field['searchable'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			}
			{ // force default
				if (!isset($field['default'])) {
					$field['default'] = '0000-00-00 00:00:00';
				}
			}
			{ // clean
				if (isset($field['required']) && !$field['required']) {
					$field['required'] = null;
				}
				if (isset($field['hidden']) && !$field['hidden']) {
					$field['hidden'] = null;
				}
				if (isset($field['required']) && $field['required']) {
					$field['default'] = null;
				}
			}
			return array(null, $field);
		};
		
		$prepareAndFilterFieldNumber = function ($field) {
			{ // formatting
				if (!isset($field['filter'])) { $field['filter'] = array(); }
				if (!isset($field['prepare'])) { $field['prepare'] = array(); }
			}
			{ // syntax
				if (isset($field['required']) && !is_bool($field['required']) && !in_array((string)$field['required'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['hidden']) && !is_bool($field['hidden']) && !in_array((string)$field['hidden'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['searchable']) && !is_bool($field['searchable']) && !in_array((string)$field['searchable'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['default']) && !is_numeric($field['default'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['min']) && !is_numeric($field['filter']['min'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['max']) && !is_numeric($field['filter']['max'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['unsigned']) && !is_bool($field['filter']['unsigned']) && !in_array((string)$field['filter']['unsigned'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['values']) && !is_array($field['filter']['values'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['values'])) {
					foreach ($field['filter']['values'] as $value) {
						if (!is_numeric($value)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
					}
				}
				if (isset($field['prepare']['integer']) && !is_bool($field['prepare']['integer']) && !in_array((string)$field['prepare']['integer'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			{ // force typecast
				if (isset($field['required'])) { $field['required'] = (is_bool($field['required']) ? $field['required'] : in_array((string)$field['required'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
				if (isset($field['hidden'])) { $field['hidden'] = (is_bool($field['hidden']) ? $field['hidden'] : in_array((string)$field['hidden'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
				if (isset($field['searchable'])) { $field['searchable'] = (is_bool($field['searchable']) ? $field['searchable'] : in_array((string)$field['searchable'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
				if (isset($field['filter']['unsigned'])) { $field['filter']['unsigned'] = (is_bool($field['filter']['unsigned']) ? $field['filter']['unsigned'] : in_array((string)$field['filter']['unsigned'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
				if (isset($field['prepare']['integer'])) { $field['prepare']['integer'] = (is_bool($field['prepare']['integer']) ? $field['prepare']['integer'] : in_array((string)$field['prepare']['integer'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			}
			{ // prepare
				if (isset($field['default'])) {
					if (isset($field['prepare']['integer']) && $field['prepare']['integer']) {
						$field['default'] = intval($field['default']);
					} else {
						$field['default'] = floatval($field['default']);
					}
				}
				if (isset($field['filter']['values'])) {
					if (isset($field['prepare']['integer']) && $field['prepare']['integer']) {
						foreach ($field['filter']['values'] as &$value) {
							$value = intval($value);
						} unset($value);
					} else {
						foreach ($field['filter']['values'] as &$value) {
							$value = floatval($value);
						} unset($value);
					}
				}
			}
			{ // postprepare (must come after prepare)
				if (isset($field['filter']['values'])) {
					$field['filter']['values'] = array_unique($field['filter']['values']);
				}
			}
			{ // logical validation
				if (isset($field['filter']['min']) && isset($field['filter']['max']) && $field['filter']['min'] > $field['filter']['max']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['unsigned']) && $field['filter']['unsigned'] && isset($field['default']) && $field['default'] < 0) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['unsigned']) && $field['filter']['unsigned'] && isset($field['filter']['min']) && $field['filter']['min'] < 0) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['unsigned']) && $field['filter']['unsigned'] && isset($field['filter']['values'])) {
					foreach ($field['filter']['values'] as $value) {
						if ($value < 0) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
					}
				}
				if (isset($field['filter']['unsigned']) && $field['filter']['unsigned'] && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($field['filter']['max']) && $field['filter']['max'] > 4294967295) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['unsigned']) && $field['filter']['unsigned'] && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($field['filter']['values'])) {
					foreach ($field['filter']['values'] as $value) {
						if ($value > 4294967295) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
					}
				}
				if ((!isset($field['filter']['unsigned']) || !$field['filter']['unsigned']) && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($field['default']) && $field['default'] < -2147483648) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if ((!isset($field['filter']['unsigned']) || !$field['filter']['unsigned']) && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($field['default']) && $field['default'] > 2147483647) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if ((!isset($field['filter']['unsigned']) || !$field['filter']['unsigned']) && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($field['filter']['min']) && $field['filter']['min'] < -2147483648) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if ((!isset($field['filter']['unsigned']) || !$field['filter']['unsigned']) && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($field['filter']['max']) && $field['filter']['max'] > 2147483647) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if ((!isset($field['filter']['unsigned']) || !$field['filter']['unsigned']) && isset($field['prepare']['integer']) && $field['prepare']['integer'] && isset($field['filter']['values'])) {
					foreach ($field['filter']['values'] as $value) {
						if ($value < -2147483648) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
						if ($value > 2147483647) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
					}
				}
				if (isset($field['filter']['min']) && isset($field['default']) && $field['default'] < $field['filter']['min']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['min']) && isset($field['filter']['values'])) {
					foreach ($field['filter']['values'] as $value) {
						if ($value < $field['filter']['min']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
					}
				}
				if (isset($field['filter']['max']) && isset($field['default']) && $field['default'] > $field['filter']['max']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['max']) && isset($field['filter']['values'])) {
					foreach ($field['filter']['values'] as $value) {
						if ($value > $field['filter']['max']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
					}
				}
				if (isset($field['filter']['values']) && !count($field['filter']['values'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['values']) && isset($field['default']) && !in_array($field['default'], $field['filter']['values'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			{ // clean
				if (isset($field['required']) && !$field['required']) {
					$field['required'] = null;
				}
				if (isset($field['hidden']) && !$field['hidden']) {
					$field['hidden'] = null;
				}
				if (isset($field['required']) && $field['required']) {
					$field['default'] = null;
				}
			}
			return array(null, $field);
		};
		
		$prepareAndFilterFieldBoolean = function ($field) {
			{ // formatting
				if (!isset($field['filter'])) { $field['filter'] = array(); }
				if (!isset($field['prepare'])) { $field['prepare'] = array(); }
			}
			{ // syntax
				if (isset($field['required']) && !is_bool($field['required']) && !in_array((string)$field['required'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['hidden']) && !is_bool($field['hidden']) && !in_array((string)$field['hidden'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['searchable']) && !is_bool($field['searchable']) && !in_array((string)$field['searchable'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['default']) && !is_bool($field['default']) && !in_array((string)$field['default'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			{ // force typecast
				if (isset($field['required'])) { $field['required'] = (is_bool($field['required']) ? $field['required'] : in_array((string)$field['required'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
				if (isset($field['hidden'])) { $field['hidden'] = (is_bool($field['hidden']) ? $field['hidden'] : in_array((string)$field['hidden'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
				if (isset($field['searchable'])) { $field['searchable'] = (is_bool($field['searchable']) ? $field['searchable'] : in_array((string)$field['searchable'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
				if (isset($field['default'])) { $field['default'] = (is_bool($field['default']) ? $field['default'] : in_array((string)$field['default'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			}
			{ // clean
				if (isset($field['required']) && !$field['required']) {
					$field['required'] = null;
				}
				if (isset($field['hidden']) && !$field['hidden']) {
					$field['hidden'] = null;
				}
				if (isset($field['required']) && $field['required']) {
					$field['default'] = null;
				}
			}
			return array(null, $field);
		};
		
		$prepareAndFilterFieldString = function ($field) {
			{ // formatting
				if (!isset($field['filter'])) { $field['filter'] = array(); }
				if (!isset($field['prepare'])) { $field['prepare'] = array(); }
			}
			{ // presets
				if (isset($field['filter']['type'])) { $field['filter']['type'] = strtolower($field['filter']['type']); }
				if (isset($field['filter']['type']) && $field['filter']['type'] === 'email') {
					$field['filter']['regex'] = null;
					$field['filter']['minlen'] = 6;
					$field['filter']['maxlen'] = 255;
					$field['prepare']['lowercase'] = true;
					$field['prepare']['uppercase'] = null;
				}
				if (isset($field['filter']['type']) && $field['filter']['type'] === 'password') {
					$field['filter']['regex'] = null;
					$field['filter']['values'] = null;
					$field['prepare']['lowercase'] = null;
					$field['prepare']['uppercase'] = null;
				}
			}
			{ // syntax
				if (isset($field['required']) && !is_bool($field['required']) && !in_array((string)$field['required'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['hidden']) && !is_bool($field['hidden']) && !in_array((string)$field['hidden'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['searchable']) && !is_bool($field['searchable']) && !in_array((string)$field['searchable'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['default']) && !is_string($field['default'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['type']) && !in_array(strtolower((string)$field['filter']['type']), array('email','password','hash'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['regex']) && !is_string($field['filter']['regex'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['regex']) && !strlen($field['filter']['regex'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['minlen']) && !is_int($field['filter']['minlen'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['maxlen']) && !is_int($field['filter']['maxlen'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['values']) && !is_array($field['filter']['values'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['values'])) {
					foreach ($field['filter']['values'] as $value) {
						if (!is_string($value)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
					}
				}
				if (isset($field['prepare']['lowercase']) && !is_bool($field['prepare']['lowercase']) && !in_array((string)$field['prepare']['lowercase'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['prepare']['uppercase']) && !is_bool($field['prepare']['uppercase']) && !in_array((string)$field['prepare']['uppercase'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y','0','false','False','FALSE','off','Off','OFF','no','No','NO','n','N'))) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			}
			{ // force typecast
				if (isset($field['required'])) { $field['required'] = (is_bool($field['required']) ? $field['required'] : in_array((string)$field['required'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
				if (isset($field['hidden'])) { $field['hidden'] = (is_bool($field['hidden']) ? $field['hidden'] : in_array((string)$field['hidden'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
				if (isset($field['searchable'])) { $field['searchable'] = (is_bool($field['searchable']) ? $field['searchable'] : in_array((string)$field['searchable'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
				if (isset($field['prepare']['lowercase'])) { $field['prepare']['lowercase'] = (is_bool($field['prepare']['lowercase']) ? $field['prepare']['lowercase'] : in_array((string)$field['prepare']['lowercase'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
				if (isset($field['prepare']['uppercase'])) { $field['prepare']['uppercase'] = (is_bool($field['prepare']['uppercase']) ? $field['prepare']['uppercase'] : in_array((string)$field['prepare']['uppercase'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			}
			{ // prepare
				if (isset($field['default']) && isset($field['prepare']['lowercase']) && $field['prepare']['lowercase']) {
					$field['default'] = strtolower($field['default']);
				}
				if (isset($field['default']) && isset($field['prepare']['uppercase']) && $field['prepare']['uppercase']) {
					$field['default'] = strtoupper($field['default']);
				}
				if (isset($field['filter']['values']) && isset($field['prepare']['lowercase']) && $field['prepare']['lowercase']) {
					foreach ($field['filter']['values'] as &$value) {
						$value = strtolower($value);
					} unset($value);
				}
				if (isset($field['filter']['values']) && isset($field['prepare']['uppercase']) && $field['prepare']['uppercase']) {
					foreach ($field['filter']['values'] as &$value) {
						$value = strtoupper($value);
					} unset($value);
				}
			}
			{ // postprepare (must come after prepare)
				if (isset($field['filter']['values'])) {
					$field['filter']['values'] = array_unique($field['filter']['values']);
				}
			}
			{ // logical validation
				if (isset($field['filter']['minlen']) && $field['filter']['minlen'] < 0) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['maxlen']) && $field['filter']['maxlen'] < 1) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['maxlen']) && $field['filter']['maxlen'] > 65535) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['minlen']) && isset($field['filter']['maxlen']) && $field['filter']['minlen'] > $field['filter']['maxlen']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['minlen']) && isset($field['default']) && strlen($field['default']) < $field['filter']['minlen']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['maxlen']) && isset($field['default']) && strlen($field['default']) > $field['filter']['maxlen']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['values']) && !count($field['filter']['values'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['values']) && isset($field['default']) && !in_array((string)$field['default'], $field['filter']['values'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['values']) && isset($field['filter']['minlen'])) {
					foreach ($field['filter']['values'] as $value) {
						if (strlen($value) < $field['filter']['minlen']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
					}
				}
				if (isset($field['filter']['values']) && isset($field['filter']['maxlen'])) {
					foreach ($field['filter']['values'] as $value) {
						if (strlen($value) > $field['filter']['maxlen']) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
					}
				}
				if (isset($field['filter']['regex']) && isset($field['default']) && !preg_match($field['filter']['regex'], $field['default'])) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
				if (isset($field['filter']['regex']) && isset($field['filter']['values'])) {
					foreach ($field['filter']['values'] as $value) {
						if (!preg_match($field['filter']['regex'], $value)) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
					}
				}
			}
			{ // clean
				if (isset($field['required']) && !$field['required']) {
					$field['required'] = null;
				}
				if (isset($field['hidden']) && !$field['hidden']) {
					$field['hidden'] = null;
				}
				if (isset($field['required']) && $field['required']) {
					$field['default'] = null;
				}
			}
			return array(null, $field);
		};
		
		$prepareAndFilterField = function ($field) use ($prepareAndFilterFieldString, $prepareAndFilterFieldBoolean, $prepareAndFilterFieldNumber, $prepareAndFilterFieldDatetime) {
			switch (true) {
				case ($field['type'] === 'string'):
					return $prepareAndFilterFieldString($field);
				case ($field['type'] === 'boolean'):
					return $prepareAndFilterFieldBoolean($field);
				case ($field['type'] === 'number'):
					return $prepareAndFilterFieldNumber($field);
				case ($field['type'] === 'datetime'):
					return $prepareAndFilterFieldDatetime($field);
				default:
					return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
			}
		};
		
		$prepareAndFilterColumn = function ($string) {
			$column = preg_replace('/[^a-z0-9_]/', '', strtolower($string));
			return (strlen($column) && strlen(preg_replace('/[0-9]/', '', $column))) ? array(null, $column) : array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
		};
		
		$cleanFields = function ($fields) use (&$cleanFields) {
			$return = array();
			foreach ($fields as $column => $field) {
				if (is_array($field)) {
					$return[$column] = $cleanFields($field);
				} else {
					if ($field !== null) { $return[$column] = $field; }
				}
			}
			return $return;
		};
		
		$prepareAndFilterFields = function ($array) use ($cleanFields, $prepareAndFilterColumn, $prepareAndFilterField) {
			$fields = array();
			foreach ($array as $column => $field) {
				list($error, $column) = $prepareAndFilterColumn($column);
				if (!!$error) { return array($error); }
				list($error, $field) = $prepareAndFilterField($field);
				if (!!$error) { return array($error); }
				$fields[$column] = $field;
			}
			return array(null, $cleanFields($fields));
		};
		
		$prepareAndFilterCharset = function ($string) {
			$charset = preg_replace('/[^a-z0-9]/', '', strtolower($string));
			return (in_array((string)$charset, array('utf8'))) ? array(null, $charset) : array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
		};
		
		$prepareAndFilterEngine = function ($string) {
			$engine = strtolower($string);
			return (in_array((string)$engine, array('innodb','myisam'))) ? array(null, $engine) : array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
		};
		
		$prepareAndFilterName = function ($string) {
			$name = preg_replace('/[^a-z0-9_]/', '', strtolower($string));
			return (strlen($name) && strlen(preg_replace('/[0-9]/', '', $name))) ? array(null, $name) : array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
		};
		
		$castFieldDatetime = function ($field) {
			if (isset($field['required'])) { $field['required'] = (is_bool($field['required']) ? $field['required'] : in_array((string)$field['required'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['hidden'])) { $field['hidden'] = (is_bool($field['hidden']) ? $field['hidden'] : in_array((string)$field['hidden'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['searchable'])) { $field['searchable'] = (is_bool($field['searchable']) ? $field['searchable'] : in_array((string)$field['searchable'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['default'])) { $field['default'] = ''.$field['default']; }
			return array(null, $field);
		};
		
		$castFieldNumber = function ($field) {
			if (isset($field['required'])) { $field['required'] = (is_bool($field['required']) ? $field['required'] : in_array((string)$field['required'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['hidden'])) { $field['hidden'] = (is_bool($field['hidden']) ? $field['hidden'] : in_array((string)$field['hidden'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['searchable'])) { $field['searchable'] = (is_bool($field['searchable']) ? $field['searchable'] : in_array((string)$field['searchable'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['filter']['unsigned'])) { $field['filter']['unsigned'] = (is_bool($field['filter']['unsigned']) ? $field['filter']['unsigned'] : in_array((string)$field['filter']['unsigned'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['prepare']['integer'])) { $field['prepare']['integer'] = (is_bool($field['prepare']['integer']) ? $field['prepare']['integer'] : in_array((string)$field['prepare']['integer'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['filter']['min'])) { $field['filter']['min'] = ((isset($field['prepare']['integer']) && $field['prepare']['integer']) ? intval($field['filter']['min']) : floatval($field['filter']['min'])); }
			if (isset($field['filter']['max'])) { $field['filter']['max'] = ((isset($field['prepare']['integer']) && $field['prepare']['integer']) ? intval($field['filter']['max']) : floatval($field['filter']['max'])); }
			if (isset($field['default'])) { $field['default'] = ((isset($field['prepare']['integer']) && $field['prepare']['integer']) ? intval($field['default']) : floatval($field['default'])); }
			if (isset($field['filter']['values'])) {
				foreach ($field['filter']['values'] as &$value) {
					$value = ((isset($field['prepare']['integer']) && $field['prepare']['integer']) ? intval($value) : floatval($value));
				} unset($value);
			}
			return array(null, $field);
		};
		
		$castFieldBoolean = function ($field) {
			if (isset($field['required'])) { $field['required'] = (is_bool($field['required']) ? $field['required'] : in_array((string)$field['required'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['hidden'])) { $field['hidden'] = (is_bool($field['hidden']) ? $field['hidden'] : in_array((string)$field['hidden'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['searchable'])) { $field['searchable'] = (is_bool($field['searchable']) ? $field['searchable'] : in_array((string)$field['searchable'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['default'])) { $field['default'] = (is_bool($field['default']) ? $field['default'] : in_array((string)$field['default'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			return array(null, $field);
		};
		
		$castFieldString = function ($field) {
			if (isset($field['required'])) { $field['required'] = (is_bool($field['required']) ? $field['required'] : in_array((string)$field['required'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['hidden'])) { $field['hidden'] = (is_bool($field['hidden']) ? $field['hidden'] : in_array((string)$field['hidden'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['searchable'])) { $field['searchable'] = (is_bool($field['searchable']) ? $field['searchable'] : in_array((string)$field['searchable'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['filter']['minlen'])) { $field['filter']['minlen'] = intval($field['filter']['minlen']); }
			if (isset($field['filter']['maxlen'])) { $field['filter']['maxlen'] = intval($field['filter']['maxlen']); }
			if (isset($field['prepare']['lowercase'])) { $field['prepare']['lowercase'] = (is_bool($field['prepare']['lowercase']) ? $field['prepare']['lowercase'] : in_array((string)$field['prepare']['lowercase'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['prepare']['uppercase'])) { $field['prepare']['uppercase'] = (is_bool($field['prepare']['uppercase']) ? $field['prepare']['uppercase'] : in_array((string)$field['prepare']['uppercase'], array('1','true','True','TRUE','on','On','ON','yes','Yes','YES','y','Y'))); }
			if (isset($field['default'])) { $field['default'] = ''.$field['default']; }
			if (isset($field['filter']['values'])) {
				foreach ($field['filter']['values'] as &$value) {
					$value = ''.$value;
				} unset($value);
			}
			return array(null, $field);
		};
		
		$castField = function ($field) use ($castFieldString, $castFieldBoolean, $castFieldNumber, $castFieldDatetime) {
			switch (true) {
				case ($field['type'] === 'string'):
					return $castFieldString($field);
				case ($field['type'] === 'boolean'):
					return $castFieldBoolean($field);
				case ($field['type'] === 'number'):
					return $castFieldNumber($field);
				case ($field['type'] === 'datetime'):
					return $castFieldDatetime($field);
				default:
					return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__);
			}
		};
		
		$castFields = function ($array) use ($castField) {
			$fields = array();
			foreach ($array as $column => $field) {
				list($error, $field) = $castField($field);
				if (!!$error) { return array($error); }
				$fields[$column] = $field;
			}
			return array(null, $fields);
		};
		
		$loadCached = function ($json) use ($castFields) {
			$schema = json_decode($json, true);
			if (!$schema) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			list($error, $fields) = $castFields($schema['fields']);
			if (!!$error) { return array($error); }
			$schema['fields'] = $fields;
			return array(null, $schema);
		};
		
		$load = function ($json) use ($prepareAndFilterName, $prepareAndFilterEngine, $prepareAndFilterCharset, $prepareAndFilterFields, $prepareAndFilterIndexes, $prepareAndFilterLimit) {
			$schema = json_decode($json, true);
			if (!$schema) { return array('error: '.basename(__DIR__).'_'.basename(__FILE__, '.php').':'.__LINE__); }
			list($error, $name) = $prepareAndFilterName($schema['name']);
			if (!!$error) { return array($error); }
			list($error, $engine) = $prepareAndFilterEngine($schema['engine']);
			if (!!$error) { return array($error); }
			list($error, $charset) = $prepareAndFilterCharset($schema['charset']);
			if (!!$error) { return array($error); }
			list($error, $fields) = $prepareAndFilterFields($schema['fields']);
			if (!!$error) { return array($error); }
			list($error, $indexes) = $prepareAndFilterIndexes($schema['indexes'], array_keys($fields));
			if (!!$error) { return array($error); }
			list($error, $limit) = $prepareAndFilterLimit($schema['limit']);
			if (!!$error) { return array($error); }
			return array(null, array(
				'name' => $name,
				'engine' => $engine,
				'charset' => $charset,
				'fields' => $fields,
				'indexes' => $indexes,
				'limit' => $limit
			));
		};
		
		return array(
			'load' => $load,
			'loadCached' => $loadCached
		);
		
	};
	
?>