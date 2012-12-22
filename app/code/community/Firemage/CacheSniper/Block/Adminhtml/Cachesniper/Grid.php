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
 * Cache Sniper Main Admin Grid
 */
class Firemage_CacheSniper_Block_Adminhtml_Cachesniper_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set defaults
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('cachesniperGrid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
}
