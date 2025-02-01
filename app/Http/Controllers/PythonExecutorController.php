<?php

namespace App\Http\Controllers;

use App\Jobs\ExecutePythonCode;
use Illuminate\Http\Request;

class PythonExecutorController extends Controller
{
    public function index()
    {
        return view('python.index'); // affiche la vue dans /views/python/index
    }

    public function execute(Request $request)
    {
        $executionId = uniqid('py_'); // génération d'un id pour l'exécution
        ExecutePythonCode::dispatch($request->code, $executionId); // envoie le job dans la file d'attente
        return response()->json(['execution_id' => $executionId]); // reponse json/envoie l'id à la vue
    }

    public function getResult($executionId)
    {
        $result = cache()->get("python_result_{$executionId}"); // recupere le resultat dans le cache avec la cle correspondante
        if ($result) {
            return response()->json($result); // retourne le resultat/envoie le resultat à la vue
        }
        return response()->json(['status' => 'pending']); // si le resultat n'est pas disponible
    }
}
