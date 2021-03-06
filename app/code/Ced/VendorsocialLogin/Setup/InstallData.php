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
  * @package     VendorsocialLogin
  * @author      CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://cedcommerce.com/license-agreement.txt
  */

/**
 * VendorsocialLogin 	installer
 *
 * @category   	Ced
 * @package    	Ced_VendorsocialLogin
 * @author		CedCommerce Magento Core Team <connect@cedcommerce.com>
 */

namespace Ced\VendorsocialLogin\Setup;

use Magento\Eav\Setup\EavSetup;

use Magento\Eav\Setup\EavSetupFactory;

use Magento\Framework\Setup\InstallDataInterface;

use Magento\Framework\Setup\ModuleContextInterface;

use Magento\Framework\Setup\ModuleDataSetupInterface;


/**
 * @codeCoverageIgnore
 */

class InstallData implements InstallDataInterface

{

    /**
    * EAV setup factory
    *
    * @var EavSetupFactory
    */

    private $eavSetupFactory;

 

    /**

     * Init

     *

     * @param EavSetupFactory $eavSetupFactory

     */

    public function __construct(EavSetupFactory $eavSetupFactory)

    {

        $this->eavSetupFactory = $eavSetupFactory;

    }

 

    /**

     * {@inheritdoc}

     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)

     */

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)

    {

        /** @var EavSetup $eavSetup */

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

 

        /**

         * Add attributes to the eav/attribute

         */



		 $eavSetup->addAttribute(

            \Magento\Customer\Model\Customer::ENTITY,

            'ced_sociallogin_gid'

            , array(

					'type' => 'text',

					'visible' => false,

					'required' => false,

					'user_defined' => false,

					'label' => 'ced Google id',

					'system' => false   // Must be non system, else customer service can not update it

				)

        );

		$eavSetup->addAttribute(

            \Magento\Customer\Model\Customer::ENTITY,

            'ced_sociallogin_gtoken' ,

			 array(

					'type' => 'text',

					'visible' => false,

					'required' => false,

					'user_defined' => false,

					'label' => 'ced Google token',

					'system' => false   // Must be non system, else customer service can not update it

				)

        );

		$eavSetup->addAttribute(

            \Magento\Customer\Model\Customer::ENTITY,

           'ced_sociallogin_fid' ,

		    array(

					'type' => 'text',

					'visible' => false,

					'required' => false,

					'user_defined' => false,

					'label' => 'ced Facebook id',

					'system' => false   // Must be non system, else customer service can not update it

				)

        );

		$eavSetup->addAttribute(

            \Magento\Customer\Model\Customer::ENTITY,

           'ced_sociallogin_ftoken' ,

		    array(

					'type' => 'text',

					'visible' => false,

					'required' => false,

					'user_defined' => false,

					'label' => 'ced Facebook token',

					'system' => false   // Must be non system, else customer service can not update it

				)

        );

		$eavSetup->addAttribute(

            \Magento\Customer\Model\Customer::ENTITY,

            'ced_sociallogin_tid' ,

			 array(

					'type' => 'text',

					'visible' => false,

					'required' => false,

					'user_defined' => false,

					'label' => 'ced Twitter id',

					'system' => false   // Must be non system, else customer service can not update it

				)

        );

		$eavSetup->addAttribute(

            \Magento\Customer\Model\Customer::ENTITY,

            'ced_sociallogin_ttoken' ,

			 array(

					'type' => 'text',

					'visible' => false,

					'required' => false,

					'user_defined' => false,

					'label' => 'ced Twitter token',

					'system' => false   // Must be non system, else customer service can not update it

				)

        );

		$eavSetup->addAttribute(

            \Magento\Customer\Model\Customer::ENTITY,

            'ced_sociallogin_lid' ,

			 array(

					'type' => 'text',

					'visible' => false,

					'required' => false,

					'user_defined' => false,

					'label' => 'ced Linkedin id',

					'system' => false   // Must be non system, else customer service can not update it

				)

        );

		$eavSetup->addAttribute(

            \Magento\Customer\Model\Customer::ENTITY,

            'ced_sociallogin_ltoken' ,

			 array(

					'type' => 'text',

					'visible' => false,

					'required' => false,

					'user_defined' => false,

					'label' => 'ced Linkedin token',

					'system' => false   // Must be non system, else customer service can not update it

				)

        );
    }
}

