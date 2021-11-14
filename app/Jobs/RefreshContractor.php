<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ContractorService;

class RefreshContractor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $contractorId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contractorId)
    {
        $this->contractorId = $contractorId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contractorService = new ContractorService;
        $contractorService->refreshContractor($this->contractorId);
    }
}
