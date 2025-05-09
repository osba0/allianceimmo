<?php

namespace App\Services\Template\ParseClass;

use App\Models\Operations;
use App\Services\Template\TemplateService;

class QuittanceLoyerTemplateParse extends TemplateParseAbstractClass
{
    /**
     * @var Operation
     */
    protected $operation;

    /**
     * @var bool
     */
    protected $isBarcode;

    protected $box;

    /**
     * @param $args
     */
    public function __construct($args)
    {
        parent::__construct();

        $this->operation = $args[0] ?? null;
        $this->isBarcode = $args[1] ?? true;
        $this->box = $args[2] ?? false;

    }

    public function handle(int $type, string $raw): string
    {
        $site = 1; //$this->companyAddressRepo->findOneBySiteId($this->order->site_id);

        $rawData = json_decode($raw);

        $data = new \stdClass();
        foreach ($rawData as $key => $item) {
            $raw = $item;
            if (!is_string($raw)) {
                continue;
            }

            $fields = $this->validateBody($type, $raw);

            foreach ($fields as $className => $columns) {
                foreach ($columns as $column) {
                    switch ($className) {
                        case TemplateService::MODEL_ORDER:
                            switch ($column) {
                                case 'barcode':
                                    $raw = str_replace("[[$column]]", $this->getBarcode(), $raw);
                                    break;
                                /*case TemplateService::TAG_NAME_ORDER_LINES:
                                    $raw = $this->getOrderLines($this->operation, $raw, $rawData);
                                    break;
                                case 'subtotal':
                                    $raw = str_replace("[[$column]]", $this->getOrderSubTotal($this->operation), $raw);
                                    break;
                                case 'VAT_amount':
                                    $raw = str_replace("[[$column]]", $this->getOrderVatAmount($this->operation), $raw);
                                    break;
                                case 'created_at':
                                    $raw = str_replace("[[$column]]", $this->operation->created_at ? $this->operation->created_at->format('d-m-Y') : '', $raw);
                                    break;
                                case 'imported_at':
                                    $raw = str_replace("[[$column]]", $this->operation->imported_at ? $this->operation->imported_at->format('d-m-Y') : '', $raw);
                                    break;
                                case 'shipping_id':
                                    $raw = str_replace("[[$column]]", $this->operation->shipping->key, $raw);
                                    break;
                                case 'shipping_name':
                                    $raw = str_replace("[[$column]]", html_entity_decode($this->operation->shipping_name), $raw);
                                    break;
                                case 'pay_id':
                                    $raw = str_replace("[[$column]]", $this->operation->payment->key, $raw);
                                    break;
                                case 'pay_name':
                                    $raw = str_replace("[[$column]]", html_entity_decode($this->operation->pay_name), $raw);
                                    break;
                                case 'site_name':
                                    $raw = str_replace("[[$column]]", $this->operation->site->name, $raw);
                                    break;
                                case 'total_price':
                                    $raw = str_replace("[[$column]]", number_format($this->operation->total_price, 2, ',', ' '), $raw);
                                    break;
                                case 'shipping_fee_incl_vat':
                                    $raw = str_replace(
                                        "[[$column]]",
                                        number_format($this->operation->shipping_fee * (1 + $this->operation->vat_percentage * 0.01), 2, ',', ' '),
                                        $raw
                                    );
                                    break;*/
                                default:
                                    $raw = str_replace("[[$column]]", $this->operation->{$column}, $raw);
                            }
                            break;
                        case TemplateService::MODEL_DELIVERY_NOTE_EXTRA:
                            switch ($column) {
                                case 'box':
                                    $raw = str_replace("[[$column]]", $this->getBox(), $raw);
                                    break;
                            }
                            break;
                        case TemplateService::MODEL_SITE:
                            switch ($column) {
                                case 'company_name':
                                    $raw = str_replace("[[$column]]", $site->name, $raw);
                                    break;
                                case 'company_url':
                                    $raw = str_replace("[[$column]]", $site->site, $raw);
                                    break;
                                default:
                                    $raw = str_replace("[[$column]]", $site->{$column}, $raw);
                            }
                            break;
                        case TemplateService::MODEL_GENERAL:
                            $raw = str_replace("[[$column]]", $this->mappingGeneral($site, $rawData->settings ?? null)[$column] ?? '', $raw);
                            break;
                        case TemplateService::MODEL_DOCUMENT:
                            $raw = str_replace("[[$column]]", $this->mappingDocument()[$column] ?? '', $raw);
                            break;
                    }
                }
            }

            $data->{$key} = $raw;
        }

        $data = $this->replaceCssClass($data);

        return json_encode($data);
    }

    /**
     * @return string
     */
    protected function getBarcode(): string
    {
        if ($this->isBarcode) {
            return '<barcode code="' . $this->operation->id . '" type="C128A" size="1" height="0.6"/>';
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    protected function getBox(): string
    {
        if ($this->box !== false) {
            return '<table cellspacing="0" cellpadding="0" class="box">
                            <tr>
                                <td class="box">' . $this->box . '</td>
                            </tr>
                        </table>';
        } else {
            return '';
        }
    }
}
