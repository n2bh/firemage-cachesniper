## Firemage_CacheSniper ##
====================

### Cache Clearing module for Magento Enterprise 1.11 ###

#### Brief Description ####
This module allows a Magento administrator to clear Full Page Cache for specific URLs from the Magento admin panel.

#### Features: ####
- Allows admins to enter specific URL for CMS pages, product pages or catalog pages rather than doing an entire cache clear
- Automatically does a best-match on store URL for reduced delete calls to cache storage


#### Prerequisites ####
- Magento 1.11
- Magento 1.11 Enteprise_PageCache enabled

#### Installation ####

 1. Install [modman](https://github.com/colinmollenhour/modman)
 2. Run modman install command:
   * `modman clone git@github.com:tdlm/firemage-cachesniper.git`
