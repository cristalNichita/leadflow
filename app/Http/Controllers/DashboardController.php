<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Http\Resources\TaskResource;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboard,
    ) {}

    public function __invoke(
        Request $request,
    ): Response {
        $user = $request->user();

        abort_unless(
            $user instanceof User,
            403,
        );

        $overview = $this->dashboard->overview(
            $user,
        );

        return Inertia::render('dashboard', [
            'metrics' => $overview['metrics']->toArray(),

            'leadStatus' => $overview['lead_status'],

            'dealStatus' => $overview['deal_status'],

            'recentActivities' => ActivityResource::collection(
                $overview['recent_activities'],
            )->resolve(),

            'upcomingTasks' => TaskResource::collection(
                $overview['upcoming_tasks'],
            )->resolve(),
        ]);
    }
}
