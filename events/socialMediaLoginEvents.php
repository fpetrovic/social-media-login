<?php

/**
 * Class which defines events.Sets up initial enviroment for modul.
 * Checks if DB columns exist and creates them if they dont. Checks if there are GmailID and TwitterID columns.Facebook
 * ID column(OXFBID) is built-in in user(oxuser) table.
 */
class socialMediaLoginEvents
{


    /**
     * Map of SEO Urls
     * @var array
     */
    public static $endpoints = array(
        'Login/' => 'index.php?cl=spxLogin',
    );

    /**
     * onActivate event function.Checks which social media services are enabled,and checks if there are columns in user table
     * for social media Id.If columns dont exist, creates them.Sets seo urls.Clears cache.
     *
     * @return void
     */
    public static function onActivate()
    {
        $aSocialMediaActivatedServices = SMLogin::getActivatedSocialMedia();
        /** @var SMLogin $service */
        foreach ($aSocialMediaActivatedServices as $service) {
            if ($service->hasDatabaseField() === false) {
                $service->createDatabaseColumn();
            }
        }
        self::setSeoUrls();
        self::clearCache();
    }

    /**
     *Sets seo urls in database in oxseo table.
     */
    public static function setSeoUrls()
    {
        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $sQtedType = $oDb->quote('static');
        foreach (self::$endpoints as $seoUrl => $value) {
            foreach (oxRegistry::getConfig()->getShopIds() as $iShopId) {
                $seoHash = md5(strtolower($seoUrl));
                $iQtedShopId = $oDb->quote($iShopId);
                $sQtedStdUrl = $oDb->quote($value);
                $sQtedSeoUrl = $oDb->quote($seoUrl);
                $sQtedIdent = $oDb->quote($seoHash);

                $sSql = "INSERT INTO oxseo
                    (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxexpired, oxparams)
                VALUES
                    ( {$sQtedIdent}, {$sQtedIdent}, {$iQtedShopId}, 0, {$sQtedStdUrl}, {$sQtedSeoUrl}, {$sQtedType}, '0', '0', '' )
                ON duplicate KEY UPDATE
                    oxident = {$sQtedIdent}, oxstdurl = {$sQtedStdUrl}, oxseourl = {$sQtedSeoUrl}, oxfixed = '', oxexpired = '0'";

                oxDb::getDb()->execute($sSql);
            }
        }
    }

    /**
     * Clears tmp cache folder.
     *
     * @return void
     */
    private static function clearCache()
    {
        $cfg = oxRegistry::get("oxConfig");
        $tmp = $cfg->getConfigParam("sCompileDir") . "*";
        foreach (glob($tmp) as $item) {
            if (!is_dir($item)) {
                unlink($item);
            } else {
                $dir = $item . "/*";
                foreach (glob($dir) as $files) {
                    unlink($files);
                }
            }
        }
    }

    /**
     * onDeactivate event function.Clears seo urls.Clears cache.
     */
    public static function onDeactivate()
    {
        self::unsetSeoUrls();
        self::clearCache();
    }

    /**
     * Unsets seo urls in database in oxseo table.
     */
    public static function unsetSeoUrls()
    {
        oxDb::getDb()->execute("DELETE FROM oxseo WHERE OXSTDURL LIKE '%spxLogin%'");
    }

}