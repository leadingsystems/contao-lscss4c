<?php

namespace LeadingSystems\LSCSS4CBundle\Compiler;


use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\HttpFoundation\RequestStack;

class Compiler
{
    private string $projectDir;

    private $str_pathToOutputFile = 'assets/css/lscss4c_--inputFileHash--.css';
    private $str_pathToSourceMapFile = 'assets/css/lscss4c_--inputFileHash--.map';
    private $str_relativePathToSourceMapFile = 'lscss4c_--inputFileHash--.map'; // relative from the css file's perspective

    private RequestStack $requestStack;
    private ScopeMatcher $scopeMatcher;

    public function __construct(RequestStack $requestStack, ScopeMatcher $scopeMatcher, string $projectDir)
    {
        $this->projectDir = $projectDir;
        $this->requestStack = $requestStack;
        $this->scopeMatcher = $scopeMatcher;
    }

    public function compile($scssFileToLoad = null, $debugMode = null, $noCache = null, $noMinifier = null, $pathsToConsiderForHash = null): string
    {
        $this->handleCompileArguments($scssFileToLoad, $debugMode, $noCache, $noMinifier, $pathsToConsiderForHash);

        if (!$GLOBALS['lscss4c_globals']['lscss4c_scssFileToLoad']) {
            return '';
        }

        $str_inputFileHash = md5($GLOBALS['lscss4c_globals']['lscss4c_scssFileToLoad']);
        $this->str_pathToOutputFile = str_replace('--inputFileHash--', $str_inputFileHash, $this->str_pathToOutputFile);
        $this->str_pathToSourceMapFile = str_replace('--inputFileHash--', $str_inputFileHash, $this->str_pathToSourceMapFile);
        $this->str_relativePathToSourceMapFile = str_replace('--inputFileHash--', $str_inputFileHash, $this->str_relativePathToSourceMapFile);

        $bln_filesOrSettingsHaveChanged = $this->check_filesOrSettingsHaveChanged();

        if (
            !file_exists($this->projectDir . '/' . $this->str_pathToOutputFile)
            || $GLOBALS['lscss4c_globals']['lscss4c_noCache']
            || $bln_filesOrSettingsHaveChanged
        ) {
            $str_filePath = $GLOBALS['lscss4c_globals']['lscss4c_scssFileToLoad'];
            $str_dirPath = \dirname($str_filePath);

            $obj_scssCompiler = new \ScssPhp\ScssPhp\Compiler();
            $obj_scssCompiler->addImportPath($this->projectDir . '/' . $str_dirPath);
            $obj_scssCompiler->addImportPath($this->projectDir);

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
                    'sourceMapWriteTo'  => $this->projectDir . '/' . $this->str_pathToSourceMapFile,

                    // relative or full url to the above .map file
                    'sourceMapURL'      => $this->str_relativePathToSourceMapFile,

                    // (optional) relative or full url to the .css file
                    'sourceMapFilename' => $this->str_pathToOutputFile,

                    // partial path (server root) removed (normalized) to create a relative url
                    'sourceMapBasepath' => $this->projectDir,

                    // (optional) prepended to 'source' field entries for relocating source files
                    'sourceRoot'        => '/',
                ]);
            }

            /*
             * @toDo: replace deprecated function compile()
             */
            file_put_contents($this->projectDir . '/' . $this->str_pathToOutputFile, $obj_scssCompiler->compile(file_get_contents($this->projectDir . '/' . $str_filePath)));
        }

        return $this->str_pathToOutputFile;
    }

    private function handleCompileArguments($scssFileToLoad, $debugMode, $noCache, $noMinifier, $pathsToConsiderForHash): void
    {
        if ($scssFileToLoad !== null || !isset($GLOBALS['lscss4c_globals']['lscss4c_scssFileToLoad'])) {
            $GLOBALS['lscss4c_globals']['lscss4c_scssFileToLoad'] = $scssFileToLoad;
        }

        if ($debugMode !== null || !isset($GLOBALS['lscss4c_globals']['lscss4c_debugMode'])) {
            $GLOBALS['lscss4c_globals']['lscss4c_debugMode'] = $debugMode;
        }

        if ($noCache !== null || !isset($GLOBALS['lscss4c_globals']['lscss4c_noCache'])) {
            $GLOBALS['lscss4c_globals']['lscss4c_noCache'] = $noCache;
        }

        if ($noMinifier !== null || !isset($GLOBALS['lscss4c_globals']['lscss4c_noMinifier'])) {
            $GLOBALS['lscss4c_globals']['lscss4c_noMinifier'] = $noMinifier;
        }

        if ($pathsToConsiderForHash !== null || !isset($GLOBALS['lscss4c_globals']['lscss4c_pathsToConsiderForHash'])) {
            $GLOBALS['lscss4c_globals']['lscss4c_pathsToConsiderForHash'] = $pathsToConsiderForHash;
        }
    }

    private function check_filesOrSettingsHaveChanged(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($this->scopeMatcher->isBackendRequest($request))
        {
            /*
             * Since we don't have a layout record when lscss4c is used in the backend, storing the cacheHash
             * and checking for changed files or settings isn't possible. As a fallback this function returns false
             * in this case which means that if the cache is being used it won't be refreshed even if files or
             * settings have changed. This means that in the backend we either use the cache or we don't. There
             * is no automatic cache refresh.
             *
             * Of course, this is something that could be improved in the future if it seems necessary.
             */
            return false;
        }

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

        $arr_pathsToCheck = explode(',', $GLOBALS['lscss4c_globals']['lscss4c_pathsToConsiderForHash']);

        $arr_pathHashes = [];

        foreach ($arr_pathsToCheck as $str_pathToCheck) {
            $str_pathToCheck = trim($str_pathToCheck);
            if (empty($str_pathToCheck)) {
                continue;
            }
            $arr_pathHashes[] = $this->hashDir($this->projectDir. '/' . $str_pathToCheck);
        }

        $str_currentHash = md5(implode('', $arr_pathHashes) . ($GLOBALS['lscss4c_globals']['lscss4c_debugMode'] ? '1' : '0') . ($GLOBALS['lscss4c_globals']['lscss4c_noMinifier'] ? '1' : '0') . $GLOBALS['lscss4c_globals']['lscss4c_pathsToConsiderForHash']);

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
        } else {
            return false;
        }
    }

    protected function hashDir($str_dir): string
    {
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
                $arr_pathParts = pathinfo($str_filePath);
                // only consider scss files
                if ($arr_pathParts['extension'] === 'scss') {
                    $arr_fileHashes[] = md5_file($str_filePath);
                }
            }
        }

        $obj_dir->close();

        return md5(implode('', $arr_fileHashes));
    }
}