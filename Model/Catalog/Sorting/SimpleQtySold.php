<?php

/**
 * @category  PHP
 * @package   Au\PbProductSortBy
 * @author    Asrar Alam
 * @license   OSL See COPYING.txt for license details.
 *
 */

declare(strict_types=1);

namespace Au\PbProductSortBy\Model\Catalog\Sorting;

use Magento\Framework\DB\Select;
use Magento\PageBuilder\Model\Catalog\Sorting\OptionInterface;

/**
 * Class for sorting by specified attribute
 */
class SimpleQtySold implements OptionInterface
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $sortDirection;

    /**
     * @var string
     */
    private $attributeField;

    /**
     * @param string $label
     * @param string $sortDirection
     * @param string $attributeField
     */
    public function __construct(
        string $label,
        string $sortDirection,
        string $attributeField
    ) {
        $this->label = $label;
        $this->sortDirection = $sortDirection;
        $this->attributeField = $attributeField;
    }

    /**
     * @inheritdoc
     */
    public function sort(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ): \Magento\Catalog\Model\ResourceModel\Product\Collection {
        $collection->getSelect()->reset(Select::ORDER);

        $collection->getSelect()->join(
            ['soi' => 'sales_order_item'],
            'e.row_id=soi.product_id',
            ["qty_ordered" => "soi.qty_ordered"]
        )
            ->where('soi.qty_ordered > ?', 0);

        $collection->addAttributeToSort($this->attributeField, $this->sortDirection);
        $collection->addAttributeToSort('entity_id', $this->sortDirection);

        return $collection;
    }

    /**
     * @inheritdoc
     */
    public function getLabel(): \Magento\Framework\Phrase
    {
        return __($this->label);
    }
}
