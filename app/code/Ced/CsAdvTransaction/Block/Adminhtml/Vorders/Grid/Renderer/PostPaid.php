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
 * @package     Ced_CsAdvTransaction
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer;

/**
 * Class PostPaid
 * @package Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer
 */
class PostPaid extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Ced\CsAdvTransaction\Helper\Data
     */
    protected $advHelper;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * PostPaid constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ced\CsAdvTransaction\Helper\Data $advHelper
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ced\CsAdvTransaction\Helper\Data $advHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Backend\Block\Context $context,
        array $data = []
    )
    {
        $this->storeManager = $storeManager;
        $this->advHelper = $advHelper;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    /**
     * Return the Order Id Link
     *
     */
    public function render(\Magento\Framework\DataObject $order)
    {

        if ($order->getPaymentState() == 1) {
            $currencyCode = $this->storeManager->getStore()->getBaseCurrencyCode();
            if ($this->advHelper->getOrderPaymentType($order->getOrderId()) == __('PrePaid')) {
                echo "N/A";
            } else {
                $postPaidAmount = $this->advHelper->getPostPaidAmount($order->getData());
                $postPaidAmount = $postPaidAmount['post_paid'];
                echo $this->priceCurrency->format($postPaidAmount, false, 2, null, $currencyCode);
            }
        } elseif ($order->getPaymentState() == 3) {
            echo "N/A";
        }

    }
}
