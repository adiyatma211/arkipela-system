<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public function log(
        string $moduleName,
        Model $record,
        string $action,
        ?array $oldValue = null,
        ?array $newValue = null,
        ?string $description = null,
    ): void {
        ActivityLog::query()->create([
            'user_id' => auth()->id(),
            'module_name' => $moduleName,
            'record_id' => $record->getKey(),
            'action' => $action,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'description' => $description,
        ]);
    }
}
