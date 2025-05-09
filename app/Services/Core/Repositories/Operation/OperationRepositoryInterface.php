<?php

namespace App\Services\Core\Repositories\Operation;

use App\Models\DataSource;
use App\Models\Operations;
use App\Services\Core\Dto\OperationDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface OperationRepositoryInterface
{
     /**
     * @param $id
     * @return Operation|null
     */
    public function findOneById($id);

}


