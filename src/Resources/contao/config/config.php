<?php

namespace LeadingSystems\Lscss4c;

$GLOBALS['TL_HOOKS']['getPageLayout'][] = array('LeadingSystems\Lsjs4c\lscss4C_controller', 'getLayoutSettingsForGlobalUse');
$GLOBALS['TL_HOOKS']['generatePage'][] = array('LeadingSystems\Lsjs4c\lscss4C_controller', 'insertLsjs');