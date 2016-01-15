<?php

$oxConfig = oxRegistry::get("oxConfig");
$moduleDirPath = $oxConfig->getModulesDir('socialMediaLogin');
$googleAutoloadRelPath = "spx/socialMediaLogin/out/libs/Google/autoload.php";
$googleAutoloadAbsPath = $moduleDirPath . $googleAutoloadRelPath;


require_once $googleAutoloadAbsPath;


class GoogleApi
{
    public $client;
    public $service;

    public $redirectUri;
    private $connected;


    public function __construct()
    {
        $oxConfig = oxRegistry::get("oxConfig");
        $this->client = new Google_Client();

        if ('' !== trim($oxConfig->getConfigParam('googleId'))) {
            $this->client->setClientId($oxConfig->getConfigParam('googleId'));

        } else {
            throw new Exception("You must set your Google Id in module settings");
        }
        if ('' !== trim($oxConfig->getConfigParam('googleSecret'))) {
            $this->client->setClientSecret($oxConfig->getConfigParam('googleSecret'));
        } else {
            throw new Exception("You must set your Google secret in module settings");
        }
        if ('' !== trim($oxConfig->getConfigParam('googleApiKey'))) {
            $this->client->setDeveloperKey($oxConfig->getConfigParam('googleApiKey'));
        }

        $this->setRedirectUri();

        $this->setConnected();

    }

    private function setConnected()
    {
        (isset($_SESSION['access_token'])) ? $this->connected = true : $this->connected = false;
    }

    public static function disconnect()
    {
        unset($_SESSION['access_token']);
        $this->connected = false;
        header('Location: ' . filter_var($this->redirect_uri, FILTER_SANITIZE_URL));
        exit;
    }

    public function setService($service)
    {
        //TODO-me check if $service is $avaliable service.Else trow exception
        $this->$service = new $service ($this->client);
    }

    //authenticate User. Sets the access token


    public function setScopes(array $scopes)
    {
        $this->client->setScopes($scopes);
    }

    public function authenticate()
    {
        if (isset($_GET['code'])) {
            $this->client->authenticate($_GET['code']);

            $_SESSION['access_token'] = $this->getAccessToken();
            header('Location: ' . filter_var($this->redirect_uri, FILTER_SANITIZE_URL));
            exit;
        }
    }

    public function getAccessToken()
    {
        return $this->client->getAccessToken();
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function isConnected()
    {
        return $this->connected;
    }

    public function getUser()
    {
        if ($this->connected) {
            $user = $this->service->userinfo->get();
            return $user['id'];
        }
    }

    protected function setRedirectUri()
    {
        $this->client->setRedirectUri($redirectUri);
        $this->redirectUri = $this->getCurrentUrl();
    }

    protected function getCurrentUrl()
    {
        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
        ) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        //todo for ssl
        $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        return $currentUrl;

    }

}




