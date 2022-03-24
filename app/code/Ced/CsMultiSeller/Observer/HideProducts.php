<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMultiSeller
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMultiSeller\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
/**
 * Class HideProducts
 * @package Ced\CsMultiSeller\Observer
 */
Class HideProducts implements ObserverInterface
{
    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory
     */
    protected $vproductsCollectionFactory;

    /**
     * HideProducts constructor.
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollectionFactory
     */
    public function __construct(
        \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollectionFactory,
       
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->vproductsCollectionFactory = $vproductsCollectionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Filter Catalog List Collection
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $visibility=$this->scopeConfig->getValue('ced_csmarketplace/ced_csmultiseller/catalogsearchindividually');
        
        if($visibility != '1'){
            $productCollection = $observer->getEvent()->getCollection();
            if ($productCollection) {
                $products = [];
                $collection = $this->vproductsCollectionFactory->create()->addFieldToFilter('is_multiseller', ['eq' => 1]);
                foreach ($collection as $row) {
                    array_push($products, $row->getProductId());
                }
                $proArray = [];
                foreach ($productCollection as $product) {
                    if (in_array($product->getId(), $products)) {
                        $proArray [] = $product->getId();
                    }
                }
                if (count($proArray)) {
                    foreach ($proArray as $key) {
                        $productCollection->removeItemByKey($key);
                    }
                }
                $observer->getEvent()->setCollection($productCollection);
            }
        }
    }

}
