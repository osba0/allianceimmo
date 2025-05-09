<?php

namespace App\Services\Core;

use Mpdf\MpdfException;
use PDF;
use ZendPdf\PdfDocument;
use ZendPdf\Resource\Extractor;

class PdfService
{
    /**
     * @param string $templatePath
     * @param array $data
     * @param null|string $filePath
     * @param array $options
     * @return string|mixed
     * @throws MpdfException
     */
    public function quittanceLoyer(string $templatePath, array $data, $filePath = null, $options = [])
    {
        $options = array_merge($options, ['temp_dir' => storage_path('temp')]);

        $pdf = PDF::loadView($templatePath, [], $data, $options);

        if ($filePath === null) {
            return $pdf->output();
        } else {
            $pdf->save($filePath);

            return file_exists($filePath);
        }
    }


    /**
     * @param string $templatePath
     * @param array $data
     * @param null|string $filePath
     * @param array $options
     * @return string|mixed
     * @throws MpdfException
     */
    public function generationPDF(string $templatePath, array $data, $filePath = null, $options = [])
    {
        $options = array_merge($options, ['temp_dir' => storage_path('temp')]);

        $pdf = PDF::loadView($templatePath, [], $data, $options);

        if ($filePath === null) {
            return $pdf->output();
        } else {
            $pdf->save($filePath);

            return file_exists($filePath);
        }
    }



}
