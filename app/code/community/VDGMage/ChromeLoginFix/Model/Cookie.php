<?php

class VDGMage_ChromeLoginFix_Model_Cookie extends Mage_Core_Model_Cookie
{
    public $store;
    public $requestBrowserIsChrome = false;

    private $_fixEnabled = 0;
    private $_fixEnabledDomain = 0;
    private $_fixEnabledHttp = 0;
    private $_fixEnabledSecure = 0;
    private $_fixEnabledLifetime = 0;
    private $_fixOnlyForChrome = 0;
    private $_canRun = false;

    public function __construct()
    {
        $this->store = $this->getStore();

        $this->_fixEnabled = Mage::getStoreConfig(
            'chromeloginfix/chromeloginfix_general/chromeloginfix_enable',
            $this->store
        );

        //don't get other settings if fix isn't enabled
        if($this->_fixEnabled == 1){
            $this->_fixEnabledDomain = Mage::getStoreConfig(
                'chromeloginfix/chromeloginfix_general/chromeloginfix_enable_domain',
                $this->store
            );

            $this->_fixEnabledHttp = Mage::getStoreConfig(
                'chromeloginfix/chromeloginfix_general/chromeloginfix_enable_http',
                $this->store
            );

            $this->_fixEnabledSecure = Mage::getStoreConfig(
                'chromeloginfix/chromeloginfix_general/chromeloginfix_enable_secure',
                $this->store
            );

            $this->_fixEnabledLifetime = Mage::getStoreConfig(
                'chromeloginfix/chromeloginfix_general/chromeloginfix_enable_lifetime',
                $this->store
            );

            $this->_fixOnlyForChrome = Mage::getStoreConfig(
                'chromeloginfix/chromeloginfix_general/chromeloginfix_enable_chrome_only',
                $this->store
            );

            $this->requestBrowserIsChrome = $this->requestBrowserIsChrome();

            //check if fix should be used
            if($this->_fixEnabled){
                if($this->_fixOnlyForChrome){
                    if($this->requestBrowserIsChrome){
                        //browser is chrome
                        $this->_canRun = true;
                    } else {
                        //browser is not chrome
                        $this->_canRun = false;
                    }
                } else {
                    //fix is enabled without restrictions
                    $this->_canRun = true;
                }
            } else {
                //fix is disabled
                $this->_canRun = false;
            }
        }
    }

    /**
     * Retrieve Config Domain for cookie
     *
     * @return string
     */
    public function getConfigDomain()
    {
        if($this->_canRun == true && $this->_fixEnabledDomain == 1){
            return '';
        }

        return parent::getConfigDomain();
    }

    /**
     * Retrieve use HTTP only flag
     *
     * @return bool
     */
    public function getHttponly()
    {
        if($this->_canRun == true && $this->_fixEnabledHttp == 1){
            return false;
        }

        return parent::getHttponly();
    }

    /**
     * Is https secure request
     * Use secure on adminhtml only
     *
     * @return bool
     */
    public function isSecure()
    {
        if($this->_canRun == true && $this->_fixEnabledSecure == 1){
            return false;
        }

        return parent::isSecure();
    }

    /**
     * Retrieve cookie lifetime
     *
     * @return int
     */
    public function getLifetime()
    {
        if($this->_canRun == true && $this->_fixEnabledLifetime == 1){
            return 86400;
        }

        return parent::getLifetime();
    }

    public function requestBrowserIsChrome()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/Chrome/i', $userAgent)) {
            return true;
        } else {
            return false;
        }
    }
}
