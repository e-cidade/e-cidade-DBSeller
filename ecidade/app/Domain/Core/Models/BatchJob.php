<?php

namespace App\Domain\Core\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property QueuedJob $queuedJobs
 * @property integer $id
 * @property string $classname
 * @property boolean $cancelled
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class BatchJob extends Model
{
    protected $fillable = [
        'classname'
    ];

    public function queuedJobs()
    {
        return $this->hasMany(QueuedJob::class, 'batch_id', 'id');
    }
}
