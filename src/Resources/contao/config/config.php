<?php

namespace LeadingSystems\Lscss4c;

$GLOBALS['TL_HOOKS']['generatePage'][] = array('LeadingSystems\Lscss4c\lscss4C_controller', 'insertLscss');