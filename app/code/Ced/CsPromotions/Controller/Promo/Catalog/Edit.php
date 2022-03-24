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
 * @package     Ced_CsPromotions
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsPromotions\Controller\Promo\Catalog;

use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;

/**
 * Class Edit
 * @package Ced\CsPromotions\Controller\Promo\Catalog
 */
class Edit extends \Ced\CsMarketplace\Controller\Vendor
{

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * Edit constructor.
     * @param \Magento\CatalogRule\Model\RuleFactory $ruleFactory
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     * @param \Ced\CsMarketplace\Helper\Acl $aclHelper
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendor
     */
    public function __construct(
        \Magento\CatalogRule\Model\RuleFactory $ruleFactory,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Ced\CsMarketplace\Helper\Acl $aclHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendor
    )
    {
        $this->_coreRegistry = $registry;
        $this->_customerSession = $customerSession;
        $this->ruleFactory = $ruleFactory;
        $this->backendSession = $backendSession;

        parent::__construct(
            $context,
            $resultPageFactory,
            $customerSession,
            $urlFactory,
            $registry,
            $jsonFactory,
            $csmarketplaceHelper,
            $aclHelper,
            $vendor
        );
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        return $this;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->ruleFactory->create();
        $vendorId = $this->_customerSession->getVendorId();
        if ($id) {
            $model->load($id);
            if ($model->getVendorId() !== $vendorId) {
                $this->messageManager->addErrorMessage(__('This rule no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = $this->backendSession->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');

        $this->_coreRegistry->register('current_promo_catalog_rule', $model);

        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Catalog Price Rule'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getRuleId() ? $model->getName() : __('New Catalog Price Rule')
        );
        $this->_view->getLayout()->getBlock(
            'promo_catalog_edit'
        );
        $this->_view->renderLayout();
    }
}
