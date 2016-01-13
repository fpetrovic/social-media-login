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

    ),                      //extends Models and Controllers
    'events' => array(
        'onActivate' => 'socialMediaLoginEvents::onActivate',
    ),
    'templates' => array(

    ),
    'files' => array(
        'socialMediaLoginEvents'=>'spx/socialMediaLogin/events/socialMediaLoginEvents.php',
    ),
    'blocks' => array(

    ),
    'settings' => array(
        array('group' => 'main', 'name' => 'serviceMap', 'type' => 'select', 'value' => '0', 'constraints' => 'googleMap|osmMap', 'position' => 1),
    )
);


