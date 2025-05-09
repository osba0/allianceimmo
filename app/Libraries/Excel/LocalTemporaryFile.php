<?php

namespace App\Libraries\Excel;

class LocalTemporaryFile extends \Maatwebsite\Excel\Files\LocalTemporaryFile
{
    public function __construct(string $filePath)
    {
        parent::__construct($filePath);

        $filePath = $this->getLocalPath();

        chmod($filePath, 0777);
    }
}
