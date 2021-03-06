<?php
/**
 * @package		GCalendar Action Pack
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2013 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JLoader::import('joomla.database.table');

class  sportsmanagementTablejsmGCalendarAP extends JTable {

	public function __construct(&$db) {
		parent::__construct('#__sportsmanagement_gcalendarap', 'id', $db);
	}

	public function bind($array, $ignore = '') {
		if (isset($array['rules']) && is_array($array['rules'])) {
			$rules = new JRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}

	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_gcalendarap.event.'.(int) $this->$k;
	}

	protected function _getAssetParentId($table = null, $id = null) {
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_gcalendarap');
		return $asset->id;
	}
}