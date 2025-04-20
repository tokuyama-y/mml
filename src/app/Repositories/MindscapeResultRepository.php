<?php

namespace App\Repositories;

use App\Models\MindscapeResult;

class MindscapeResultRepository
{
    public function create(array $data): MindscapeResult
    {
        return MindscapeResult::create($data);
    }

    public function find(int $id): ?MindscapeResult
    {
        return MindscapeResult::find($id);
    }

    public function all()
    {
        return MindscapeResult::all();
    }

    public function update(int $id, array $data): ?MindscapeResult
    {
        $record = MindscapeResult::find($id);
        if ($record) {
            $record->update($data);
        }
        return $record;
    }

    public function delete(int $id): bool
    {
        return MindscapeResult::destroy($id) > 0;
    }

    public function findLatestWithoutHaiku(): ?MindscapeResult
    {
        return MindscapeResult::whereNull('haiku')
                              ->orderByDesc('updated_at')
                              ->first();
    }
}
