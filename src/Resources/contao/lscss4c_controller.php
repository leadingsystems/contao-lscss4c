<?php

namespace LeadingSystems\Lscss4c;

use Contao\System;
use LeadingSystems\LSCSS4CBundle\Compiler\Compiler;

class lscss4C_controller extends \Controller {
    protected static $objInstance;

    protected function __construct() {
        parent::__construct();
    }

    private function __clone() {
    }

    public static function getInstance() {
        if (!is_object(self::$objInstance)) {
            self::$objInstance = new self();
        }
        return self::$objInstance;
    }

    /**
     * @deprecated
     */
    public function getLscss($str_scssFileToLoad = '', $bln_noCache = true, $bln_noMinifier = true, $bln_debugMode = true) {
        trigger_deprecation('leadingsystems/contao-lscss4c', '1.0.2', 'Using "%s" has been deprecated and will no longer work in leadingsystems/contao-lscss4c bundle 2.0. Use "%s" directly instead.', __METHOD__, Compiler::class . '::compile');

        $str_pathToOutputFile = System::getContainer()->get('LeadingSystems\LSCSS4CBundle\Compiler\Compiler')->compile($str_scssFileToLoad, $bln_debugMode, $bln_noCache, $bln_noMinifier);

        return $str_pathToOutputFile;
    }
}
