<?php

namespace App\Repositories\Template;

use App\Models\Template;

class TemplateRepository implements TemplateRepositoryInterface
{
    /** @var Template */
    protected $model;

    /**
     * @param Template $template
     */
    public function __construct(Template $template)
    {
        $this->model = $template;
    }

    /**
     * @return Template
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return Template[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->get();
    }

    /**
     * @param int $type
     * @return Template[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllByType(int $type)
    {
        return $this->model->where('type', $type)->get();
    }

    /**
     * @param int $id
     * @return Template|null
     */
    public function getOne(int $id): ?Template
    {
        return $this->model->find($id);
    }

    /**
     * @param array $data
     * @param Template $template
     * @return Template
     * @throws \Exception
     */
    public function update(array $data, Template $template): Template
    {
        $template->fill($data);

        if (!$template->save()) {
            throw new \Exception("Can't update Template model");
        }

        return $template;
    }

    /**
     * @param array $data
     * @return Template
     * @throws \Exception
     */
    public function create(array $data): Template
    {
        $template = new Template();
        $template->fill($data);

        if (!$template->save()) {
            throw new \Exception("Can't create Template model");
        }

        return $template;
    }
}
