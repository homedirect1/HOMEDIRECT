<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
  * @package  Ced_GShop
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\GShop\Model\Source;

/**
 * Class Productstatus
 * @package Ced\GShop\Model\Source
 */
class Productstatus extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const NOT_UPLOADED = 1;
    const INVALID = 2;
    const VALID = 3;
    const UPLOADED = 4;
    const END_LISTING = 5;
    const APPROVED = 6;
    const DISAPPROVED = 7;
    const NOTFOUND = 8;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }

    /**
    * @return array
    */
    public function getAllOptions()
    {
        return [
            [
                'value' => self::NOT_UPLOADED,
                'label' => __('Not Uploaded')
            ],            
            [
                'value' => self::INVALID,
                'label' => __('Invalid On Google Shopping')
            ],
            [
                'value' => self::UPLOADED,
                'label' => __('Uploaded on Google Shopping')
            ],                      
            [
                'value' => self::VALID,
                'label' => __('Ready To Uplaod')
            ],
            [
                'value' => self::END_LISTING,
                'label' => __('End Listing on Google Shopping')
            ],
            [
                'value' => self::APPROVED,
                'label' => __('Approved')
            ],
            [
                'value' => self::DISAPPROVED,
                'label' => __('Disapproved')
            ],
            [
                'value' => self::NOTFOUND,
                'label' => __('item not found')
            ],
        ];
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        foreach ($this->getAllOptions() as $option) {
            $options[$option['value']] =(string)$option['label'];
        }
        return $options;
    }

    /**
     * @return array
     */
    public function getAllOption()
    {
        $options = $this->getOptionArray();
        array_unshift($options, ['value' => '', 'label' => '']);
        return $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $res = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }
        return $res;
    }

    /**
     * @param int|string $optionId
     * @return mixed|null
     */
    public function getOptionText($optionId)
    {
        $options = $this->getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

}
