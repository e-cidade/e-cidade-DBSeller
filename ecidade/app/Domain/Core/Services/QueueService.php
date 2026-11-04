<?php

namespace App\Domain\Core\Services;

use App\Domain\Core\Models\BatchJob;
use App\Domain\Core\Models\QueuedJob;
use Illuminate\Database\Eloquent\Model;

class QueueService
{
    /**
     * @var BatchJob
     */
    private $batch;

    /**
     * @param string $job
     */
    public function __construct($job)
    {
        $this->batch = BatchJob::create(['classname' => $job]);
    }

    /**
     * @return QueuedJob
     */
    public function next()
    {
        $queuedJob = new QueuedJob;
        $queuedJob->batch()->associate($this->batch);
        $queuedJob->save();

        return $queuedJob;
    }

    /**
     * @param QueuedJob $queuedJob
     * @return int
     * @throws \Exception
     */
    public function terminate(QueuedJob $queuedJob)
    {
        $queuedJob->delete();
        return $this->batch->queuedJobs()->count();
    }

    /**
     * @return void
     */
    public function cancel()
    {
        $this->batch->cancelled = true;
    }

    /**
     * @return BatchJob
     */
    public function getBatch()
    {
        return $this->batch;
    }
}
