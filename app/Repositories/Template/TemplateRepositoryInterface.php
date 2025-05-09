<?php

namespace App\Repositories\Template;

use App\Models\Template;

interface TemplateRepositoryInterface
{
    /**
     * @return Template
     */
    public function getModel();

    /**
     * @return Template[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

    /**
     * @param int $type
     * @return Template[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllByType(int $type);

    /**
     * @param int $id
     * @return Template|null
     */
    public function getOne(int $id): ?Template;

    /**
     * @param array $data
     * @param Template $template
     * @return Template
     * @throws \Exception
     */
    public function update(array $data, Template $template): Template;

    /**
     * @param array $data
     * @return Template
     * @throws \Exception
     */
    public function create(array $data): Template;
}
