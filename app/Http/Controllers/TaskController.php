<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\TaskIndexRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $tasks,
    ) {}

    public function index(TaskIndexRequest $request): Response
    {
        $user = $request->user();

        abort_unless(
            $user instanceof User,
            403,
        );

        $filters = $request->filters();

        return Inertia::render('tasks/index', [
            'tasks' => TaskResource::collection(
                $this->tasks->paginate(
                    $user,
                    $filters,
                ),
            ),

            'filters' => $filters->toArray(),

            'users' => $this->tasks->assigneeOptions(),

            'can' => [
                'create' => Gate::allows(
                    'create',
                    Task::class,
                ),

                'manage' => $user->isAdmin()
                    || $user->isManager(),
            ],
        ]);
    }

    public function create(): Response
    {
        Gate::authorize(
            'create',
            Task::class,
        );

        return Inertia::render(
            'tasks/create',
            $this->tasks->formOptions(),
        );
    }

    public function store(
        StoreTaskRequest $request,
    ): RedirectResponse {
        $task = $this->tasks->create(
            $request->data(),
        );

        return to_route(
            'tasks.show',
            $task,
        )->with(
            'success',
            'Task created successfully.',
        );
    }

    public function show(Task $task): Response
    {
        Gate::authorize(
            'view',
            $task,
        );

        $task = $this->tasks->details(
            $task,
        );

        return Inertia::render('tasks/show', [
            'task' => TaskResource::make(
                $task,
            )->resolve(),

            'can' => [
                'update' => Gate::allows(
                    'update',
                    $task,
                ),

                'delete' => Gate::allows(
                    'delete',
                    $task,
                ),
            ],
        ]);
    }

    public function edit(Task $task): Response
    {
        Gate::authorize(
            'update',
            $task,
        );

        $user = request()->user();

        abort_unless(
            $user instanceof User,
            403,
        );

        return Inertia::render('tasks/edit', [
            'task' => TaskResource::make(
                $this->tasks->details($task),
            )->resolve(),

            ...$this->tasks->formOptions(),

            'canManageTask' => $user->isAdmin()
                || $user->isManager(),
        ]);
    }

    public function update(
        UpdateTaskRequest $request,
        Task $task,
    ): RedirectResponse {
        $user = $request->user();

        abort_unless(
            $user instanceof User,
            403,
        );

        $task = $this->tasks->update(
            $user,
            $task,
            $request->data(),
        );

        return to_route(
            'tasks.show',
            $task,
        )->with(
            'success',
            'Task updated successfully.',
        );
    }

    public function destroy(
        Task $task,
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $task,
        );

        $this->tasks->delete($task);

        return to_route(
            'tasks.index',
        )->with(
            'success',
            'Task deleted successfully.',
        );
    }
}
