<?php

namespace LeadingSystems\LSCSS4CBundle\EventListener;

use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use function LeadingSystems\Helpers\ls_getFilePathFromVariableSources;

class GetPageLayoutListener
{
    public function getLayoutSettingsForGlobalUse(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        $GLOBALS['lscss4c_globals']['lscss4c_scssFileToLoad'] = ls_getFilePathFromVariableSources($layout->lscss4c_scssFileToLoad);

        $GLOBALS['lscss4c_globals']['lscss4c_debugMode'] = $layout->lscss4c_debugMode;

        $GLOBALS['lscss4c_globals']['lscss4c_noCache'] = $layout->lscss4c_noCache;

        $GLOBALS['lscss4c_globals']['lscss4c_noMinifier'] = $layout->lscss4c_noMinifier;

        $GLOBALS['lscss4c_globals']['lscss4c_pathsToConsiderForHash'] = trim($layout->lscss4c_pathsToConsiderForHash);

        $GLOBALS['lscss4c_globals']['layoutId'] = $layout->id;
    }
}