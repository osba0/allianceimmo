<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Http\Resources\TemplateCollection;
use App\Http\Resources\TemplateResource;
use App\Models\Admin\AppSetting;
use App\Models\Template;
use App\Repositories\Template\TemplateRepositoryInterface;
use App\Services\Template\TemplateService;
use Gate;
use Illuminate\Http\Request;
use DB;

class TemplateController extends Controller
{
    protected TemplateRepositoryInterface $templateRepo;

    /**
     * @var TemplateService
     */
    protected TemplateService $templateService;

    /**
     * @param TemplateService $templateService
     * @param OrderRepositoryInterface $orderRepo
     */
    public function __construct(
        TemplateRepositoryInterface $templateRepo,
        TemplateService $templateService
    )
    {
       // parent::__construct();

        $this->templateRepo = $templateRepo;
        $this->templateService = $templateService;
        //$this->orderRepo = $orderRepo;
    }

    public function index(Request $request)
    {

        $type = $request->type;
        switch ($type) {
            case Template::TYPE_QUITTANCE_LOYER:
                $emailTemplates = $this->templateRepo->getAllByType(Template::TYPE_QUITTANCE_LOYER);
                $title = __('template.delivery_note_templates');
                $type = Template::TYPE_QUITTANCE_LOYER;
                break;
            default:

                $emailTemplates = $this->templateRepo->getAllByType(Template::TYPE_QUITTANCE_LOYER);
                $title = __('template.email_templates');
                $type = Template::TYPE_QUITTANCE_LOYER;
        }

        return view('template.index', compact('emailTemplates', 'title', 'type'));
    }

    public function getList(Request $request)
    {
        $paginate = request('paginate');

        $templates = DB::table('templates');


        if(isset($paginate)){
            $templates = $templates->groupBy("templates.id")->orderby("created_at", "desc")->paginate($paginate);
        }else{
            $templates = $templates->get();
        }

        return TemplateResource::collection($templates);
    }

    public function create(Request $request)
    {
        $template = Template::findOrFail($request->id);
        $template->name .= ' (COPY)';

        $type = $template->type;
        $variables = $this->templateService::ALLOWED_MODEL_COLUMNS[$type];

        return view('template.create', compact('variables', 'type', 'template'));
    }

    public function store(Request $request)
    {
        try {
            $this->templateRepo->create(array_merge(['created_by' => auth()->id()], $request->except('id')));

            return response('Success', 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 400);
        }
    }

    public function edit(Request $request, Template $template)
    {
        $type = $request->type;
        $variables = $this->templateService::ALLOWED_MODEL_COLUMNS[$type];

        return view('template.update', compact('variables', 'type', 'template'));
    }

    public function update(Request $request, Template $template)
    {
        try {
            $rawData = $request->raw;

            foreach ($rawData as $item) {
                $raw = $item;
                if (!is_string($raw)) {
                    continue;
                }

                $this->templateService->validateBody($template->type, $raw);
            }

            if (!empty($template->created_by)) {
                $this->templateRepo->update(array_merge(['created_by' => auth()->id()], $request->all()), $template);
            } else {
                $this->templateRepo->update($request->all(), $template);
            }

            return response('Success', 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 400);
        }
    }

    public function delete(Template $template)
    {
        try {
            $template->delete();
            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
