<?php
/**
 * Firemage CacheSniper
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Firemage
 * @package    Firemage_CacheSniper
 * @author     Scott Weaver <scottmweaver@gmail.com>
 * @copyright  Copyright (C) 2012 Scott Weaver (http://scottmw.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Firemage Helper Class
 */
class Firemage_CacheSniper_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Store URLs Array
     *
     * @var array
     */
    private $_storeUrls = null;

    /**
     * Get Page Cache ID
     *
     * @param $uri
     * @param array $queryParams
     * @param null $extra
     * @return string
     */
    public function getPageId($uri, array $queryParams, $extra = null)
    {
        ksort($queryParams);
        $queryParamsHash = md5(serialize($queryParams));

        if (is_null($extra))
        {
            return $uri . '_' . $queryParamsHash;
        } else
        {
            return $uri . '_' . $queryParamsHash . $extra;
        }

    }

    /**
     * Get Page Ids
     *
     * @param $uri
     * @param array $queryParams
     * @return array
     */
    public function getPageIds($uri, array $queryParams)
    {
        $pageIds = array();

        // Logged in
        $loggedInKey = $this->_getCookieModel()->getObscureValue('customer_logged_in_1');

        /** @var $customerGroups Mage_Customer_Model_Resource_Group_Collection */
        $customerGroups = Mage::getModel('customer/group')->getCollection();
        $customerGroups->addFieldToFilter('customer_group_id',
            array('neq' => Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
        );

        foreach ($customerGroups as $groupId => $group)
        {
            $groupKey = $this->_getCookieModel()->getObscureValue('customer_group_'.$groupId);

            $groupUri = $uri . '_' .  $groupKey . '_' . $loggedInKey;

            $pageIds[] = $this->getPageId($groupUri, $queryParams);
        }

        return $pageIds;
    }

    /**
     * Get Category Ids
     *
     * @param $uri
     * @param array $queryParams
     * @return array
     */
    public function getCategoryIds($uri, array $queryParams)
    {
        $categoryIds = array();

        // Logged in
        $loggedInKey = $this->_getCookieModel()->getObscureValue('customer_logged_in_1');

        /** @var $customerGroups Mage_Customer_Model_Resource_Group_Collection */
        $customerGroups = Mage::getModel('customer/group')->getCollection();
        $customerGroups->addFieldToFilter('customer_group_id',
            array('neq' => Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
        );

        foreach ($customerGroups as $groupId => $group)
        {
            $groupKey = $this->_getCookieModel()->getObscureValue('customer_group_'.$groupId);

            $groupUri = $uri . '_' .  $groupKey . '_' . $loggedInKey;

            $categoryIds[] = $this->getCategoryId($groupUri, $queryParams);
        }

        return $categoryIds;
    }

    /**
     * Get Category Cache ID
     *
     * @param $uri
     * @param array $queryParams
     * @return string
     */
    public function getCategoryId($uri, array $queryParams)
    {
        ksort($queryParams);
        $queryParams = md5(json_encode($queryParams));
        return $uri . '_' . $queryParams;
    }

    /**
     * Clear Page (CMS, Product)
     *
     * @param $url
     * @return bool
     */
    public function clearPage($url)
    {
        $url = trim($url);

        if (empty($url)) {
            return false;
        }

        $fullUrl = 'http://'.$url;

        $pUrl = parse_url($fullUrl);

        $storeId = $this->matchUrlToStore($fullUrl);

        if (!$pUrl || !array_key_exists('host', $pUrl) || !$storeId) {
            return;
        }

        $store = Mage::getModel('core/store')->load($storeId);

        $url = array_key_exists('path', $pUrl) ? $pUrl['host'] . $pUrl['path'] : $pUrl['host'];

        $queryParams = array();

        if (array_key_exists('query', $pUrl)) {
            parse_str($pUrl['query'], $queryParams);
        }

        $scope = $this->_getScopeAndTypeFromStore($store);

        foreach ($this->getPageIds($url, $queryParams) as $pageIdCustomer)
        {
            $cacheId = $this->_prepareCacheId($pageIdCustomer, $scope['scope_code']);
            $this->_getCacheInstance()->remove($cacheId);
        }

        $cacheId = $this->_prepareCacheId($this->getPageId($url, $queryParams), $scope['scope_code']);
        $this->_getCacheInstance()->remove($cacheId);
    }

    /**
     * Clear Category URL
     *
     * @param $url
     * @return bool
     */
    public function clearCategory($url)
    {
        $url = trim($url);

        if (empty($url)) {
            return false;
        }

        $fullUrl = 'http://'.$url;

        $pUrl = parse_url($fullUrl);

        $storeId = $this->matchUrlToStore($fullUrl);

        if (!$pUrl || !array_key_exists('host', $pUrl) || !$storeId) {
            return;
        }

        $store = Mage::getModel('core/store')->load($storeId);

        $url = array_key_exists('path', $pUrl) ? $pUrl['host'] . $pUrl['path'] : $pUrl['host'];

        $queryParams = array();

        if (array_key_exists('query', $pUrl)) {
            parse_str($pUrl['query'], $queryParams);
        }

        $scope = $this->_getScopeAndTypeFromStore($store);

        foreach ($this->getCategoryIds($url, $queryParams) as $categoryIdCustomer)
        {
            $cacheId = $this->_prepareCacheId($categoryIdCustomer, $scope['scope_code']);
            $this->_getCacheInstance()->remove($cacheId);
        }

        $cacheId = $this->_prepareCacheId($this->getCategoryId($url, $queryParams), $scope['scope_code']);
        $this->_getCacheInstance()->remove($cacheId);

    }

    /**
     * Match URL to Store and fetch its ID
     *
     * Format examples:
     *
     * - http://www.someurl.com/
     * - http://www.someurl.com/name/
     *
     * Note - the end slash is as important as the http:// prefix.
     *
     * @param $url
     * @return bool|int
     */
    public function matchUrlToStore($url)
    {
        $urlParts = parse_url($url);

        $urlHost = $urlParts['host'];
        $urlTrunc = isset($urlParts['path']) ? $urlHost . $urlParts['path'] : $urlHost;

        $storeUrls = $this->getStoreUrls();

        uasort($storeUrls, array(get_class($this), '_lengthSort'));

        foreach ($storeUrls as $storeId => $storeUrl)
        {
            if (strcasecmp($urlTrunc, $storeUrl) === 0) {
                return $storeId;
            }

            $testUrl = substr($urlTrunc, 0, strlen($storeUrl));

            if (strcasecmp($testUrl, $storeUrl) === 0) {
                return $storeId;
            }
        }

        return false;
    }

    /**
     * Return array of Store URLs
     *
     * @return array
     */
    public function getStoreUrls()
    {
        if (is_null($this->_storeUrls))
        {
            $this->_storeUrls = array();

            foreach (Mage::app()->getStores() as $store)
            {
                $storeUrl = parse_url($store->getBaseUrl());

                $this->_storeUrls[$store->getId()] = $storeUrl['host'] . $storeUrl['path'];
            }
        }

        return $this->_storeUrls;
    }

    /**
     * Refetch the given URL
     *
     * @param $url
     * @return bool
     */
    public function refetchUrl($url)
    {
        $url = 'http://' . trim($url);

        try
        {
            $client = new Zend_Http_Client($url);
            $client->setConfig(array(
                'maxredirects' => 0,
                'timeout' => 25
            ));

            return $client->request();
        }
        catch (Exception $e)
        {
            Mage::logException($e);
            Mage::throwException($this->__('Unable to fetch URL: %s', $url));
            return false;
        }
    }

    /**
     * Get Scope and Type from Store Object
     *
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    protected function _getScopeAndTypeFromStore(Mage_Core_Model_Store $store)
    {
        $scopeType = $store->getStoreId() == $store->getWebsiteId() ? 'website' : 'store';

        $scopeCode = $store->getCode();

        if ($scopeType == 'website') {
            $website = Mage::app()->getWebsite($store->getWebsiteId());
            $scopeCode = $website->getCode();
        }

        return array(
            'scope_code' => $scopeCode,
            'scope_type' => $scopeType,
            'options' => array()
        );
    }

    /**
     * Prepares a standardized URL for use in other methods.
     *
     * @param $url
     * @return mixed
     */
    protected function _prepareStandardUrl($url)
    {
        if (!preg_match('/^http/i', trim($url), $matches))
        {
            $url = 'http://' . $url;
        }

        $parsedUrl = parse_url($url);

        return $parsedUrl;
    }

    /**
     * Mimic FPC Prepare Cache ID
     *
     * @param $id
     * @param string $scopeCode
     * @return string
     */
    protected function _prepareCacheId($id, $scopeCode = '')
    {
        $cacheId = Enterprise_PageCache_Model_Processor::REQUEST_ID_PREFIX . md5($id . $scopeCode);
        return $cacheId;
    }

    /**
     * Get Cache Instance
     *
     * @return Mage_Core_Model_Cache
     */
    protected function _getCacheInstance()
    {
        return Enterprise_PageCache_Model_Cache::getCacheInstance();
    }

    /**
     * Get Cache Processor
     *
     * @return Enterprise_PageCache_Model_Processor
     */
    protected function _getCacheProcessor()
    {
        return Mage::getModel('enterprise_pagecache/processor');
    }

    /**
     * Get Cookie Model
     *
     * @return Firemage_CacheSniper_Model_PageCache_Cookie|Mage_Core_Model_Abstract
     */
    protected function _getCookieModel()
    {
        return Mage::getModel('enterprise_pagecache/cookie');
    }

    /**
     * Sort method for usort() or uasort(); sorts from longest to shortest
     *
     * @param $a
     * @param $b
     * @return int
     */
    private function _lengthSort($a, $b)
    {
        return strlen($b) - strlen($a);
    }
}
