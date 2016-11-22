<?php

$oxConfig = oxRegistry::get("oxConfig");
$moduleDirPath = $oxConfig->getModulesDir('socialMediaLogin');
$googleAutoloadRelPath = "spx/socialMediaLogin/out/libs/Google/autoload.php";
$googleAutoloadAbsPath = $moduleDirPath . $googleAutoloadRelPath;


require_once $googleAutoloadAbsPath;


class GoogleLogin extends SMLogin
{
    /**
     * Static singleton instance of this class.
     * @var object $instance
     */
    private static $instance;

    /**
     * Google Service we want to use(exmpl oauth2,gplus,youtube...)
     * @var object $service
     */
    public $service;

    /**
     * Google Id field name.
     * @var string
     */
    public $sIdFieldName = 'googleloginid';

    /**
     * Google login button template
     * @var string
     */
    public $sLoginButtonTpl = "googleLoginButton.tpl";

    /**
     * GoogleLogin constructor.
     */
    protected function __construct()
    {



        parent::__construct();
        //default scope
        $this->setClientApiConnection();
        $this->clientApiConnection->addScope("email");
        $this->clientApiConnection->addScope("profile");

        $this->service = new Google_Service_Oauth2($this->clientApiConnection);

        $this->authenticate();
        $this->setAccessToken();
    }

    /**
     * Sets clientApiConnection property.
     * @return mixed
     */
    protected function setClientApiConnection()
    {
        $oxConfig = oxRegistry::get("oxConfig");
        $this->clientApiConnection = new Google_Client();
        $this->clientApiConnection->setClientId($oxConfig->getConfigParam('GoogleLoginId'));
        $this->clientApiConnection->setClientSecret($oxConfig->getConfigParam('GoogleSecret'));
        $this->clientApiConnection->setDeveloperKey($oxConfig->getConfigParam('GoogleApiKey'));
        $this->clientApiConnection->setRedirectUri(static::$redirectUri);


    }

    /**
     * Authenticates user.
     */
    private function authenticate()
    {

        if (isset($_GET['code']) && $_SERVER['HTTP_REFERER'] !== 'https://www.facebook.com/') {
            $this->clientApiConnection->authenticate($_GET['code']);
            $_SESSION['google_access_token'] = $this->clientApiConnection->getAccessToken();

            header('Location: ' . filter_var(static::$redirectUri, FILTER_SANITIZE_URL));
            exit;
        }
    }

    /**
     * Sets the access token.
     */
    private function setAccessToken()
    {

        if (isset($_SESSION['google_access_token']) && $_SESSION['google_access_token']) {
            $this->clientApiConnection->setAccessToken($_SESSION['google_access_token']);
            $this->accessToken = $_SESSION['google_access_token'];
        }
    }

    /**
     * Gets the singleton instance of this class.
     * @return object
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Returns authentication url.
     *
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->clientApiConnection->createAuthUrl();
    }

    /**
     * Checks if user is connected to social media service.
     *
     * @return bool
     */
    public function isConnected()
    {
        return (isset($this->accessToken)) ? true : false;
    }

    /**
     * Logs out user.
     */
    public function logout()
    {
        unset($_SESSION['google_access_token']);
        unset($this->accessToken);
        header("location:" . static::$redirectUri . "?cl=start&fnc=logout&redirect=1");
    }

    /**
     * Gets user from the data collected from google plus.
     *
     * @return array
     */
    public function getUser()
    {
        $user = $this->service->userinfo->get();
        $aUser['id'] = $user['id'];
        $aUser['firstName'] = $user['givenName'];
        $aUser['lastName'] = $user['familyName'];
        $aUser['email'] = $user['email'];

        return $aUser;
    }

    /**
     * Sets the user google id.
     * @param object $oUser
     * @param string $sUserSocialMediaId
     * @return object  updated user object.
     */
    public function setUserSocialMediaId($oUser, $sUserSocialMediaId)
    {
        //part after oxuser__ must be the lowercase name of social media id database field
        $oUser->oxuser__googleloginid = new oxField($sUserSocialMediaId);
    }
}


