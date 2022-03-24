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
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsProduct\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

/**
 * Class Related
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Related extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Related
{
    const DATA_SCOPE = '';

    const DATA_SCOPE_RELATED = 'related';

    const DATA_SCOPE_UPSELL = 'upsell';

    const DATA_SCOPE_CROSSSELL = 'crosssell';

    const GROUP_RELATED = 'related';

    /**
     * @var string
     */
    private static $previousGroup = 'search-engine-optimization';

    /**
     * @var int
     */
    private static $sortOrder = 90;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ProductLinkRepositoryInterface
     */
    protected $productLinkRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var AttributeSetRepositoryInterface
     */
    protected $attributeSetRepository;

    /**
     * @var string
     */
    protected $scopeName;

    /**
     * @var string
     */
    protected $scopePrefix;

    /**
     * @var \Magento\Catalog\Ui\Component\Listing\Columns\Price
     */
    private $priceModifier;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Related constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Catalog\Ui\Component\Listing\Columns\Price $priceModifier
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param ProductLinkRepositoryInterface $productLinkRepository
     * @param ProductRepositoryInterface $productRepository
     * @param ImageHelper $imageHelper
     * @param Status $status
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param string $scopeName
     * @param string $scopePrefix
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Catalog\Ui\Component\Listing\Columns\Price $priceModifier,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        ProductLinkRepositoryInterface $productLinkRepository,
        ProductRepositoryInterface $productRepository,
        ImageHelper $imageHelper,
        Status $status,
        AttributeSetRepositoryInterface $attributeSetRepository,
        $scopeName = '',
        $scopePrefix = ''

    )
    {
        parent::__construct($locator, $urlBuilder, $productLinkRepository, $productRepository, $imageHelper, $status, $attributeSetRepository);
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->productLinkRepository = $productLinkRepository;
        $this->productRepository = $productRepository;
        $this->status = $status;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->scopeName = $scopeName;
        $this->scopePrefix = $scopePrefix;
        $this->scopeConfig = $scopeConfig;
        $this->_assetRepo = $assetRepo;
        $this->priceModifier = $priceModifier;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {


        $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_RELATED => [
                    'children' => [
                        $this->scopePrefix . static::DATA_SCOPE_RELATED => $this->getRelatedFieldset(),
                        $this->scopePrefix . static::DATA_SCOPE_UPSELL => $this->getUpSellFieldset(),
                        $this->scopePrefix . static::DATA_SCOPE_CROSSSELL => $this->getCrossSellFieldset(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Related Products, Up-Sells, and Cross-Sells'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::DATA_SCOPE,
                                'sortOrder' =>
                                    $this->getNextGroupSortOrder(
                                        $meta,
                                        self::$previousGroup,
                                        self::$sortOrder
                                    ),
                            ],
                        ],

                    ],
                ],
            ]
        );

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();

        if (!$productId) {
            return $data;
        }

        $priceModifier = $this->getPriceModifier();
        /**
         * Set field name for modifier
         */
        $priceModifier->setData('name', 'price');

        foreach ($this->getDataScopes() as $dataScope) {
            $data[$productId]['links'][$dataScope] = [];
            foreach ($this->productLinkRepository->getList($product) as $linkItem) {
                if ($linkItem->getLinkType() !== $dataScope) {
                    continue;
                }

                /** @var \Magento\Catalog\Model\Product $linkedProduct */
                $linkedProduct = $this->productRepository->get(
                    $linkItem->getLinkedProductSku(),
                    false,
                    $this->locator->getStore()->getId()
                );
                $data[$productId]['links'][$dataScope][] = $this->fillData($linkedProduct, $linkItem);
            }
            if (!empty($data[$productId]['links'][$dataScope])) {
                $dataMap = $priceModifier->prepareDataSource([
                    'data' => [
                        'items' => $data[$productId]['links'][$dataScope]
                    ]
                ]);
                $data[$productId]['links'][$dataScope] = $dataMap['data']['items'];
            }
        }

        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $productId;
        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_store_id'] = $this->locator->getStore()->getId();

        return $data;
    }

    /**
     * Get price modifier
     *
     * @return \Magento\Catalog\Ui\Component\Listing\Columns\Price
     * @deprecated
     */
    private function getPriceModifier()
    {
        return $this->priceModifier;
    }

    /**
     * Prepare data column
     *
     * @param ProductInterface $linkedProduct
     * @param ProductLinkInterface $linkItem
     * @return array
     */
    protected function fillData(ProductInterface $linkedProduct, ProductLinkInterface $linkItem)
    {
        $params = [];
        $store = $this->storeManager->getStore();
        if ($linkedProduct->getThumbnail() && $linkedProduct->getThumbnail() != "no_selection")
            $imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $linkedProduct->getThumbnail();
        else
            $imageUrl = $this->_assetRepo->getUrlWithParams('Ced_CsProduct/images/thumbnail.jpg', $params);
        return [
            'id' => $linkedProduct->getId(),
            'thumbnail' => $imageUrl,
            'name' => $linkedProduct->getName(),
            'status' => $this->status->getOptionText($linkedProduct->getStatus()),
            'attribute_set' => $this->attributeSetRepository
                ->get($linkedProduct->getAttributeSetId())
                ->getAttributeSetName(),
            'sku' => $linkItem->getLinkedProductSku(),
            'price' => $linkedProduct->getPrice(),
            'position' => $linkItem->getPosition(),
        ];
    }

    /**
     * Retrieve all data scopes
     *
     * @return array
     */
    protected function getDataScopes()
    {
        return [
            static::DATA_SCOPE_RELATED,
            static::DATA_SCOPE_CROSSSELL,
            static::DATA_SCOPE_UPSELL,
        ];
    }

    /**
     * Prepares config for the Related products fieldset
     *
     * @return array
     */
    protected function getRelatedFieldset()
    {
        if (!$this->scopeConfig->getValue('ced_csmarketplace/vproducts/relatedproducts', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $content = __(
                'You are not allowed to add Related Products'
            );
            return [
                'childrens' => [
                    'button_set' => $this->getButtonSet(
                        $content,
                        __('Add Related Products'),
                        $this->scopePrefix . static::DATA_SCOPE_RELATED
                    ),
                    'modal' => $this->getGenericModal(
                        __('Add Related Products'),
                        $this->scopePrefix . static::DATA_SCOPE_RELATED
                    ),
                    static::DATA_SCOPE_RELATED => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_RELATED),
                ],
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'admin__fieldset-section',
                            'label' => __('You are not allowed to add Related Products'),
                            'collapsible' => false,
                            'componentType' => Fieldset::NAME,
                            'dataScope' => '',
                            'sortOrder' => 10,
                        ],
                    ],
                ]
            ];
        }
        $content = __(
            'Related products are shown to customers in addition to the item the customer is looking at.'
        );

        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Related Products'),
                    $this->scopePrefix . static::DATA_SCOPE_RELATED
                ),
                'modal' => $this->getGenericModal(
                    __('Add Related Products'),
                    $this->scopePrefix . static::DATA_SCOPE_RELATED
                ),
                static::DATA_SCOPE_RELATED => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_RELATED),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Related Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 10,
                    ],
                ],
            ]
        ];
    }

    /**
     * Prepares config for the Up-Sell products fieldset
     *
     * @return array
     */
    protected function getUpSellFieldset()
    {

        if (!$this->scopeConfig->getValue('ced_csmarketplace/vproducts/upsells', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $content = __(
                'You are not allowed to add UpSell Products'
            );
            return [
                'childrens' => [
                    'button_set' => $this->getButtonSet(
                        $content,
                        __('Add Up-Sell Products'),
                        $this->scopePrefix . static::DATA_SCOPE_UPSELL
                    ),
                    'modal' => $this->getGenericModal(
                        __('Add Up-Sell Products'),
                        $this->scopePrefix . static::DATA_SCOPE_UPSELL
                    ),
                    static::DATA_SCOPE_UPSELL => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_UPSELL),
                ],
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'admin__fieldset-section',
                            'label' => __('You are not allowed to add UpSell Products'),
                            'collapsible' => false,
                            'componentType' => Fieldset::NAME,
                            'dataScope' => '',
                            'sortOrder' => 20,
                        ],
                    ],
                ]
            ];
        }


        $content = __(
            'An up-sell item is offered to the customer as a pricier or higher-quality' .
            ' alternative to the product the customer is looking at.'
        );

        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Up-Sell Products'),
                    $this->scopePrefix . static::DATA_SCOPE_UPSELL
                ),
                'modal' => $this->getGenericModal(
                    __('Add Up-Sell Products'),
                    $this->scopePrefix . static::DATA_SCOPE_UPSELL
                ),
                static::DATA_SCOPE_UPSELL => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_UPSELL),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Up-Sell Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 20,
                    ],
                ],
            ]
        ];
    }

    /**
     * Prepares config for the Cross-Sell products fieldset
     *
     * @return array
     */
    protected function getCrossSellFieldset()
    {
        if (!$this->scopeConfig->getValue('ced_csmarketplace/vproducts/crosssells', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $content = __(
                'You are not allowed to add CrossSell Products'
            );
            return [
                'childrens' => [
                    'button_set' => $this->getButtonSet(
                        $content,
                        __('Add Cross-Sell Products'),
                        $this->scopePrefix . static::DATA_SCOPE_CROSSSELL
                    ),
                    'modal' => $this->getGenericModal(
                        __('Add Cross-Sell Products'),
                        $this->scopePrefix . static::DATA_SCOPE_CROSSSELL
                    ),
                    static::DATA_SCOPE_CROSSSELL => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_CROSSSELL),
                ],
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'admin__fieldset-section',
                            'label' => __('You are not allowed to add CrossSell Products'),
                            'collapsible' => false,
                            'componentType' => Fieldset::NAME,
                            'dataScope' => '',
                            'sortOrder' => 30,
                        ],
                    ],
                ]
            ];
        }

        $content = __(
            'These "impulse-buy" products appear next to the shopping cart' .
            ' as cross-sells to the items already in the shopping cart.'
        );

        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Cross-Sell Products'),
                    $this->scopePrefix . static::DATA_SCOPE_CROSSSELL
                ),
                'modal' => $this->getGenericModal(
                    __('Add Cross-Sell Products'),
                    $this->scopePrefix . static::DATA_SCOPE_CROSSSELL
                ),
                static::DATA_SCOPE_CROSSSELL => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_CROSSSELL),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Cross-Sell Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 30,
                    ],
                ],
            ]
        ];
    }

    /**
     * Retrieve button set
     *
     * @param Phrase $content
     * @param Phrase $buttonTitle
     * @param string $scope
     * @return array
     */
    protected function getButtonSet(Phrase $content, Phrase $buttonTitle, $scope)
    {
        $modalTarget = $this->scopeName . '.' . static::GROUP_RELATED . '.' . $scope . '.modal';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => $content,
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'button_' . $scope => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $modalTarget . '.' . $scope . '_product_listing',
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title' => $buttonTitle,
                                'provider' => null,
                            ],
                        ],
                    ],

                ],
            ],
        ];
    }

    /**
     * Prepares config for modal slide-out panel
     *
     * @param Phrase $title
     * @param string $scope
     * @return array
     */
    protected function getGenericModal(Phrase $title, $scope)
    {
        $listingTarget = $scope . '_product_listing';

        $modal = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'options' => [
                            'title' => $title,
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ],
                                [
                                    'text' => __('Add Selected Products'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $listingTarget,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $listingTarget => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => $listingTarget,
                                'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                                'selectionsProvider' => $listingTarget . '.' . $listingTarget . '.product_columns.ids',
                                'ns' => $listingTarget,
                                'render_url' => $this->urlBuilder->getUrl('csproduct/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.current_product_id',
                                    'storeId' => '${ $.provider }:data.product.current_store_id',
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.current_product_id',
                                    'storeId' => '${ $.externalProvider }:params.current_store_id',
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $modal;
    }

    /**
     * Retrieve grid
     *
     * @param string $scope
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getGrid($scope)
    {
        $dataProvider = $scope . '_product_listing';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Ced_CsProduct/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $dataProvider,
                        'map' => [
                            'id' => 'entity_id',
                            'name' => 'name',
                            'status' => 'status_text',
                            'attribute_set' => 'attribute_set_text',
                            'sku' => 'sku',
                            'price' => 'price',
                            'thumbnail' => 'thumbnail_src',
                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }'
                        ],
                        'sortOrder' => 2,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Ced_CsProduct/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => $this->fillMeta(),
                ],
            ],
        ];
    }

    /**
     * Retrieve meta column
     *
     * @return array
     */
    protected function fillMeta()
    {
        return [
            'id' => $this->getTextColumn('id', false, __('ID'), 0),
            'thumbnail' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'elementTmpl' => 'ui/dynamic-rows/cells/thumbnail',
                            'dataType' => Text::NAME,
                            'dataScope' => 'thumbnail',
                            'fit' => true,
                            'label' => __('Thumbnail'),
                            'sortOrder' => 10,
                        ],
                    ],
                ],
            ],
            'name' => $this->getTextColumn('name', false, __('Name'), 20),
            'status' => $this->getTextColumn('status', true, __('Status'), 30),
            'attribute_set' => $this->getTextColumn('attribute_set', false, __('Attribute Set'), 40),
            'sku' => $this->getTextColumn('sku', true, __('SKU'), 50),
            'price' => $this->getTextColumn('price', true, __('Price'), 60),
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 70,
                            'fit' => true,
                        ],
                    ],
                ],
            ],
            'position' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Number::NAME,
                            'formElement' => Input::NAME,
                            'componentType' => Field::NAME,
                            'dataScope' => 'position',
                            'sortOrder' => 80,
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Retrieve text column structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    protected function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];

        return $column;
    }
}
