<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Controller;

class LogsController extends Controller
{
    public function index()
    {
        $logPath = storage_path('logs/laravel.log');
        $logContent = File::exists($logPath) ? File::get($logPath) : '';
        $logSize = File::exists($logPath) ? File::size($logPath) : 0;
        return view('admin.pages.logs.index', compact('logContent', 'logSize'));
    }

    public function clear()
    {
        $logPath = storage_path('logs/laravel.log');
        File::put($logPath, '');
        return redirect()->route('admin.logs.index')->with('success', 'Log limpo com sucesso!');
    }

    public function download()
    {
        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) {
            return redirect()->route('admin.logs.index')->with('error', 'Arquivo de log não encontrado.');
        }
        return response()->download($logPath, 'laravel.log');
    }
} 