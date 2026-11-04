<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class QueuedJob extends Model
{
    public function batch()
    {
        return $this->belongsTo(BatchJob::class);
    }

    public function failedJob()
    {
        return $this->hasOne(FailedJob::class, "payload", "payload");
    }
}
