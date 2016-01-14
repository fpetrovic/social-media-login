<?php


class spxUser extends spxUser_parent
{
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

            if(!$sUserID) {

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

        return $sUserID;
    }



}


?>