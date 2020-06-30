<?php

namespace LeadingSystems\Lscss4c;
use function LeadingSystems\Helpers\ls_getFilePathFromVariableSources;

class lscss4C_controller extends \Controller {
    protected static $objInstance;

    private $str_pathToOutputFile = 'assets/css/lscss4c_--inputFileHash--.css';
    private $str_pathToSourceMapFile = 'assets/css/lscss4c_--inputFileHash--.map';
    private $str_relativePathToSourceMapFile = 'lscss4c_--inputFileHash--.map'; // relative from the css file's perspective

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
		if (!$GLOBALS['lscss4c_globals']['lscss4c_scssFileToLoad']) {
			return;
		}

		$str_inputFileHash = md5($GLOBALS['lscss4c_globals']['lscss4c_scssFileToLoad']);
		$this->str_pathToOutputFile = str_replace('--inputFileHash--', $str_inputFileHash, $this->str_pathToOutputFile);
		$this->str_pathToSourceMapFile = str_replace('--inputFileHash--', $str_inputFileHash, $this->str_pathToSourceMapFile);
		$this->str_relativePathToSourceMapFile = str_replace('--inputFileHash--', $str_inputFileHash, $this->str_relativePathToSourceMapFile);

        if (
            !file_exists(TL_ROOT . '/' . $this->str_pathToOutputFile)
            || $GLOBALS['lscss4c_globals']['lscss4c_noCache']
        ) {
            $str_filePath = $GLOBALS['lscss4c_globals']['lscss4c_scssFileToLoad'];
            $str_dirPath = \dirname($str_filePath);

            $obj_scssCompiler = new \ScssPhp\ScssPhp\Compiler();
            $obj_scssCompiler->addImportPath(TL_ROOT . '/' . $str_dirPath);
            $obj_scssCompiler->setFormatter($GLOBALS['lscss4c_globals']['lscss4c_noMinifier'] ? \ScssPhp\ScssPhp\Formatter\Nested::class : \ScssPhp\ScssPhp\Formatter\Compressed::class);
            if ($GLOBALS['lscss4c_globals']['lscss4c_debugMode']) {
                $obj_scssCompiler->setLineNumberStyle(\ScssPhp\ScssPhp\Compiler::LINE_COMMENTS);
                $obj_scssCompiler->setSourceMap(\ScssPhp\ScssPhp\Compiler::SOURCE_MAP_FILE);
                $obj_scssCompiler->setSourceMapOptions([
                    // absolute path to write .map file
                    'sourceMapWriteTo'  => TL_ROOT . '/' . $this->str_pathToSourceMapFile,

                    // relative or full url to the above .map file
                    'sourceMapURL'      => $this->str_relativePathToSourceMapFile,

                    // (optional) relative or full url to the .css file
                    'sourceMapFilename' => $this->str_pathToOutputFile,

                    // partial path (server root) removed (normalized) to create a relative url
                    'sourceMapBasepath' => TL_ROOT,

                    // (optional) prepended to 'source' field entries for relocating source files
                    'sourceRoot'        => '/',
                ]);
            }

            file_put_contents(TL_ROOT . '/' . $this->str_pathToOutputFile, $obj_scssCompiler->compile(file_get_contents(TL_ROOT . '/' . $str_filePath)));
        }

        $GLOBALS['TL_CSS'][] = $this->str_pathToOutputFile;
    }

	public function getLayoutSettingsForGlobalUse(\PageModel $objPage, \LayoutModel $objLayout, \PageRegular $objPageRegular) {
		$GLOBALS['lscss4c_globals']['lscss4c_scssFileToLoad'] = ls_getFilePathFromVariableSources($objLayout->lscss4c_scssFileToLoad);

		$GLOBALS['lscss4c_globals']['lscss4c_debugMode'] = $objLayout->lscss4c_debugMode;

		$GLOBALS['lscss4c_globals']['lscss4c_noCache'] = $objLayout->lscss4c_noCache;

		$GLOBALS['lscss4c_globals']['lscss4c_noMinifier'] = $objLayout->lscss4c_noMinifier;
	}
}
