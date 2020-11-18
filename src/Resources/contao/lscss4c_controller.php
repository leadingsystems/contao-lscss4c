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

		$bln_filesOrSettingsHaveChanged = $this->check_filesOrSettingsHaveChanged();

        if (
            !file_exists(TL_ROOT . '/' . $this->str_pathToOutputFile)
            || $GLOBALS['lscss4c_globals']['lscss4c_noCache']
            || $bln_filesOrSettingsHaveChanged
        ) {
            $str_filePath = $GLOBALS['lscss4c_globals']['lscss4c_scssFileToLoad'];
            $str_dirPath = \dirname($str_filePath);

            $obj_scssCompiler = new \ScssPhp\ScssPhp\Compiler();
            $obj_scssCompiler->addImportPath(TL_ROOT . '/' . $str_dirPath);

            /*
             * Because at the moment scssphp 1.4 conflicts with contao, we still have to support scssphp 1.3 but want
             * lscss4c to work with scssphp 1.4 as soon as it is available in contao
             */
            if (method_exists($obj_scssCompiler, 'setOutputStyle')) {
                // >= scssphp 1.4.0
                $obj_scssCompiler->setOutputStyle($GLOBALS['lscss4c_globals']['lscss4c_noMinifier'] ? \ScssPhp\ScssPhp\OutputStyle::EXPANDED : \ScssPhp\ScssPhp\OutputStyle::COMPRESSED);
            } else {
                // <= scssphp 1.3
                $obj_scssCompiler->setFormatter($GLOBALS['lscss4c_globals']['lscss4c_noMinifier'] ? \ScssPhp\ScssPhp\Formatter\Nested::class : \ScssPhp\ScssPhp\Formatter\Compressed::class);
            }

            if ($GLOBALS['lscss4c_globals']['lscss4c_debugMode']) {
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

		$GLOBALS['lscss4c_globals']['lscss4c_pathsToConsiderForHash'] = trim($objLayout->lscss4c_pathsToConsiderForHash);

		$GLOBALS['lscss4c_globals']['layoutId'] = $objLayout->id;
	}

	protected function check_filesOrSettingsHaveChanged() {
	    $obj_dbres_storedHash = \Database::getInstance()
            ->prepare("
                SELECT lscss4c_cacheHash
                FROM tl_layout
                WHERE id = ?
            ")
            ->limit(1)
            ->execute($GLOBALS['lscss4c_globals']['layoutId']);
	    
	    if (!$obj_dbres_storedHash->numRows) {
	        return true;
        }
	    
	    $str_storedHash = $obj_dbres_storedHash->first()->lscss4c_cacheHash;
	    
//        $float_utStart = microtime(true);

	    $arr_pathsToCheck = explode(',', $GLOBALS['lscss4c_globals']['lscss4c_pathsToConsiderForHash']);

	    $arr_pathHashes = [];

	    foreach ($arr_pathsToCheck as $str_pathToCheck) {
	        $arr_pathHashes[] = $this->hashDir(TL_ROOT. '/' . trim($str_pathToCheck));
        }

	    $str_currentHash = md5(implode('', $arr_pathHashes) . ($GLOBALS['lscss4c_globals']['lscss4c_debugMode'] ? '1' : '0') . ($GLOBALS['lscss4c_globals']['lscss4c_noMinifier'] ? '1' : '0') . $GLOBALS['lscss4c_globals']['lscss4c_pathsToConsiderForHash']);
//        $float_runtime = microtime(true) - $float_utStart;
//	    \LeadingSystems\Helpers\lsErrorLog('$str_currentHash', $str_currentHash, 'perm', 'var_dump');
//	    \LeadingSystems\Helpers\lsErrorLog('$float_runtime', $float_runtime, 'perm', 'var_dump');
	    
	    if ($str_currentHash != $str_storedHash) {
	        \Database::getInstance()
                ->prepare("
                    UPDATE tl_layout
                    SET lscss4c_cacheHash = ?
                    WHERE id = ?
                ")
                ->execute(
                    $str_currentHash,
                    $GLOBALS['lscss4c_globals']['layoutId']
                );
	        return true;
        }
    }

    protected function hashDir($str_dir) {
	    if (!is_dir($str_dir)) {
	        return '';
        }

	    $arr_fileHashes = [];
	    $obj_dir = dir($str_dir);

	    while (false !== ($str_file = $obj_dir->read())) {
	        if ($str_file == '.' || $str_file == '..') {
	            continue;
            }

	        $str_filePath = $str_dir . '/' . $str_file;

	        if (is_dir($str_filePath)) {
	            $arr_fileHashes[] = $this->hashDir($str_filePath);
            } else {
	            $arr_fileHashes[] = md5_file($str_filePath);
            }
        }

        $obj_dir->close();

	    return md5(implode('', $arr_fileHashes));
    }
}
