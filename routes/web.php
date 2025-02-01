<?php

use App\Http\Controllers\PythonExecutorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Process;

Route::get('/', [PythonExecutorController::class, 'index']);
Route::post('/execute-python', [PythonExecutorController::class, 'execute']);
Route::get('/python-result/{executionId}', [PythonExecutorController::class, 'getResult']);
Route::get('/test', function (){
    $process = Process::path('D:\MSQ\E&D\Syn Cole')->run('ls');
    echo $process->exitCode();
});
