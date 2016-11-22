<?php

class spxServiceMenu extends spxServiceMenu_parent
{

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'serviceMenu.tpl';

    /**
     * Gets activated social media services.
     *
     * @return array
     * @throws exception
     */
    public function getActivatedSocialMediaServices()
    {
        return SMLogin::getActivatedSocialMedia();
    }




}



