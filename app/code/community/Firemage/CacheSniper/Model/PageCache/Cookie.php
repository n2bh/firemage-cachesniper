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
 * PageCache Cookie override
 */
class Firemage_CacheSniper_Model_PageCache_Cookie extends Enterprise_PageCache_Model_Cookie
{
    /**
     * Retrieve obscured cookie value
     *
     * NOTE: Do not use this for printing!
     *
     * Uses the current Magento FPC salt to set a cookie value.
     *
     * @param $value
     * @return string
     */
    public function getObscureValue($value)
    {
        return md5($this->_getSalt() . $value);
    }
}