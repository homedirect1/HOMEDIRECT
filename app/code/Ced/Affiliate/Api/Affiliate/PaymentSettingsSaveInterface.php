<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Affiliate\Api\Affiliate;

/**
 * Interface DashboardInterface
 * @api
 */
interface PaymentSettingsSaveInterface
{
    /**
    * Gets the json.
    *
    * @param mixed parameters
    *
    * @return string
    */
    public function savePaymentMethod($parameters);
}