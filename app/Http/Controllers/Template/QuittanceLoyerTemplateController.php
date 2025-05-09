<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Services\Core\OperationsService;
use App\Models\Operations;
use App\Services\Core\Repositories\Operation\OperationRepositoryInterface;
use Gate;
use Illuminate\Http\Request;
use Printer;

class QuittanceLoyerTemplateController extends Controller
{
    /**
     * @var OperationRepositoryInterface
     */
   // protected OperationRepositoryInterface $operationRepo;

    /**
     * @var OperationsService
     */
    protected OperationsService $operationsService;

    /**
     * @param OperationRepositoryInterface $operationRepo
     */
    public function __construct(OperationRepositoryInterface $operationRepo, OperationsService $operationsService)
    {
        //parent::__construct();

        $this->operationRepo = $operationRepo;
        $this->operationsService = $operationsService;
    }

    public function preview(Request $request)
    {
        try {
            $operationId = $request->orderId;
            $raw = json_encode($request->raw);

            $operation = $this->operationRepo->findOneById($operationId);
            //$order = Operations::where("id", $orderId);

            if (!$operation) {
                throw new \Exception("Operation {$operationId} was not found!");
            }

            $fileName = $this->operationsService->generateOperations($operation, $raw);
            $url = asset("assets/{$fileName}");

            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            return response($e->getMessage(), 400);
        }
    }
}
