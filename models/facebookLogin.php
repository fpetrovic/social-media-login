<?php


class FacebookLogin extends SMLogin
{
    /**
     * Static singleton instance of this class.
     * @var object $instance
     */
    private static $instance;

    /**
     * Core oxfb class object which helps us to interact with the facebook.
     * @var object $oxFb
     */
    public $oxFb;

    /**
     * Database column name for user facebook id.
     * @var string
     */
    public $sIdFieldName = 'oxfbid';

    /**
     * Facebook login button template
     * @var string
     */
    public $sLoginButtonTpl = "facebookLoginButton.tpl";

    protected function __construct()
    {
        parent::__construct();
        $this->oxFb = oxRegistry::get("oxFb");

    }

    /**
     * Returns singleton instance.
     * @return object static::$instance
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Shows if user is connected with the facebook.
     * @return bool
     */
    public function isConnected()
    {
        return $this->oxFb->isConnected();
    }

    /**
     * Getter for authentication url.
     *
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->oxFb->getLoginUrl();
    }

    /**
     * Gets user object from facebook service.Transforms it in array.
     *
     * @return array
     */
    public function getUser()
    {
        $oUser = $this->oxFb->api("/me?fields=id,name,email");
        $aUser['id'] = array_shift($oUser);
        $parseFirstLastName = explode(' ', array_shift($oUser));
        $aUser['firstName'] = $parseFirstLastName[0];
        $aUser['lastName'] = $parseFirstLastName[1];
        $aUser['email'] = array_shift($oUser);

        return $aUser;
    }

    /**
     * Logout user first from the facebook and after that from the shop.
     */
    public function logout()
    {
        $fbPattern = "/fb_[0-9]+_[a-zA-Z]/";
        $fbKeys = preg_grep($fbPattern, array_keys($_SESSION));
        foreach ($fbKeys as $fbKey) {
            oxRegistry::getSession()->deleteVariable($fbKey);
        }

        $logoutURL = $this->oxFb->getLogoutUrl();
        oxRegistry::getUtils()->redirect($logoutURL);
    }

    /**
     * Sets the user oxfbid.
     * @param object $oUser
     * @param string $sUserSocialMediaId
     */
    public function setUserSocialMediaId($oUser, $sUserSocialMediaId)
    {
        //part after oxuser__ must be the lowercase name of social media id database field
        $oUser->oxuser__oxfbid = new oxField($sUserSocialMediaId);
    }

    /**
     * Sets clientApiConnection property.It is not implemented because for connection with Facebook we use core facebook classes.
     * @return void
     */
    protected function setClientApiConnection()
    {

    }

}