<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;

trait DeepReplicates
{
    public function custonReplicate()
    {
        $copy = parent::replicate();
        $copy->push();

        foreach ($copy->getRelations() as $relation => $entries) {
            if ($entries instanceof Collection) {
                foreach ($entries as $entry) {
                    $e = $entry->replicate();
                    $copy->{$relation}()->save($e);
                }
            } else {
                $e = $entries->replicate();
                $copy->{$relation}()->save($e);
            }
        }
        return $copy;
    }
}
