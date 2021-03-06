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
 * @package     Ced_CsSubAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Controller\Customer;

use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;

/**
 * Class Send
 * @package Ced\CsSubAccount\Controller\Customer
 */
class Send extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    protected $vendorFactory;

    /**
     * @var \Ced\CsSubAccount\Model\CsSubAccountFactory
     */
    protected $csSubAccountFactory;

    /**
     * @var \Ced\CsSubAccount\Model\ResourceModel\AccountStatus\CollectionFactory
     */
    protected $accountstatusCollectionFactory;

    /**
     * @var \Ced\CsSubAccount\Model\AccountStatusFactory
     */
    protected $accountStatusFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * Send constructor.
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory
     * @param \Ced\CsSubAccount\Model\ResourceModel\AccountStatus\CollectionFactory $accountstatusCollectionFactory
     * @param \Ced\CsSubAccount\Model\AccountStatusFactory $accountStatusFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
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
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory,
        \Ced\CsSubAccount\Model\ResourceModel\AccountStatus\CollectionFactory $accountstatusCollectionFactory,
        \Ced\CsSubAccount\Model\AccountStatusFactory $accountStatusFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
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

        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->vendorFactory = $vendor;
        $this->csSubAccountFactory = $csSubAccountFactory;
        $this->accountstatusCollectionFactory = $accountstatusCollectionFactory;
        $this->accountStatusFactory = $accountStatusFactory;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $vendorId = $this->_getSession()->getVendorId();
        if (!$vendorId) {
            return;
        }

        if ($this->getRequest()->getPostValue()) {
            $emails = $this->getRequest()->getPost('email');
            $messages = $this->getRequest()->getPost('msg');
            $success = 0;
            $fail = 0;
            $count = count($emails);
            for ($i = 0; $i < count($emails); $i++) {
                $cid = $emails[$i];
                $vendor = $this->vendorFactory->create()->loadByEmail($cid);
                $subVendor = $this->csSubAccountFactory->create()->load($cid, 'email')->getData();
                if ($vendor || count($subVendor)) {
                    $fail = $fail + 1;
                    continue;
                }
                $savedRequests = $this->accountstatusCollectionFactory->create()
                    ->addFieldToFilter('parent_vendor', $vendorId)
                    ->addFieldToFilter('email', $cid)->getData();
                if ((count($savedRequests) && $savedRequests[0]['status'] == \Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_PENDING) || (count($savedRequests) && $savedRequests[0]['status'] == \Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_ACCEPTED)) {
                    $fail = $fail + 1;
                    continue;
                }
                $data = array();
                $data['parent_vendor'] = $vendorId;
                $data['email'] = $cid;
                $data['status'] = \Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_PENDING;
                $model = $this->accountStatusFactory->create()->setData($data);
                $model->save();
                $emailTemplate = 'ced_cssubaccount_request_template';
                $vendor_data = $this->_getSession()->getVendor();
                $customer = $this->customerFactory->create()->setWebsiteId($this->_storeManager->getWebsite()->getId());
                $name = $customer->loadByEmail($emails[$i])->getName();

                $emailTemplateVariables = array();
                $emailTemplateVariables['vname'] = $vendor_data['public_name'];
                $emailTemplateVariables['vid'] = $vendorId;
                $emailTemplateVariables['cid'] = $model->getId();
                if (isset($vendor_data['company_address']))
                    $emailTemplateVariables['company_address'] = $vendor_data['company_address'];
                $emailTemplateVariables['message'] = $messages[$i];
                $sender = [
                    'name' => $vendor_data['public_name'],
                    'email' => 'info@homedirect.in'/*$vendor_data['email']*/,
                ];
                $transport = $this->_transportBuilder->setTemplateIdentifier($emailTemplate)
                    ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                    ->setTemplateVars($emailTemplateVariables)
                    ->setFrom($sender)
                    ->addTo($emails[$i])
                    ->getTransport();

                try {
                    $transport->sendMessage();
                    $success = $success + 1;
                } catch (\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $this->messageManager->addErrorMessage(__($errorMessage));
                    $this->_redirect('cssubaccount/customer/index/');
                    return;
                }
            }
            if ($fail)
                $this->messageManager->addErrorMessage(__($fail . ' Request has not been sent.'));
            if ($success)
                $this->messageManager->addSuccessMessage(__($success . ' Request has been successfully sent.'));

            $this->_redirect('cssubaccount/customer/index/');
            return;
        }
    }

}
