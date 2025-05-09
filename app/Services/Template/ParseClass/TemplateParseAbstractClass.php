<?php

namespace App\Services\Template\ParseClass;

use App\Helpers\AppSettingHelper;
use App\Models\Admin\AppSetting;
use App\Models\CompanyAddress;
use App\Models\Line;
use App\Models\Operations;
use App\Models\Template;
//use App\Repositories\CompanyAddress\CompanyAddressRepositoryInterface;
use App\Services\StockSystem\LocationService;
use App\Services\Template\TemplateService;
use DB;

abstract class TemplateParseAbstractClass implements TemplateParseInterface
{
   // protected CompanyAddressRepositoryInterface $companyAddressRepo;

    public function __construct()
    {
        //$this->companyAddressRepo = app()->make(CompanyAddressRepositoryInterface::class);
    }

    /**
     * @param int $type
     * @param string $raw
     * @return array
     * @throws \Exception
     */
    public function validateBody(int $type, string $raw): array
    {
        preg_match_all("/\[\[(.*?)\]\]/", $raw, $matches);

        $fields = $matches[1];

        $mapping = [];

        foreach ($fields as $field) {
            $notFound = true;
            foreach (TemplateService::ALLOWED_MODEL_COLUMNS[$type] as $model => $columns) {
                if (array_key_exists($field, $columns) || array_key_exists(ltrim($field, '/'), $columns)) {
                    $mapping[$model][] = $field;
                    $notFound = false;
                }
            }

            if ($notFound) {
                throw new \Exception("Field [[$field]] is not available!");
            }
        }

        return $mapping;
    }

    /**
     * @param \stdClass $rawResult
     * @return \stdClass
     */
    public function replaceCssClass(\stdClass $rawResult): \stdClass
    {
        foreach ($rawResult as $key => $value) {
//            $rawResult->{$key} = str_replace('class="border-bottom"', 'style="border-bottom: 1px solid gray;"', $rawResult->{$key});
//            $rawResult->{$key} = str_replace('class="border-top"', 'style="border-top: 1px solid gray;"', $rawResult->{$key});
//            $rawResult->{$key} = str_replace('class="border-left"', 'style="border-left: 1px solid gray;"', $rawResult->{$key});
//            $rawResult->{$key} = str_replace('class="border-right"', 'style="border-right: 1px solid gray;"', $rawResult->{$key});
            $rawResult->{$key} = str_replace('class="border"', 'style="border: 1px solid gray;"', $rawResult->{$key});
            $rawResult->{$key} = str_replace('<table', '<table cellspacing="0" cellpadding="0" ', $rawResult->{$key});
        }

        return $rawResult;
    }

    /**
     * @param CompanyAddress $companyAddress
     * @param \stdClass|null $settings
     * @return array
     */
    protected function mappingGeneral(CompanyAddress $companyAddress, ?\stdClass $settings = null): array
    {
        $width = $settings->logoSize ?? 100;
        return [
            'date' => now()->format('d-m-Y'),
            'logo' => '<a href="' . $companyAddress->site . '">
                            <img
                                src="' . asset($companyAddress->logo) . '"
                                :alt="' . $companyAddress->company . '"
                                style="width: ' . $width . 'px"
                            />
                        </a>',
        ];
    }

    /**
     * @return string[]
     */
    protected function mappingDocument(): array
    {
        return [
            'page_number' => '{PAGENO}',
            'number_of_pages' => '{nbpg}',
        ];
    }

    /**
     * @param Operations $operation
     * @return string
     */
    protected function getOrderLines(Operations $operation, string $raw, \stdClass $data)
    {
        $settings = $data->settings ?? [];

        $locationService = app()->make(LocationService::class);
        $enableBundle = AppSettingHelper::isEnable(AppSetting::TYPE_BUNDLE);

        $orderLines = $operation->lines;

        $columnsRaw = $data->orderLines;
        if (empty($columnsRaw)) {
            return '';
        }

        $linesColumnsRaw = [];
        $lineColumnsTitle = [];
        foreach ($columnsRaw as $k => $item) {
            $lineColumnsTitle[] = $item->name;
            $linesColumnsRaw[$k] = $item->columnData;
        }

        $subLines = [];
        $lineColumnsLines = [];
        $columnClasses = [];
        foreach ($orderLines as $key => $orderLine) {
            $classes = [];

            if ($enableBundle && $orderLine->type === Line::TYPE_BUNDLE_COMPONENT) {
                continue;
            }

            if ($enableBundle && $orderLine->type === Line::TYPE_BUNDLE_MASTER) {
                $classes[] = "bundle-product";
            }
            if ($orderLine->quantity >= 2) {
                $classes[] = 'order-line-multi';
            }
            if ($operation->status < 0 && $orderLine->shipment != $operation->part_id) {
                $classes[] = "order-line-through";
            }

            $lineColumnsLine = [];
            foreach ($linesColumnsRaw as $item) {
                $lineColumnsLine[] = $this->getMappingOrderLines($orderLine, $item, $locationService, $settings);
                if ($enableBundle && $orderLine->subLinesWithShipment->count() > 0) {
                    foreach ($orderLine->subLinesWithShipment as $subLine) {
                        $subLineData = [];
                        foreach ($linesColumnsRaw as $subLineItem) {
                            $subLineData[] = $this->getMappingOrderLines($subLine, $subLineItem, $locationService, $settings);
                        }
                        $subLines[$key][] = $subLineData;
                    }
                }
            }


            $lineColumnsLines[$key] = $lineColumnsLine;

            //TODO other rules for classes
            $columnClasses[$key] = \Arr::toCssClasses($classes);
        }

        $result = view('pdf._order_lines_template', compact('lineColumnsTitle', 'lineColumnsLines', 'subLines', 'columnClasses'))->render();

        return str_replace("[[" . TemplateService::TAG_NAME_ORDER_LINES . "]]", $result, $raw);
    }

