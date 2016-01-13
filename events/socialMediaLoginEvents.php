<?php

/**
 * Class which defines events.Sets up initial enviroment for modul.
 * Checks if DB columns exist and creates them if they dont. Checks if there are GmailID and TwitterID columns.Facebook ID column(OXFBID)
 * is built-in in user(oxuser) table.
 */
class socialMediaLoginEvents
{

    /**
     * onActivate event function.
     * @return void
     */
    public static function onActivate()
    {
        $aFieldsExist = self::hasDatabaseFields();
        if (in_array(false, $aFieldsExist)) {
            foreach ($aFieldsExist as $sService => $blValue) {
                if ($blValue === false && self::checkIfServiceEnabled($sService) === true) {
                    self::CreateColumn($sService);
                }
            }
        }
        self::clearCache();
    }

    /**
     * Checks if googleId and Twitter Id database fields exist.
     * @return array  ['service_name'=>bool]
     */
    private static function hasDatabaseFields()
    {
        $aDatabaseFieldsExists = array();
        $sTable = 'oxuser';
        $sColumns = array('googleId', 'twitterId');
        $oDbHandler = oxNew("oxDbMetaDataHandler");
        foreach ($sColumns as $column) {
            $aDatabaseFieldsExists[$column] = $oDbHandler->fieldExists($column, $sTable);
        }

        return $aDatabaseFieldsExists;
    }

    /**
     * Creates columns for social media ID(googleId,TwitterId).
     * @param string $key - name from the service.
     * @return bool
     */
    private static function checkIfServiceEnabled($sService)
    {
        $myconfig = oxRegistry::get("oxConfig");

        switch ($sService) {
            case 'googleId':
                return $myconfig->getConfigParam("gmailLogin");
                break;
            case 'twitterId':
                return $myconfig->getConfigParam("twitterLogin");
                break;
            default:
                throw new Exception("Service doesnt exist");
        }
    }

    /**Creates a column for service ID.
     * @param $key
     */


    private static function createColumn($sService)
    {
        $sSql = "ALTER TABLE oxuser ADD COLUMN {$sService} varchar(50)";
        oxDb::getDb()->execute($sSql);
        $oDbHandler = oxNew("oxDbMetaDataHandler");
        $oDbHandler->updateViews();

    }

    private static function clearCache()
    {
        $cfg = oxRegistry::get("oxConfig");
        $tmp = $cfg->getConfigParam("sCompileDir") . "*";
        foreach (glob($tmp) as $item) {
            if (!is_dir($item)) {
                unlink($item);
            }
        }

    }


}