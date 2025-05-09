<?php

namespace App\Services\Template;

use App\Models\Template;
use App\Services\Template\ParseClass\QuittanceLoyerTemplateParse;
use App\Services\Template\ParseClass\TemplateParseInterface;

class TemplateService
{
    public const TAG_NAME_ORDER_LINES = 'order_lines';
    public const MODEL_PARCEL = 'Parcel';
    public const MODEL_ORDER = 'Order';
    public const MODEL_SITE = 'Site';
    public const MODEL_GENERAL = 'General';
    public const MODEL_DOCUMENT = 'Document';
    public const MODEL_DELIVERY_NOTE_EXTRA = 'Extra';
    public const MODEL_LINES = 'Lines';

    public const COMMON_ALLOWED_MODEL_SITE = [
        'company' => 'Company',
        'company_name' => 'Name',
        'company_url' => 'Site',
        'address' => 'Address',
        'zip_code' => 'Zipcode',
        'city' => 'City',
        'country_code' => 'Country code',
        'country' => 'Country',
        'email' => 'E-mail',
        'phone' => 'Phone',
        'cvr' => 'CVR',
    ];
    public const COMMON_ALLOWED_MODEL_GENERAL = [
        'date' => 'Current date',
        'logo' => 'Logo',
    ];
    public const COMMON_ALLOWED_MODEL_DOCUMENT = [
        'page_number' => 'Page number',
        'number_of_pages' => 'Number of pages',
    ];

    public const ALLOWED_MODEL_COLUMNS = [
        Template::TYPE_QUITTANCE_LOYER => [
            self::MODEL_ORDER => [
                'id' => 'Order ID',
                'source_order_number' => 'Source order ID',
                'site_name' => 'Site name',
                'barcode' => 'Barcode',
                self::TAG_NAME_ORDER_LINES => 'Order lines',
                'total_weight' => 'Total weight',
                'subtotal' => 'Subtotal',
                'VAT_amount' => 'VAT amount',
                'created_at' => 'Created at',
                'imported_at' => 'Imported at',
                'total_price' => 'Total price',
                'currency_code' => 'Currency code',
                'vat_percentage' => 'VAT percentage',
                'sales_discount' => 'Discount',
                'comment' => 'Comment',
                'customer_comment' => 'Customer comment',
                'customer_id' => 'Customer nymber',
                'customer_name' => 'Customer name',
                'customer_company' => 'Customer company',
                'customer_address' => 'Customer address',
                'customer_address2' => 'Customer address2',
                'customer_zip_code' => 'Customer zip code',
                'customer_city' => 'Customer city',
                'customer_country' => 'Customer country',
                'customer_country_code' => 'Customer country code',
                'customer_phone' => 'Customer phone',
                'customer_email' => 'Customer email',
                'customer_ean' => 'Customer EAN',
                'delivery_name' => 'Delivery name',
                'delivery_company' => 'Delivery company',
                'delivery_address' => 'Delivery address',
                'delivery_address2' => 'Delivery address2',
                'delivery_zip_code' => 'Delivery zip code',
                'delivery_city' => 'Delivery city',
                'delivery_country' => 'Delivery country',
                'delivery_country_code' => 'Delivery country code',
                'delivery_phone' => 'Delivery phone',
                'delivery_email' => 'Delivery email',
                'delivery_ean' => 'Delivery EAN',
                'delivery_parcel_shop' => 'Delivery parcel shop',
                'shipping_id' => 'Shipping ID',
                'shipping_name' => 'Shipping name',
                'shipping_fee' => 'Shipping fee',
                'shipping_fee_incl_vat' => 'Shipping fee incl. VAT',
                'pay_id' => 'Payment ID',
                'pay_name' => 'Payment name',
                'reserved_field1' => 'Reserved field1',
                'reserved_field2' => 'Reserved field2',
                'reserved_field3' => 'Reserved field3',
                'reserved_field4' => 'Reserved field4',
                'reserved_field5' => 'Reserved field5',
            ],
            self::MODEL_SITE => self::COMMON_ALLOWED_MODEL_SITE,
            self::MODEL_GENERAL => self::COMMON_ALLOWED_MODEL_GENERAL,
            self::MODEL_DOCUMENT => self::COMMON_ALLOWED_MODEL_DOCUMENT,
            self::MODEL_DELIVERY_NOTE_EXTRA => [
                'box' => 'Box',
            ],
            self::MODEL_LINES => [
                'source_line_id' => 'Source ID',
                'sku' => 'SKU',
                'product_name' => 'Product name',
                'variant_title' => 'Variant',
                'quantity' => 'Quantity',
                'unit_price' => 'Unit price',
                'unit_price_incl_vat' => 'Unit price incl. VAT',
                'total_price' => 'Total price',
                'total_price_incl_vat' => 'Total price incl. VAT',
                'discount' => 'Discount',
                'product_picture' => 'Product picture',
                'locations' => 'Locations',
                'product_barcode' => 'Barcode',
                'ean' => 'EAN',
                'total_stock' => 'Total stock',
                'sellable' => 'Sellable',
            ]
        ],
    ];

    public const DEFAULT_QUITTANCE_LOYER = 1;

    public static array $mappingDefaultTemplates = [
        self::DEFAULT_QUITTANCE_LOYER => 'quittance-loyer.json',
    ];

    public static array $getClassParseTemplate = [
        Template::TYPE_QUITTANCE_LOYER => QuittanceLoyerTemplateParse::class,
    ];

    /**
     * @param int $type
     * @param string $raw
     * @param ...$args
     * @return \stdClass
     */
    public function formatRaw(int $type, string $raw, ...$args)
    {
        /**
         * @var TemplateParseInterface $class
         */
        $class = new self::$getClassParseTemplate[$type]($args);

        $raw = $class->handle($type, $raw);

        $result = json_decode($raw);

//        $result = $class->replaceCssClass($result);

        return $result;
    }

    /**
     * @param int $type
     * @param string $raw
     * @return array
     * @throws \Exception
     */
    public function validateBody(int $type, string $raw): array
    {
        /**
         * @var TemplateParseInterface $class
         */
        $class = new self::$getClassParseTemplate[$type]([]);

        return $class->validateBody($type, $raw);
    }
}
