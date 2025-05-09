<?php

namespace App\Services\Core\Repositories\Operation;

use App\Models\Operations;

use Carbon\Carbon;
use DB;


class OperationRepository implements OperationRepositoryInterface
{
    /**
     * @var Operation
     */
    protected $model;

    /**
     * OperationRepository constructor.
     * @param Operation $operation
     */
    public function __construct(Operations $operation)
    {
        $this->model = $operation;


    }

    public function findOneById($id)
    {
        return $this->model->find($id);
    }

}
