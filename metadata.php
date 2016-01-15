<?php
/**
 * Metadata version
 */
$sMetadataVersion = '1.0';

/**
 * Module information
 */
$aModule = array(
    'id' => 'socialMediaLogin',     //Must be the same as name of the directory
    'title' => 'Social media login for eshop',
    'description' => 'Module which enables users to login via social media accounts',
    'thumbnail' => 'picture.png',
    'version' => '1.0',
    'author' => 'Filip Petrovic <filip.petrovic@soprex.com>',
    'extend' => array(
        'oxuser'=>'spx/socialMediaLogin/models/spxUser',
        'oxcmp_user'=>'spx/socialMediaLogin/components/spxOxcmp_user'
    ),                      //extends Models and Controllers
    'events' => array(
        'onActivate' => 'socialMediaLoginEvents::onActivate',
    ),
    'templates' => array(

    ),
    'files' => array(
        'socialMediaLoginEvents'=>'spx/socialMediaLogin/events/socialMediaLoginEvents.php',
        'googleApi'=>'spx/socialMediaLogin/models/googleApi.php'
    ),
    'blocks' => array(

    ),
    'settings' => array(
        array('group' => 'socialMediaLogin', 'name' => 'facebookLogin', 'type' => 'bool', 'value' => 'true', 'position' => 1),
        array('group' => 'socialMediaLogin', 'name' => 'gmailLogin', 'type' => 'bool', 'value' => 'true', 'position' => 2),
        array('group' => 'socialMediaLogin', 'name' => 'googleApiKey', 'type' => 'str', 'value' => '', 'position' => 3),
        array('group' => 'socialMediaLogin', 'name' => 'googleId', 'type' => 'str', 'value' => '', 'position' => 4),
        array('group' => 'socialMediaLogin', 'name' => 'googleSecret', 'type' => 'str', 'value' => '', 'position' => 5),
        array('group' => 'socialMediaLogin', 'name' => 'twitterLogin', 'type' => 'bool', 'value' => 'true', 'position' => 6),
    )
);


