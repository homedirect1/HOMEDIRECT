<?php

namespace Ced\CsReport\Controller\Product;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class ExportReturnReport
 * @package Ced\CsReport\Controller\Product
 */
class ExportReturnReport extends \Ced\CsMarketplace\Controller\Vendor
{

    /**
     * @var SessionFactory
     */
    protected $sessionFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * ExportReturnReport constructor.
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param SessionFactory $sessionFactory
     * @param Context $context
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
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        SessionFactory $sessionFactory,
        Context $context,
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
        $this->sessionFactory = $sessionFactory;

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

        $this->_fileFactory = $fileFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     */
    public function execute()
    {
        $from = $this->_request->getParam('from');
        $to = $this->_request->getParam('to');
        $fileName = 'vendor-' . $from . '-sales.csv';
        $gridBlock = $this->_view->getLayout()->createBlock(
            'Ced\CsReport\Block\Product\Returns\Grid'
        );
        $vendorId = $this->sessionFactory->create()->getVendorId();
        $gridBlock->setVendorId($vendorId);
        $content = $gridBlock->getCsvFile();
        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}