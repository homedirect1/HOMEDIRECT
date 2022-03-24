
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
 * @category  Ced
 * @package   Ced_CsMultiShipping
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */


var config = {
    config: {
        mixins: {
            'Magento_ConfigurableProduct/js/configurable': {
                'Ced_CsMultiSeller/js/configurable-mixin': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Ced_CsMultiSeller/js/swatch-renderer-mixin': true
            }
        }
    }
};
