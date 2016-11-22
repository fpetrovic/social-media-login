<?php

class spxLogin extends oxUBase
{

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'spxLogin.tpl';


    /**
     * If user is logged,redirects it to home page.
     *
     */
    public function redirectLoggedUser()
    {
        $oConfig = oxNew('oxConfig');
        oxRegistry::getUtils()->redirect($oConfig->getShopHomeURL());
    }

    /**
     * Getter for activated social media services.
     *
     * @return array
     * @throws exception
     */
    public function getActivatedSocialMediaServices()
    {
        return SMLogin::getActivatedSocialMedia();
    }

    /**
     * Getter for the current shop
     *
     * @return object
     */
    public function getCurrentShop()
    {
        $conf = oxNew('oxConfig');
        $oShop = $conf->getActiveShop();

        return $oShop;
    }



}