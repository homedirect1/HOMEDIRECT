<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Block\Adminhtml\Customer;

/**
 * Adminhtml block action item renderer
 */
class Customername extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Sales\Model\Order $order
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Sales\Model\Order $order,
        array $data = []
    ) {
        $this->order = $order;
        parent::__construct($context, $data);
    }

    /**
     * Render data
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $orderName = '';
        if ($row->getData('order_id')) {
            $currentOrder = $this->order->load($row->getData('order_id'));
            $orderName = $currentOrder->getCustomerName();
        }
        return $orderName;
    }
}
