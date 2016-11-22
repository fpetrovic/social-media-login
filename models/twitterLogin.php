<?php

/*iskljuci radi provere*/
error_reporting(1);


$oxConfig = oxRegistry::get("oxConfig");
$moduleDirPath = $oxConfig->getModulesDir('socialMediaLogin');
$twitterAutoloadRelPath = "spx/socialMediaLogin/out/libs/Twitteroauth/autoload.php";

$twitterAutoloadAbsPath = $moduleDirPath . $twitterAutoloadRelPath;

require_once $twitterAutoloadAbsPath;
use Abraham\TwitterOAuth\TwitterOAuth;


class TwitterLogin extends SMLogin
{
    /**
     * Static singleton instance of this class.
     * @var object $instance
     */
    private static $instance;

    /**
     * Database column name for user twitter id.
     * @var string
     */
    public $sIdFieldName = "twitterloginid";

    /**
     * Twitter login button template
     * @var string
     */
    public $sLoginButtonTpl = "twitterLoginButton.tpl";

    /**
     * Consumer key defined in twitter apps settings on the "apps.twitter.com" website.
     * @var string
     */
    protected $consumerKey;

    /**
     * Consumer secret defined in twitter apps settings on the "apps.twitter.com" website.
     * @var string
     */
    protected $consumerSecret;

    /**
     * Request token is array constisted of keys: oauth_token,oauth_token_secret,oauth_token_confirmed. We send oauth_token and oauth_token_secret
     * to get access token.
     * @var array
     */
    private $requestToken;

    protected function __construct()
    {
        parent::__construct();
        $this->checkTokensStatus();
        $this->setClientApiConnection();
    }

    private function checkTokensStatus()
    {
        if (isset($_REQUEST['oauth_verifier'], $_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] == $_SESSION['twitter_oauth_token']
        ) {
            $this->requestToken['oauth_token'] = $_SESSION['twitter_oauth_token'];
            $this->requestToken['oauth_token_secret'] = $_SESSION['twitter_oauth_token_secret'];
        } elseif (isset($_SESSION['twitter_access_token'])) {
            $this->accessToken = $_SESSION['twitter_access_token'];
        } else {
            unset($this->requestToken, $this->accessToken);
        }
    }


    //parametre vadi iz getRequestParam() funkcije sve;
    /**
     * Sets clientApiConnection property.
     */

    protected function setClientApiConnection()
    {
        $oxConfig = oxRegistry::get("oxConfig");
        $this->consumerKey = $oxConfig->getConfigParam('TwitterLoginId');
        $this->consumerSecret = $oxConfig->getConfigParam('TwitterSecret');
        //if we have request token, ok, lets send that request token to take access token
        if ($this->requestToken) {
            $this->clientApiConnection = new TwitterOAuth($this->consumerKey, $this->consumerSecret,
                $this->requestToken['oauth_token'], $this->requestToken['oauth_token_secret']);
            $this->getAccessToken();

            //if we have accessToken,great,make our clientApiConnection to make some api calls
        } elseif ($this->accessToken) {
            $this->clientApiConnection = new TwitterOAuth($this->consumerKey, $this->consumerSecret,
                $this->accessToken['oauth_token'], $this->accessToken['oauth_token_secret']);

            //we dont have anything, well, lets set up our clientApiConnection to take request token
        } else {
            $this->clientApiConnection = new TwitterOAuth($this->consumerKey, $this->consumerSecret);
        }
    }

    /**
     * Used to get access token.
     *
     * @throws Exception
     */
    private function getAccessToken()
    {
        $this->accessToken = $this->requestToken("access");
        $_SESSION['twitter_access_token'] = $this->accessToken;
        // redirect user back to index page
        header("Location:" . static::$redirectUri);
        exit;
    }

    /**
     * Get requested token from twitter.Wheater it is request token or access token.
     *
     * @param $token
     * @return array
     * @throws Exception
     */
    private function requestToken($token)
    {
        switch ($token) {
            case "request":
                return $this->clientApiConnection->oauth('oauth/request_token', array('oauth_callback' => static::$redirectUri));
                break;
            case "access":
                return $this->clientApiConnection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
                break;
            default:
                throw new Exception("Requested token does not exist.");
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
     * Checks if user is connected to social media service.
     *
     * @return bool
     */
    public function isConnected()
    {
        return (isset($this->accessToken)) ? true : false;
    }

    /**
     * Returns authentication url.
     *
     * @return string
     */
    public function getAuthUrl()
    {
        $this->requestToken = $this->requestToken("request");

        $_SESSION['twitter_oauth_token'] = $this->requestToken['oauth_token'];
        $_SESSION['twitter_oauth_token_secret'] = $this->requestToken['oauth_token_secret'];

        $url = $this->clientApiConnection->url('oauth/authorize', array('oauth_token' => $this->requestToken['oauth_token']));

        return $url;
    }

    /**
     * Gets user from the data collected from twitter.
     *
     * @return array
     */
    public function getUser()
    {
        $oUser = $this->clientApiConnection->get("account/verify_credentials", ['include_email' => 'true']);  // boolean MUST be inside quotes
        $aUser['id'] = $oUser->id;
        $parseFirstLastName = explode(' ', $oUser->name);
        $aUser['firstName'] = $parseFirstLastName[0];
        $aUser['lastName'] = $parseFirstLastName[1];
        $aUser['email'] = $oUser->email;
        return $aUser;
    }

    /**
     * Logs out user.
     */
    public function logout()
    {
        unset($_SESSION['twitter_access_token'], $_SESSION['twitter_oauth_token'], $_SESSION['twitter_oauth_token_secret']);
        unset($this->accessToken, $this->requestToken);
        header("Location:" . static::$redirectUri);
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
        $oUser->oxuser__twitterloginid = new oxField($sUserSocialMediaId);
        return $oUser;
    }
}


