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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Model\Api\Affiliate;

/**
 * Class AccountCheck
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class AccountCheck extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * AccountCheck constructor.
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->affiliateAccountFactory = $affiliateAccountFactory;
    }

    /**
     * @param $customerId
     * @return array
     */
    public function accountCheck($customerId)
    {

        $affiliateData = [];
        $affiliate = $this->affiliateAccountFactory->create()->load($customerId, 'customer_id');
        if ($affiliate->getData()) {
            $affiliateData['affiliate_exist'] = true;
            $affiliateData['approval_required'] = $affiliate->getStatus();
        } else {
            $affiliateData['affiliate_exist'] = false;
        }
        return ['data' => $affiliateData];
    }
}
