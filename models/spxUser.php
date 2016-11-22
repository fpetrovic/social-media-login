<?php

/**
 * Class spxUser
 * Extends core oxUser class. Adds posibility to login and register users via social
 * networks(Twitter, Google+ and Facebook).Use strategy pattern for registering users from the file regSMLogin.php.
 */
class spxUser extends spxUser_parent
{

    /**
     * Loads active user. Checks if user is logged in and if he is,loads the user.
     * @param bool $blForceAdmin if it is true user is admin.
     *
     * @return bool
     * @throws exception
     */
    public function loadActiveUser($blForceAdmin = false)
    {
        $oConfig = $this->getConfig();

        $blAdmin = $this->isAdmin() || $blForceAdmin;

        // first - checking session info
        $sUserID = $blAdmin ? oxRegistry::getSession()->getVariable('auth') : oxRegistry::getSession()->getVariable('usr');

        // trying automatic login (by 'remember me' cookie)
        $blFoundInCookie = false;
        if (!$sUserID && !$blAdmin && $oConfig->getConfigParam('blShowRememberMe')) {
            $sUserID = $this->_getCookieUserId();
            $blFoundInCookie = $sUserID ? true : false;
        }

        // If social Media connection is enabled, trying to login user using social media Ids

        $activeSocialMedia = SMLogin::getActivatedSocialMedia();
        foreach ($activeSocialMedia as $service) {
            if (!$sUserID && !$blAdmin) {
                $sUserID = $this->getUserId($service);
            }
        }

        // checking user results
        if ($sUserID) {
            if ($this->load($sUserID)) {
                // storing into session
                if ($blAdmin) {
                    oxRegistry::getSession()->setVariable('auth', $sUserID);
                } else {
                    oxRegistry::getSession()->setVariable('usr', $sUserID);
                }

                // marking the way user was loaded
                $this->_blLoadedFromCookie = $blFoundInCookie;

                return true;
            }
        } else {
            // no user
            if ($blAdmin) {
                oxRegistry::getSession()->deleteVariable('auth');
            } else {
                oxRegistry::getSession()->deleteVariable('usr');
            }

            return false;
        }
    }

    /**
     * Gets oxid user id. Compares if there is Social media ID in the row and if it is gets the oxid id. IF there is not
     * oxid user id, then user doesnt exist and it creates new user. Return oxid user id.
     *
     * @param object $oSocialMedia
     *
     * @return string $sUserID
     */
    public function getUserId($oSocialMedia)
    {
        if ($oSocialMedia->isConnected() && $aSocialMediaUser = $oSocialMedia->getUser()) {
            $sUserID = $this->getUserFromDb($oSocialMedia->sIdFieldName, $aSocialMediaUser['id']);
            if (!$sUserID) {
                $sUserID = $this->getUserFromDb("oxusername", $aSocialMediaUser['email']);
                //set new social media id
                if ($sUserID) {
                    $oSocialMedia->setIdForNewSocialMedia($aSocialMediaUser['id'], $sUserID);
                } else {
                    $sUserID = $oSocialMedia->register($aSocialMediaUser);
                }
            }
        }
        return $sUserID;
    }

    /**
     * Gets the user oxid id from the database.
     *
     * @param $sComparingFieldName
     * @param $sSearchUserCriteria
     * @return string
     *
     * @returns string $sUserID
     */
    protected function getUserFromDb($sComparingFieldName, $sSearchUserCriteria)
    {
        $oDb = oxDb::getDb();
        $sUserSelect = "oxuser.$sComparingFieldName = " . $oDb->quote($sSearchUserCriteria);
        $sSelect = "select oxid from oxuser where oxuser.oxactive = 1 and {$sUserSelect}";
        $sUserID = $oDb->getOne($sSelect);
        return $sUserID;
    }

}