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

    private $code;
    private $executionId;

    public function __construct(string $code, string $executionId)
    {
        $this->code = $code;
        $this->executionId = $executionId;
    }

    public function handle()
    {
        $filename = "{$this->executionId}.py";
        Storage::disk('local')->put($filename, $this->code);
        $filepath = Storage::disk('local')->path($filename);

        $process = new Process(['python', $filepath]);
        $process->run();

        cache()->put(
            "python_result_{$this->executionId}",
            [
                'output' => $process->getOutput() ?: $process->getErrorOutput()
            ],
            now()->addMinutes(5)
        );

        Storage::disk('local')->delete($filename);
    }
}
