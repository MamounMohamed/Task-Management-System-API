<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTaskUpdateFields
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        $requestFields = [];
        // Only apply restriction to non-manager users
        if ($user && $user->role != 'manager') {
            $restrictedFields = ['title', 'description', 'assignee_id', 'due_date', 'dependencies'];

            foreach ($restrictedFields as $field) {
                if ($request->has($field)) {
                    $requestFields[] = $field;
                }
            }

            if (count($requestFields) > 0) {
                $requestFields = implode(', ', $requestFields);
                abort(403, "You are not authorized to update the field: {$requestFields}");
            }
        }

        return $next($request);
    }
}
