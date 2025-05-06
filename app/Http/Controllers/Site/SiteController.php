<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {    
        $pages = Page::where('status', 1)->get();
        $services = Service::where('status', 1)->orderBy('sort_order')->get();
        $projects = Portfolio::where('status', 1)->orderBy('created_at', 'desc')->get();
        $testimonials = Testimonial::where('status', 1)->get();

        return view('site.pages.home', compact('pages', 'services', 'projects', 'testimonials'));
    }

    public function pageShow($slug)
    {
        $page = Page::where('slug', $slug)->first();

        return view('examples.pages_show', compact('page'));
    }

    public function serviceShow($slug)
    {
        $service = Service::where('slug', $slug)->first();

        return view('examples.services_show', compact('service'));
    }

    public function projectsIndex()
    {
        $projects = Portfolio::where('status', 1)->orderBy('created_at', 'desc')->get();
        return view('site.pages.projects', compact('projects'));
    }

    public function projectsShow($slug)
    {
        $project = Portfolio::where('slug', $slug)->firstOrFail();
        return view('site.pages.project_details', compact('project'));
    }
}
