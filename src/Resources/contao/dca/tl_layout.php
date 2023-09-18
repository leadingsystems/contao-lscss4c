<?php

namespace LeadingSystems\Lscss4c;

$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] .= ';{lscss4c_legend},lscss4c_scssFileToLoad,lscss4c_debugMode,lscss4c_noCache,lscss4c_noMinifier,lscss4c_pathsToConsiderForHash';

$GLOBALS['TL_DCA']['tl_layout']['fields']['lscss4c_scssFileToLoad'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['lscss4c_scssFileToLoad'],
    'exclude'                 => true,
    'inputType'               => 'fileTree',
    'eval'                    => array ('multiple' => false, 'tl_class'=>'clr', 'files' => true, 'filesOnly' => true, 'fieldType' => 'radio', 'extensions' =>'scss'),
    'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['lscss4c_debugMode'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['lscss4c_debugMode'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array ('tl_class'=>'m12'),
    'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['lscss4c_noCache'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['lscss4c_noCache'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array ('tl_class'=>'m12'),
    'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['lscss4c_noMinifier'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['lscss4c_noMinifier'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array ('tl_class'=>'m12'),
    'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['lscss4c_pathsToConsiderForHash'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['lscss4c_pathsToConsiderForHash'],
    'exclude'                 => true,
    'inputType'               => 'textarea',
    'eval'                    => array('tl_class'=>'clr'),
    'sql'                     => "text NULL"
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['lscss4c_cacheHash'] = array
(
    'sql'                     => "varchar(255) NOT NULL default ''"
);