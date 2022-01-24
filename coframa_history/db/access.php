<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'mod/coframa_history:generatehistory' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
            'archetypes' => array(
            ),
        ),
);