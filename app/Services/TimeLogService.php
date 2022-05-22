<?php

namespace App\Services;

use App\Models\TimeLog;

class TimeLogService
{
    public static function addTimeLog($request): bool
    {
        $validated = $request->validated();
        $log = TimeLog::create(
            [
                'activity_name' => $validated['activity_name'],
                'user_id' => $validated['user_id'],
                'project_id' => $validated['project_id'],
                'billable' => $validated['billable'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
            ]
        );
        if (!is_object($log)) return false;
        return true;
    }

}