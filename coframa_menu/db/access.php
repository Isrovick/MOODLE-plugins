<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'mod/coframa_menu:modifymetadata' => array(
    'captype' => 'read',
    'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
        ),
    ),
    'mod/coframa_menu:inscribe' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
            'archetypes' => array(
            ),
    ),
);