<?php


class spxOxcmp_user extends spxOxcmp_user_parent
{
    public function logout()
    {
        $myConfig = $this->getConfig();
        $oUser = oxNew('oxuser');

        if ($oUser->logout()) {

            $this->setLoginStatus(USER_LOGOUT);

            // finalizing ..
            $this->_afterLogout();


            if ($this->getParent()->isEnabledPrivateSales()) {
                return 'account';
            }

            $oxFb = oxRegistry::get("oxFb");
            if ($oxFb->isConnected()) {
                $logoutURL = $oxFb->getLogoutUrl();
                oxRegistry::getUtils()->redirect($logoutURL);
            }

            // redirecting if user logs out in SSL mode
            if (oxRegistry::getConfig()->getRequestParameter('redirect') && $myConfig->getConfigParam('sSSLShopURL')) {
                oxRegistry::getUtils()->redirect($this->_getLogoutLink());
            }
        }
    }

    protected function _afterLogout()
    {
        oxRegistry::getSession()->deleteVariable('paymentid');
        oxRegistry::getSession()->deleteVariable('sShipSet');
        oxRegistry::getSession()->deleteVariable('deladrid');
        oxRegistry::getSession()->deleteVariable('dynvalue');


        $oxFb = oxRegistry::get("oxFb");
        //try to logout
        if ($oxFb->isConnected()) {
            $fbPattern = "/fb_[0-9]+_[a-zA-Z]/";
            $fbKeys = preg_grep($fbPattern, array_keys($_SESSION));
            foreach ($fbKeys as $fbKey) {
                oxRegistry::getSession()->deleteVariable($fbKey);
            }
        }

        // resetting & recalc basket
        if (($oBasket = $this->getSession()->getBasket())) {
            $oBasket->resetUserInfo();
            $oBasket->onUpdate();
        }
    }
}

?>