<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Artisan;

class LogController extends Controller
{
    public function index()
    {
        $logPath = storage_path('logs/laravel.log');
        $logContent = File::exists($logPath) ? File::get($logPath) : '';
        $logSize = File::exists($logPath) ? File::size($logPath) : 0;
        
        return response()->json([
            'content' => $logContent,
            'size' => $logSize,
            'size_formatted' => $this->formatBytes($logSize)
        ]);
    }

    public function clear()
    {
        $logPath = storage_path('logs/laravel.log');
        File::put($logPath, '');
        return response()->json(['message' => 'Log limpo com sucesso!']);
    }

    public function download()
    {
        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) {
            return response()->json(['error' => 'Arquivo de log não encontrado.'], 404);
        }
        return response()->download($logPath, 'laravel.log');
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
} 