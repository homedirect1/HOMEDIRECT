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
 * @package     Ced_StorePickup
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\StorePickup\Block\Adminhtml\Extensions;

/**
 * Class Details
 * @package Ced\StorePickup\Block\Adminhtml\Extensions
 */
class Details extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var \Ced\StorePickup\Helper\Feed
     */
    protected $feedHelper;

    /**
     * @var \Ced\StorePickup\Helper\Data
     */
    protected $storepickupHelper;

    /**
     * Details constructor.
     * @param \Ced\StorePickup\Helper\Feed $feedHelper
     * @param \Ced\StorePickup\Helper\Data $storepickupHelper
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Ced\StorePickup\Helper\Feed $feedHelper,
        \Ced\StorePickup\Helper\Data $storepickupHelper,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    )
    {
        $this->feedHelper = $feedHelper;
        $this->storepickupHelper = $storepickupHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return $this|string
     */
    public function getModules()
    {
        $modules = $this->feedHelper->getCedCommerceExtensions();
        $helper = $this->storepickupHelper;
        $params = array();
        $args = '';
        foreach ($modules as $moduleName => $releaseVersion) {
            $m = strtolower($moduleName);
            if (!preg_match('/ced/i', $m)) {
                return $this;
            }
            $h = $helper->getStoreConfig(\Ced\StorePickup\Block\Extensions::HASH_PATH_PREFIX . $m . '_hash');
            for ($i = 1; $i <= (int)$helper->getStoreConfig(\Ced\StorePickup\Block\Extensions::HASH_PATH_PREFIX . $m . '_level'); $i++) {
                $h = base64_decode($h);
            }
            $h = json_decode($h, true);
            if (is_array($h) && isset($h['domain']) && isset($h['module_name']) && isset($h['license']) && strtolower($h['module_name']) == $m && $h['license'] == $helper->getStoreConfig(\Ced\StorePickup\Block\Extensions::HASH_PATH_PREFIX . $m)) {
            } else {
                $args .= $m . ',';
            }
        }
        $args = trim($args, ',');
        return $args;
    }

}