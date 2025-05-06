<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use App\Models\Testimonial;
use Carbon\Carbon;
use App\Services\TestimonialService;

class TestimonialsController extends Controller
{
    public $name = 'Depoimento'; //  singular
    public $folder = 'admin.pages.testimonials';

    protected $testimonialService;

    public function __construct(TestimonialService $testimonialService)
    {
        $this->testimonialService = $testimonialService;
    }

    public function index()
    {
        return view($this->folder . '.index');
    }

    public function load(Request $request)
    {
        $query = [];
        $filters = $request->only(['name', 'status', 'date_range']);
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        if (!empty($filters['name'])) {
            $query['name'] = $filters['name'];
        }

        if (!empty($filters['status'])) {
            $query['status'] = $filters['status'];
        }

        if (!empty($filters['date_range'])) {
            [$startDate, $endDate] = explode(' até ', $filters['date_range']);
            $query['start_date'] = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $query['end_date'] = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        }

        $results = $this->testimonialService->getAllTestimonials($query, true, $perPage);

        if ($request->ajax()) {
            return view($this->folder . '.index_load', compact('results'));
        }

        return view($this->folder . '.index_load', compact('results'));
    }

    public function create()
    {
        $statuses = Status::default();

        return view($this->folder . '.form', compact('statuses'));
    }

    public function store(Request $request)
    {
        $result = $request->all();

        $rules = array(
            'name' => 'required',
            'content' => 'required',
            'rating' => 'required',
            'sort_order' => 'required',
            'status' => 'required',
        );
        $messages = array(
            'name.required' => 'name é obrigatório',
            'content.required' => 'content é obrigatório',
            'rating.required' => 'rating é obrigatório',
            'sort_order.required' => 'sort_order é obrigatório',
            'status.required' => 'status é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }
        
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarPath = $avatar->store('testimonials', 'public');
            $result['avatar'] = $avatarPath;
        }

        $testimonial = $this->testimonialService->createTestimonial($result);

        return response()->json($this->name . ' adicionado com sucesso', 200);
    }

    public function edit($id)
    {
        $result = $this->testimonialService->getTestimonialById($id);
        $statuses = Status::default();

        return view($this->folder . '.form', compact('result', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        $rules = array(
            'name' => 'required',
            'content' => 'required',
            'rating' => 'required',
            'sort_order' => 'required',
            'status' => 'required',
        );
        $messages = array(
            'name.required' => 'name é obrigatório',
            'content.required' => 'content é obrigatório',
            'rating.required' => 'rating é obrigatório',
            'sort_order.required' => 'sort_order é obrigatório',
            'status.required' => 'status é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarPath = $avatar->store('testimonials', 'public');
            $result['avatar'] = $avatarPath;
        }

        $testimonial = $this->testimonialService->updateTestimonial($id, $result);

        return response()->json($this->name . ' atualizado com sucesso', 200);
    }

    public function delete($id)
    {
        $this->testimonialService->deleteTestimonial($id);

        return response()->json($this->name . ' excluído com sucesso', 200);
    }
}
