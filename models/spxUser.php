<?php


class spxUser extends spxUser_parent
{


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

        // If facebook connection is enabled, trying to login user using Facebook ID
        if (!$sUserID && !$blAdmin && $oConfig->getConfigParam("bl_showFbConnect")) {
            $sUserID = $this->_getFacebookUserId();

        }
        if (!$sUserID && !$blAdmin && $oConfig->getConfigParam("gmailLogin")) {
            $sUserID = $this->_getGoogleUserId();
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

        //TODO refactor code below into strategy pattern
    /**
     * Checks if user is connected via Facebook connect and if so, returns user id.
     *
     * @return string
     */
    protected function _getFacebookUserId()
    {

        $oDb = oxDb::getDb();
        $oFb = oxRegistry::get("oxFb");

        $oConfig = $this->getConfig();
        if ($oFb->isConnected() && $oFb->getUser()) {
            $sUserSelect = "oxuser.oxfbid = " . $oDb->quote($oFb->getUser());
            $sShopSelect = "";


            $sSelect = "select oxid from oxuser where oxuser.oxactive = 1 and {$sUserSelect} {$sShopSelect} ";
            $sUserID = $oDb->getOne($sSelect);

            if (!$sUserID) {

                $aUserData = $oFb->api("/me?fields=id,name,email"); //make api call
                //parse data
                $sUserFBID = array_shift($aUserData);
                $sUserName = array_shift($aUserData);
                $sUserEmail = array_shift($aUserData);

                //create user in DB
                //checkIfEmailExists($sEmail);

                $newUser = oxnew("oxuser");
                $newUser->oxuser__oxfname = new oxField($sUserName);
                $newUser->oxuser__oxfbid = new oxField($sUserFBID);
                $newUser->oxuser__oxusername = new oxField($sUserEmail);
                $newUser->setPassword('generic');

                $sUserID = $newUser->save();

            }
        }

        //prebaci ovo u zasebnu fju i overrajduj loadActiveUser


        return $sUserID;
    }

    protected function _getGoogleUserId()
    {
        $oDb = oxDb::getDb();
        $oConfig = $this->getConfig();
        $oGoogle = new GoogleApi();
        $oGoogle->setScopes(array('https://www.googleapis.com/auth/userinfo.email'));

        if ($oGoogle->isConnected() && $oGoogle->getUser()) {
            $sUserSelect = "oxuser.googleId = " . $oDb->quote($oGoogle->getUser());
            $sShopSelect = "";


            $sSelect = "select oxid from oxuser where oxuser.oxactive = 1 and {$sUserSelect} {$sShopSelect} ";
            $sUserID = $oDb->getOne($sSelect);

            if (!$sUserID) {

                $aUserData = $oGoogle->service->userinfo->get(); //make api call

                //create user in DB
                //checkIfEmailExists($sEmail);

                $newUser = oxnew("oxuser");
                $newUser->oxuser__googleId = new oxField($sUserData['id']);
                $newUser->oxuser__oxfname = new oxField($sUserData['givenName']);
                $newUser->oxuser__oxlname = new oxField($sUserData['familyName']);
                $newUser->oxuser__oxusername = new oxField($sUserData['Email']);
                //TODO hashuj password
                $newUser->setPassword('generic');

                $sUserID = $newUser->save();

            }
        }

    }
}


?>