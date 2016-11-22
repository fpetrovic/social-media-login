<?php


class spxOxcmp_user extends spxOxcmp_user_parent
{
    /**
     * Logs out user from on logout request.If is possible logs out user from social media too(google does not allow this for example).
     * Redirects to home url,after logout.
     *
     * @throws exception
     */

    protected function _afterLogout()
    {
        parent::_afterLogout();

        $SMServices = SMLogin::getActivatedSocialMedia();
        foreach ($SMServices as $service) {
            if ($service->isConnected()) {
                $service->logout();
            }
        }
        header("Location:" . SMLogin::$redirectUri);
    }
}
