<?php

namespace App\Services\Core;

use App;

use App\Models\Template;
use App\Models\Operations;
use App\Services\Template\TemplateService;
use App\Repositories\Template\TemplateRepositoryInterface;

class OperationsService
{
	/**
     * @var PdfService
    */
    protected $pdfService;

    /**
     * @var TemplateService
    */
    protected $templateService;

    /**
     * @var TemplateRepo
    */
    protected $templateRepo;

    /**
     * OperationsService constructor
     * @param PdfService $pdfService
     * @param TemplateService $templateService
     * @param TemplateRepositoryInterface $templateRepo
     */
	public function __construct(
        PdfService $pdfService,
        TemplateService $templateService,
        TemplateRepositoryInterface $templateRepo
    )
    {
        $this->pdfService = $pdfService;
        $this->templateService = $templateService;
        $this->templateRepo = $templateRepo;
    }
	 /**
     * @param Operation $operation
     * @param string $filePath
     * @param string $rawTemplatePreview
     * @return void
     */
    public function generateOperations(Operations $operation, string $filePath, string $rawTemplatePreview = '')
    {
        $locale = app()->getLocale();


        $templateModel = $this->templateRepo->getOne(Template::TYPE_QUITTANCE_LOYER);

        $template = $this->templateService->formatRaw(
            Template::TYPE_QUITTANCE_LOYER,
            !empty($rawTemplatePreview) ? $rawTemplatePreview : json_encode($templateModel->raw),
            $operation,
            true //$this->checkIsBarcode($operation)
        );

        $pdf = $this->pdfService->quittanceLoyer(
            config('common.pdf.quittance_loyer.template'),
            [
                'template' => $template,
                'order' => $operation
            ],
            $filePath
        );

        app()->setLocale($locale);

        if (!$pdf) {
            throw new PrintException("Pdf has not been generated (For order #{$operation->id})");
        }
    }

}
