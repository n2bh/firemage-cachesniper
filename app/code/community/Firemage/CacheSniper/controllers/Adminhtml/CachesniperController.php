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
 * Cache Sniper Main Admin Controller
 *
 * @todo Refactor this index action
 */
class Firemage_CacheSniper_Adminhtml_CachesniperController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        if ($this->getRequest()->isPost())
        {
            try
            {
                $urls = explode("\n", trim($this->getRequest()->get('urls', null)));

                $catUrls = explode("\n", trim($this->getRequest()->get('cat_urls', null)));

                $refetch = $this->getRequest()->getParam('refetch', false);
                $refetchCategories = $this->getRequest()->getParam('refetch_categories', false);

                $this->_getSession()->addSuccess($this->_getHelper()->__('Successfully cleared cache.'));

                // Clear product and CMS URLs
                foreach ($urls as $url)
                {
                    if (strlen($url) > 0)
                    {
                        $this->_getSession()->addSuccess($this->_getHelper()->__("Cleared URL: %s", $url));
                        $this->_getHelper()->clearPage($url);
                    }
                }

                // Clear category URL since they are handled differently by FPC
                foreach ($catUrls as $catUrl)
                {
                    if (strlen($catUrl) > 0)
                    {
                        $this->_getSession()->addSuccess($this->_getHelper()->__("Cleared Category URL: %s", $catUrl));
                        $this->_getHelper()->clearCategory($catUrl);
                    }
                }

                if ($refetch)
                {
                    foreach ($urls as $url)
                    {
                        if (strlen($url) > 0 && $this->_getHelper()->refetchUrl($url))
                        {
                            $this->_getSession()->addSuccess($this->_getHelper()->__("Refetched URL: %s", $url));
                        } else
                        {
                            $this->_getSession()->addNotice($this->_getHelper()->__("Unable to fetch URL: %s", $url));
                        }
                    }
                }

                if ($refetchCategories)
                {
                    foreach ($catUrls as $catUrl)
                    {
                        if (strlen($catUrl) > 0 && $this->_getHelper()->refetchUrl($catUrl))
                        {
                            $this->_getSession()->addSuccess($this->_getHelper()->__("Refetched Category URL: %s", $catUrl));
                        } else
                        {
                            $this->_getSession()->addNotice($this->_getHelper()->__("Unable to fetch Category URL: %s", $catUrl));
                        }
                    }
                }
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                Mage::logException($e);
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e, $this->_getHelper()->__('There was an error clearing cache: %s', $e->getMessage()));
                Mage::logException($e);
            }

        }

        $this->loadLayout();
        $this->_setActiveMenu('system/firemage_cachesniper');
        $this->renderLayout();
    }


    /**
     * @return Firemage_CacheSniper_Helper_Data
     */
    protected function _getHelper() {
        return Mage::helper('firemage_cachesniper');
    }
}
