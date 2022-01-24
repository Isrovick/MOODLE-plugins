<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'mod/coframa_mail:sendadminemail' => array(
    'captype' => 'read',
    'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
        ),
    ),
    'mod/coframa_mail:sendteacheremail' => array(
    'captype' => 'read',
    'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
        ),
    ),
);