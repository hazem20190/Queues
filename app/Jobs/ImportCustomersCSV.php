<?php

namespace App\Jobs;

use App\Models\Customers;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class ImportCustomersCSV implements ShouldQueue
{
    use Queueable;
    use Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $from, public int $to)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        // if ($this->batch()->canceled()) {
        //     return;
        // }
        // if ($this->from > 500) {
        //     $this->batch()->cancel();
        // }



        $path = storage_path('app/customers-10000.csv');

        if (($file = fopen($path, 'r')) !== false) {

            fgetcsv($file); // skip header

            $currentrow = 1;

            while (($row = fgetcsv($file)) !== false) {

                if ($currentrow > $this->to) {
                    break;
                }

                if ($currentrow >= $this->from) {
                    Customers::create([
                        'first_name'    => $row[2],
                        'last_name'     => $row[3],
                        'company'       => $row[4],
                        'city'          => $row[5],
                        'phone'         => $row[7],
                        'email'         => $row[9],
                        // 'created_at'    => Carbon::createFromFormat('m/d/Y', trim($row[10]))->format('Y-m-d'),
                    ]);
                }
                $currentrow++;
            }
            fclose($file);
        }
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        return [new SkipIfBatchCancelled()];
    }
}
