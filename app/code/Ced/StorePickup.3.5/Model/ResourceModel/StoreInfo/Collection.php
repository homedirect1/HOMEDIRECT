<?php
namespace Ced\StorePickup\Model\ResourceModel\StoreInfo;
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_StorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected function _construct() 
    {
        $this->_init('Ced\StorePickup\Model\StoreInfo', 'Ced\StorePickup\Model\ResourceModel\StoreInfo');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }
}