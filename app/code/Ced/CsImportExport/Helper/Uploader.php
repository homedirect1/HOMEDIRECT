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
 * @category  Ced
 * @package   Ced_CsImportExport
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsImportExport\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Uploader
 */
class Uploader extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Media files uploader
     *
     * @var \Magento\CatalogImportExport\Model\Import\Uploader
     */
    protected $fileUploader;

    /**
     * File helper downloadable product
     *
     * @var \Magento\Downloadable\Helper\File
     */
    protected $fileHelper;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * Entity model parameters.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var
     */
    protected $filesystem;

    /**
     * Uploader constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Downloadable\Helper\File $fileHelper
     * @param \Magento\CatalogImportExport\Model\Import\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Customer\Model\Session $customerSession
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Downloadable\Helper\File $fileHelper,
        \Magento\CatalogImportExport\Model\Import\UploaderFactory $uploaderFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        parent::__construct($context);
        $this->fileHelper = $fileHelper;
        $this->customerSession = $customerSession;
        $this->fileUploader = $uploaderFactory->create();
        $this->fileUploader->init();
        $this->fileUploader->setAllowedExtensions($this->getAllowedExtensions());
        $this->fileUploader->removeValidateCallback('catalog_product_image');
        $this->connection = $resource->getConnection('write');
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
    }

    /**
     * Returns an object for upload a media files
     *
     * @param string $type
     * @param array $parameters
     * @return \Magento\CatalogImportExport\Model\Import\Uploader
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getUploader($type, $parameters)
    {
        $dirConfig = DirectoryList::getDefaultConfig();
        $dirAddon = $dirConfig[DirectoryList::MEDIA][DirectoryList::PATH];

        $DS = DIRECTORY_SEPARATOR;
        $vendorId = $this->customerSession->getVendorId();
        if (!empty($parameters[\Magento\ImportExport\Model\Import::FIELD_NAME_IMG_FILE_DIR])) {
            $tmpPath = $parameters[\Magento\ImportExport\Model\Import::FIELD_NAME_IMG_FILE_DIR];
        } else {
            $tmpPath = $dirAddon . $DS . $this->mediaDirectory->getRelativePath('import') . $DS . $vendorId;
            if (!file_exists($tmpPath)) {
                mkdir($tmpPath, 0777, true);
            }
        }
        if (!$this->fileUploader->setTmpDir($tmpPath)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('File directory \'%1\' is not readable.', $tmpPath)
            );
        }
        $destinationDir = "downloadable/files/" . $type;
        $destinationPath = $dirAddon . $DS . $this->mediaDirectory->getRelativePath($destinationDir);

        $this->mediaDirectory->create($destinationPath);
        if (!$this->fileUploader->setDestDir($destinationPath)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('File directory \'%1\' is not writable.', $destinationPath)
            );
        }
        return $this->fileUploader;
    }

    /**
     * Get all allowed extensions
     *
     * @return array
     */
    protected function getAllowedExtensions()
    {
        $result = [];
        foreach (array_keys($this->fileHelper->getAllMineTypes()) as $option) {
            $result[] = substr($option, 1);
        }
        return $result;
    }
}
