<?php

namespace App\Libraries\Excel;

use Illuminate\Support\Str;

class TemporaryFileFactory extends \Maatwebsite\Excel\Files\TemporaryFileFactory
{
    /**
     * @var string|null
     */
    private $temporaryPath;

    /**
     * @param  string|null  $temporaryPath
     * @param  string|null  $temporaryDisk
     */
    public function __construct(string $temporaryPath = null, string $temporaryDisk = null)
    {
        parent::__construct($temporaryPath, $temporaryDisk);

        $this->temporaryPath = $temporaryPath;
    }

    /**
     * @param  string|null  $fileName
     * @param  string|null  $fileExtension
     * @return LocalTemporaryFile
     */
    public function makeLocal(string $fileName = null, string $fileExtension = null): LocalTemporaryFile
    {
        if (!file_exists($this->temporaryPath) && !mkdir($concurrentDirectory = $this->temporaryPath) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        return new LocalTemporaryFile(
            $this->temporaryPath . DIRECTORY_SEPARATOR . ($fileName ?: $this->generateFilename($fileExtension))
        );
    }

    /**
     * @param  string|null  $fileExtension
     * @return string
     */
    private function generateFilename(string $fileExtension = null): string
    {
        return 'laravel-excel-' . Str::random(32) . ($fileExtension ? '.' . $fileExtension : '');
    }
}