    /**
     * @param Line $orderLine
     * @param string $raw
     * @param LocationService $locationService
     * @param \stdClass|null $settings
     * @return array
     */
    protected function getMappingOrderLines(Line $orderLine, string $raw, LocationService $locationService, ?\stdClass $settings = null)
    {
        $fields = $this->validateBody(Template::TYPE_DELIVERY_NOTE, $raw);
        $linesColumns = $fields[TemplateService::MODEL_LINES];
        foreach ($linesColumns as $column) {
            switch ($column) {
                case 'sku':
                    $raw = str_replace("[[$column]]", $orderLine->product->sku ?? $orderLine->source_product_number, $raw);
                    break;
                case 'unit_price':
                case 'total_price':
                case 'discount':
                    $raw = str_replace("[[$column]]", number_format($orderLine->{$column}, 2, ',', ' '), $raw);
                    break;
                case 'unit_price_incl_vat':
                    $raw = str_replace("[[$column]]", number_format($orderLine->unit_price * (1 + $orderLine->vat_percentage * 0.01), 2, ',', ' '), $raw);
                    break;
                case 'total_price_incl_vat':
                    $raw = str_replace("[[$column]]", number_format($orderLine->total_price * (1 + $orderLine->vat_percentage * 0.01), 2, ',', ' '), $raw);
                    break;
                case 'product_picture':
                    $img = $orderLine->product->picture_link ?? null;
                    if ($img && @getimagesize($img)) {
                        $raw = str_replace("[[$column]]", '<img style="width: ' . ($settings->productPictureSize ?? '5') . '%; max-width:120px;" src="' . $img . '">', $raw);
                    } else {
                        $raw = str_replace("[[$column]]", '', $raw);
                    }
                    break;
                case 'locations':
                    $raw = str_replace("[[$column]]", $orderLine->product !== null
                        ? implode('<br>', \Arr::pluck($locationService->getProductLocations($orderLine->product, $orderLine->quantity), 'location')) : '', $raw);
                    break;
                case 'product_barcode':
                    $barcode = $orderLine->product->bar_code_number ?? null;
                    $raw = str_replace("[[$column]]", !empty($barcode)
                        ? ('<barcode code="' . $barcode . '" type="C128A" size="' . ($settings->productBarcodeSize ?? '0.6') . '" height="0.6"/>') : '', $raw);
                    break;
                case 'ean':
                    $raw = str_replace("[[$column]]", $orderLine->product->bar_code_number ?? '', $raw);
                    break;
                case 'total_stock':
                    $raw = str_replace("[[$column]]", $orderLine->product->total_stock ?? '-', $raw);
                    break;
                case 'sellable':
                    $raw = str_replace("[[$column]]", $orderLine->product->stock->sellable ?? '-', $raw);
                    break;
                default:
                    $raw = str_replace("[[$column]]", $orderLine->{$column}, $raw);
            }
        }

        return $raw;
    }

    /**
     * @param string $string
     * @param string $start
     * @param string $end
     * @return string
     */
    protected function getStringBetween(string $string, string $start, string $end): string
    {
        $ini = mb_strpos($string, $start);
        if ($ini == 0) return '';
        $len = mb_strpos($string, $end, $ini) - $ini + mb_strlen($end);
        return mb_substr($string, $ini, $len);
    }

    /**
     * @param string $raw
     * @param string $column
     * @param string|null $replace
     * @return string
     */
    private function getSubString(string $raw, string $column, ?string $replace = ''): string
    {
        return str_replace(["[[$column]]", "[[/$column]]"], $replace, $raw);
    }

    protected function getOrderSubTotalLines(Order $order)
    {
        return $order->lines()
            ->where('unit_price', '>', 0)
            ->sum(DB::raw('(unit_price + discount) * quantity'));
    }

    /**
     * @param Order $order
     * @param \stdClass $data
     * @return string
     */
    protected function getOrderSubTotal(Order $order)
    {
        return number_format($this->getOrderSubTotalLines($order), 2, ',', ' ');
    }

    /**
     * @param Order $order
     * @return string
     */
    protected function getOrderVatAmount(Order $order)
    {
        return number_format($order->total_price - $this->getOrderSubTotalExceptVat($order), 2, ',', ' ');
    }

    /**
     * @param Order $order
     * @return float
     */
    protected function getOrderSubTotalExceptVat(Order $order)
    {
        return round($order->total_price / (1 + $order->vat_percentage * 0.01), 2);
    }

    /**
     * @param \stdClass $rawResult
     * @return \stdClass
     */
    public function improveSpamDetection(\stdClass $rawResult): \stdClass
    {
        foreach ($rawResult as $key => $value) {
            if ($key === 'body') {
                $rawResult->{$key} = view('template.emails.layout', ['body' => $value])->render();
            }
        }

        return $rawResult;
    }
    abstract public function handle(int $type, string $raw): string;
}
