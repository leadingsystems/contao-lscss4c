<?php

namespace LeadingSystems\Lscss4c;
use function LeadingSystems\Helpers\ls_getFilePathFromVariableSources;

class lscss4C_controller extends \Controller {
	protected $str_folderUpPrefix = '_dup4_/';

	protected static $objInstance;

	protected function __construct() {
		parent::__construct();
	}

	final private function __clone() {
		
	}

	public static function getInstance() {
		if (!is_object(self::$objInstance)) {
			self::$objInstance = new self();
		}
		return self::$objInstance;
	}

	public function insertLsjs() {
		if (!$GLOBALS['lscss4c_globals']['lscss4c_lessFileToLoad']) {
			return;
		}

		die('FIXME: ' . __METHOD__);
	}

	public function getLayoutSettingsForGlobalUse(\PageModel $objPage, \LayoutModel $objLayout, \PageRegular $objPageRegular) {
		$GLOBALS['lscss4c_globals']['lscss4c_lessFileToLoad'] = ls_getFilePathFromVariableSources($objLayout->lscss4c_lessFileToLoad);

		$GLOBALS['lscss4c_globals']['lscss4c_debugMode'] = $objLayout->lscss4c_debugMode;

		$GLOBALS['lscss4c_globals']['lscss4c_noCache'] = $objLayout->lscss4c_noCache;

		$GLOBALS['lscss4c_globals']['lscss4c_noMinifier'] = $objLayout->lscss4c_noMinifier;
	}
}
