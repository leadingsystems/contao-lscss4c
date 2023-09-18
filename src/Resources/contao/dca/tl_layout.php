<?php

namespace LeadingSystems\Lscss4c;


$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] .= ';{lscss4c_legend},lscss4c_scssFileToLoad,lscss4c_debugMode,lscss4c_noCache,lscss4c_noMinifier,lscss4c_pathsToConsiderForHash';

$GLOBALS['TL_DCA']['tl_layout']['fields']['lscss4c_scssFileToLoad'] = array(
    'sql'                     => "blob NULL",
	'label' => &$GLOBALS['TL_LANG']['tl_layout']['lscss4c_scssFileToLoad'],
	'exclude' => true,
	'inputType' => 'fileTree',
	'eval' => array(
		'multiple' => false,
		'tl_class'=>'clr',
		'files' => true,
		'filesOnly' => true,
		'fieldType' => 'radio',
        'extensions'=>'scss'
	)
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['lscss4c_debugMode'] = array(
    'sql'                     => "char(1) NOT NULL default ''",
    'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['lscss4c_debugMode'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'m12')
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['lscss4c_noCache'] = array(
    'sql'                     => "char(1) NOT NULL default ''",
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['lscss4c_noCache'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'m12')
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['lscss4c_noMinifier'] = array(
    'sql'                     => "char(1) NOT NULL default ''",
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['lscss4c_noMinifier'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'m12')
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['lscss4c_pathsToConsiderForHash'] = array(
    'sql'                     => "text NULL",
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['lscss4c_pathsToConsiderForHash'],
	'exclude'                 => true,
	'inputType'               => 'textarea',
	'eval'                    => array('tl_class'=>'clr')
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['lscss4c_cacheHash'] = array(
    'sql'                     => "varchar(255) NOT NULL default ''",
);