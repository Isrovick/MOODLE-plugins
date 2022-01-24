<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'mod/infoprofe:sendemails' => array(
    'captype' => 'read',
    'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        ),
    ),
    'mod/infoprofe:seeinform' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
            'archetypes' => array(
                'manager' => CAP_ALLOW
            ),
    ),
    'mod/infoprofe:changeinformstatus' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
            'archetypes' => array(
                'manager' => CAP_ALLOW
            ),
    ),
    'mod/infoprofe:enroll' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
            'archetypes' => array(
                'manager' => CAP_ALLOW
            ),
    ),
    'mod/infoprofe:notifyemail' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
            'archetypes' => array(
                'manager' => CAP_ALLOW
            ),
    ),
    'mod/infoprofe:sendconstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
            'archetypes' => array(
                'manager' => CAP_ALLOW
            ),
    ),
    'mod/infoprofe:seecage' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
            'archetypes' => array(
                'teacher' => CAP_ALLOW,
                'editingteacher' => CAP_ALLOW
            ),
    ),
    'mod/infoprofe:seeadmcage' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
            'archetypes' => array(
                'manager' => CAP_ALLOW
            ),
    ),
);