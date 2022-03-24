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
 * @package     Ced_HelpDesk
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\HelpDesk\Controller\Tickets;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Ced\HelpDesk\Controller\Tickets
 */
class Index extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Ced\HelpDesk\Helper\Data
     */
    protected $helpdeskHelper;

    /**
     * Index constructor.
     * @param \Ced\HelpDesk\Helper\Data $helpdeskHelper
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Ced\HelpDesk\Helper\Data $helpdeskHelper,
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->helpdeskHelper = $helpdeskHelper;
        parent::__construct($context);
    }

    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->helpdeskHelper->getStoreConfig('helpdesk/general/enable')) {
            $this->_redirect('cms/index/index');
            return;
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Support System Tickets'));
        return $resultPage;
    }
}
