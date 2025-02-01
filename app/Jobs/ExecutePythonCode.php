<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class ExecutePythonCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $code; // Code à exécuter
    private $executionId; // id généré par le contrôleur

    // initialisation
    public function __construct(string $code, string $executionId)
    {
        $this->code = $code;
        $this->executionId = $executionId;
    }

    public function handle()
    {
        $filename = "{$this->executionId}.py"; // création du fichier python (ex: "py_61a3b4c.py")
        Storage::disk('local')->put($filename, $this->code); // sauvegarde du fichier avec le code dans 'Storage/app/'
        $filepath = Storage::disk('local')->path($filename); // chemin complet du fichier

        $process = new Process(['python', $filepath]); // création du processus
        $process->run(); // exécution du processus (python chemin/complet/du/fichier/py_61a3b4c.py.py)


        // stockage du resultat dans le cache
        cache()->put(
            "python_result_{$this->executionId}", // clé unique pour le résultat (ex: "python_result_py_61a3b4c"
            [
                'output' => $process->getOutput() ?: $process->getErrorOutput() // getOutput retourne le resultat apres exécution et getErrorOutput l'erreur si l'exécution échoue
            ],
            now()->addMinutes(5) //conserve le cache pendant 5 minutes
        );

        Storage::disk('local')->delete($filename); // suppréssion du fichier
    }
}
