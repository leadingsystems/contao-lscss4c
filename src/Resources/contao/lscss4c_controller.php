<?php

namespace LeadingSystems\Lscss4c;
use function LeadingSystems\Helpers\ls_getFilePathFromVariableSources;

class lscss4C_controller extends \Controller {
    protected static $objInstance;

    private $str_pathToOutputFile = 'assets/css/lscss4c.css';

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

	public function insertLscss() {
		if (!$GLOBALS['lscss4c_globals']['lscss4c_lessFileToLoad']) {
			return;
		}

        if (
            !file_exists(TL_ROOT . '/' . $this->str_pathToOutputFile)
            || $GLOBALS['lscss4c_globals']['lscss4c_noCache']
        ) {
            $str_filePath = $GLOBALS['lscss4c_globals']['lscss4c_lessFileToLoad'];
            $str_dirPath = \dirname($str_filePath);

            $arr_options = array
            (
                'strictMath' => false,
                'import_dirs' => array(TL_ROOT . '/' . $str_dirPath => $str_dirPath),
                'sourceMap' => $GLOBALS['lscss4c_globals']['lscss4c_debugMode'],
                'compress' => !$GLOBALS['lscss4c_globals']['lscss4c_noMinifier']
            );

            $obj_parser = new \Less_Parser();
            $obj_parser->SetOptions($arr_options);
            $obj_parser->parseFile(TL_ROOT . '/' . $str_filePath);
            file_put_contents(TL_ROOT . '/' . $this->str_pathToOutputFile, $obj_parser->getCss());
        }

        $GLOBALS['TL_CSS'][] = $this->str_pathToOutputFile;
    }

	public function getLayoutSettingsForGlobalUse(\PageModel $objPage, \LayoutModel $objLayout, \PageRegular $objPageRegular) {
		$GLOBALS['lscss4c_globals']['lscss4c_lessFileToLoad'] = ls_getFilePathFromVariableSources($objLayout->lscss4c_lessFileToLoad);

		$GLOBALS['lscss4c_globals']['lscss4c_debugMode'] = $objLayout->lscss4c_debugMode;

		$GLOBALS['lscss4c_globals']['lscss4c_noCache'] = $objLayout->lscss4c_noCache;

		$GLOBALS['lscss4c_globals']['lscss4c_noMinifier'] = $objLayout->lscss4c_noMinifier;
	}
}
