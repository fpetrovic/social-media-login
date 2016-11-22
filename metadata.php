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
    'author' => 'Soprex <fpetrovic15@soprex.com>',
    'extend' => array(
        'oxuser' => 'spx/socialMediaLogin/models/spxUser',
        'oxcmp_user' => 'spx/socialMediaLogin/components/spxOxcmp_user',
        'oxwServiceMenu' => 'spx/socialMediaLogin/controllers/spxServiceMenu',
    ), //extends Models and Controllers
    'events' => array(
        'onActivate' => 'socialMediaLoginEvents::onActivate',
    ),
    'templates' => array(
        'serviceMenu.tpl' => 'spx/socialMediaLogin/views/tpl/widget/header/servicemenu.tpl',
        'spxLogin.tpl' => 'spx/socialMediaLogin/views/tpl/login.tpl',
        'googleLoginButton.tpl'=>'spx/socialMediaLogin/views/tpl/loginButtons/googleloginbutton.tpl',
        'twitterLoginButton.tpl'=>'spx/socialMediaLogin/views/tpl/loginButtons/twitterloginbutton.tpl',
        'facebookLoginButton.tpl'=>'spx/socialMediaLogin/views/tpl/loginButtons/facebookloginbutton.tpl',
    ),
    'files' => array(
        'socialMediaLoginEvents' => 'spx/socialMediaLogin/events/socialMediaLoginEvents.php',
        'SMLogin' => 'spx/socialMediaLogin/models/SMLogin.php',
        'FacebookLogin' => 'spx/socialMediaLogin/models/facebookLogin.php',
        'GoogleLogin' => 'spx/socialMediaLogin/models/googleLogin.php',
        'TwitterLogin' => 'spx/socialMediaLogin/models/twitterLogin.php',
        'spxLogin'=>'spx/socialMediaLogin/controllers/spxLogin.php',
    ),
    'blocks' => array(
        array('template' => 'layout/base.tpl','block'=>'base_style','file'=>'views/blocks/page/layout/basestyle.tpl'),
    ),
    'settings' => array(
        array('group' => 'socialMediaLogin', 'name' => 'FacebookLogin', 'type' => 'bool', 'value' => 'true', 'position' => 1),
        array('group' => 'socialMediaLogin', 'name' => 'GoogleLogin', 'type' => 'bool', 'value' => 'true', 'position' => 2),
        array('group' => 'socialMediaLogin', 'name' => 'GoogleApiKey', 'type' => 'str', 'value' => '', 'position' => 3),
        array('group' => 'socialMediaLogin', 'name' => 'GoogleLoginId', 'type' => 'str', 'value' => '', 'position' => 4),
        array('group' => 'socialMediaLogin', 'name' => 'GoogleSecret', 'type' => 'str', 'value' => '', 'position' => 5),
        array('group' => 'socialMediaLogin', 'name' => 'TwitterLogin', 'type' => 'bool', 'value' => 'true', 'position' => 6),
        array('group' => 'socialMediaLogin', 'name' => 'TwitterLoginId', 'type' => 'str', 'value' => '', 'position' => 7),
        array('group' => 'socialMediaLogin', 'name' => 'TwitterSecret', 'type' => 'str', 'value' => '', 'position' => 8),
    )
);

