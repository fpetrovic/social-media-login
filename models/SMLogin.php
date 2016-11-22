<?php

/**
 * Abstract class SMLogin.
 */
abstract class SMLogin
{
    /**
     * Variable for redirection after we get credentials from social media services.
     * @var string
     */
    public static $redirectUri;

    /**Name of social media service user id database field.
     * @var string
     *
     */
    public $sIdFieldName;

    /**
     * Login Button template name
     *
     * @var string
     */
    public $sLoginButtonTpl;

    /**
     * Makes object we use  to connect to social media services.
     * @var object
     */
    protected $clientApiConnection;

    /**
     * Value of access token.
     * @var string
     */
    protected $accessToken;

    /**
     * SMLogin constructor.
     */
    protected function __construct()
    {
        static::$redirectUri = oxRegistry::get('oxViewConfig')->getHomeLink();
    }

    /**
     * Gets Enabled social media services for logging in.
     * @return array
     * @throws exception
     */
    public static function getActivatedSocialMedia()
    {
        $oDb = oxDb::getDb();
        $sModuleDBName = "module:socialMediaLogin";

        $sSelect = "SELECT OXVARNAME FROM oxconfig WHERE OXVARNAME LIKE '%LOGIN%' AND oxmodule ='{$sModuleDBName}'";
        $aSocialMediaServices = $oDb->getAll($sSelect);
        $aSocialMediaObjects = [];

        foreach ($aSocialMediaServices as $sSocialMediaService) {
            $myConfig = oxRegistry::get("oxConfig");
            $blIsEnabled = $myConfig->getConfigParam($sSocialMediaService[0]);

            if ($blIsEnabled === true) {
                if ($oSocialMediaObject = $sSocialMediaService[0]::getInstance()) {
                    $aSocialMediaObjects[] = $oSocialMediaObject;
                } else {
                    throw new exception("Cannot make this module. Please check if module exist and
                    if the class name is the same as setting name for module enable button");
                }
            }
        }
        return $aSocialMediaObjects;
    }

    /**
     * Registers user using data collected from socialMedia service
     * @param array
     * @return string
     * @throws Exception
     */
    public function register($aSocialMediaUser)
    {
        $oNewUser = oxnew("oxuser");
        if (isset($aSocialMediaUser['id'])) {
            $this->setUserSocialMediaId($oNewUser, $aSocialMediaUser['id']);
            //what to put instead of null?
            isset($aSocialMediaUser['firstName']) ? $oNewUser->oxuser__oxfname = new oxField($aSocialMediaUser['firstName']) : null;
            isset($aSocialMediaUser['lastName']) ? $oNewUser->oxuser__oxlname = new oxField($aSocialMediaUser['lastName']) : null;
            isset($aSocialMediaUser['email']) ? $oNewUser->oxuser__oxusername = new oxField($aSocialMediaUser['email']) : null;
        } else {
            throw new Exception("Cannot register user, because social media id is missing!");
        }

        //hash password
        $sRandomString = $this->generateRandomString();
        $oNewUser->setPassword($sRandomString);
        $sUserID = $oNewUser->save();

        return $sUserID;
    }

    /**
     * Sets the user social media id.
     * @param object $oUser
     * @param string $sUserSocialMediaId
     */
    abstract function setUserSocialMediaId($oUser, $sUserSocialMediaId);

    /**
     * Generates random string for password.
     *
     * @param int $length
     * @return string
     */
    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Shows if user is connected with social media service.
     * @return bool
     */
    abstract public function isConnected();

    /**
     * Gets user object from social media service.Transforms it in array.
     * @return array
     */
    abstract public function getUser();

    /**
     * Logout user first from social media service (if its possible("Google and some other services forbid this action")) and after that from oxid eshop.
     */
    abstract public function logout();

    /**
     * Checks if service has database field "socialMediaId"
     *
     * @return bool
     */
    public function hasDatabaseField()
    {
        $sTable = 'oxuser';
        $oDbHandler = oxNew("oxDbMetaDataHandler");
        $blDatabaseFieldExist = $oDbHandler->fieldExists($this->sIdFieldName, $sTable);

        return $blDatabaseFieldExist;
    }

    /**
     * Creates a column for service ID.
     *
     * @return void
     */
    public function createDatabaseColumn()
    {
        $sSql = 'ALTER TABLE oxuser ADD COLUMN ' . $this->sIdFieldName . ' varchar(50)';
        oxDb::getDb()->execute($sSql);
        $oDbHandler = oxNew("oxDbMetaDataHandler");
        $oDbHandler->updateViews();
    }

    /**
     * Sets the id of new social media for user who already exists in db.
     *
     * @param string $sSocialMediaUserId
     * @param string $sUserID
     *
     * @returns string $sUserID
     */
    public function setIdForNewSocialMedia($sSocialMediaUserId, $sUserID)
    {
        $oUser = oxnew("oxuser");
        $oUser->load($sUserID);
        $this->setUserSocialMediaId($oUser, $sSocialMediaUserId);
        return $oUser->save(); //this returns the user id
    }

    /** Generates url for authentication to social media service.
     * @return string
     */
    abstract protected function getAuthUrl();

    /**
     * Sets clientApiConnection property.
     * @return void
     */
    abstract protected function setClientApiConnection();

}