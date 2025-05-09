<?php

namespace App\Services\Template\ParseClass;

interface TemplateParseInterface
{
    /**
     * @param int $type
     * @param string $raw
     * @return string
     */
    public function handle(int $type, string $raw): string;

    /**
     * @param int $type
     * @param string $raw
     * @return array
     * @throws \Exception
     */
    public function validateBody(int $type, string $raw): array;
}
