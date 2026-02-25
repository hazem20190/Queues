<?php

use App\Http\Controllers\ProfileController;
use App\Jobs\InfoJob1;
use App\Jobs\InfoJob2;
use App\Jobs\InfoJob3;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    // LogInfoJob::dispatch(); //  IF YOU RUN QUEUE:WORK   THE WORKER RUN DEFAULT QUEUE ONLY
    // LogInfoJob::dispatch()->onQueue('info-queue');
    // LogInfoJob::dispatch()->onConnection('sync');

    Bus::batch([
        new InfoJob1(),
        new InfoJob2(),
        new InfoJob3(),
        // new ImportCustomersCSV(1, 500),
        // new ImportCustomersCSV(501, 1000),
        // new ImportCustomersCSV(1001, 1500),
        // new ImportCustomersCSV(1501, 2000),
    ])
    ->name('infoJob')
    ->before(function (Batch $batch) {
        // The batch has been created but no jobs have been added...
        Log::info('before job');
    })->progress(function (Batch $batch) {
        // A single job has completed successfully...
        Log::info('single job completed');
    })->then(function (Batch $batch) {
        // All jobs completed successfully...
        Log::info('All jobs completed');
    })->catch(function (Batch $batch, Throwable $e) {
        // Batch job failure detected...
        $e->getMessage();
    })->finally(function (Batch $batch) {
        // The batch has finished executing...
        Log::info('All jobs finished');
    })
    ->allowFailures()
    ->dispatch();

    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
