<?php

// app/Http/Controllers/PythonExecutorController.php
namespace App\Http\Controllers;

use App\Jobs\ExecutePythonCode;
use Illuminate\Http\Request;

class PythonExecutorController extends Controller
{
    public function index()
    {
        return view('python.index');
    }

    public function execute(Request $request)
    {
        $executionId = uniqid('py_');
        ExecutePythonCode::dispatch($request->code, $executionId);
        return response()->json(['execution_id' => $executionId]);
    }

    public function getResult($executionId)
    {
        $result = cache()->get("python_result_{$executionId}");
        if ($result) {
            return response()->json($result);
        }
        return response()->json(['status' => 'pending']);
    }
}
