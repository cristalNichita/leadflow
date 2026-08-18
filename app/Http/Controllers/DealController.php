<?php

namespace App\Http\Controllers;

use App\Http\Requests\DealIndexRequest;
use App\Http\Requests\StoreDealRequest;
use App\Http\Requests\UpdateDealRequest;
use App\Http\Resources\DealResource;
use App\Models\Deal;
use App\Models\User;
use App\Services\DealService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DealController extends Controller
{
    public function __construct(
        private readonly DealService $deals,
    ) {}

    public function index(DealIndexRequest $request): Response
    {
        $user = $request->user();

        abort_unless(
            $user instanceof User,
            403,
        );

        $filters = $request->filters();

        return Inertia::render('deals/index', [
            'deals' => DealResource::collection(
                $this->deals->paginate(
                    $user,
                    $filters,
                ),
            ),

            'filters' => $filters->toArray(),

            'users' => $this->deals->assigneeOptions(),

            'can' => [
                'create' => Gate::allows(
                    'create',
                    Deal::class,
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
            Deal::class,
        );

        return Inertia::render(
            'deals/create',
            $this->deals->formOptions(),
        );
    }

    public function store(
        StoreDealRequest $request,
    ): RedirectResponse {
        $deal = $this->deals->create(
            $request->data(),
        );

        return to_route(
            'deals.show',
            $deal,
        )->with(
            'success',
            'Deal created successfully.',
        );
    }

    public function show(Deal $deal): Response
    {
        Gate::authorize(
            'view',
            $deal,
        );

        $deal = $this->deals->details(
            $deal,
        );

        return Inertia::render('deals/show', [
            'deal' => DealResource::make($deal)->resolve(),

            'can' => [
                'update' => Gate::allows(
                    'update',
                    $deal,
                ),

                'delete' => Gate::allows(
                    'delete',
                    $deal,
                ),
            ],
        ]);
    }

    public function edit(Deal $deal): Response
    {
        Gate::authorize(
            'update',
            $deal,
        );

        $user = request()->user();

        abort_unless(
            $user instanceof User,
            403,
        );

        return Inertia::render('deals/edit', [
            'deal' => DealResource::make(
                $this->deals->details($deal),
            )->resolve(),

            ...$this->deals->formOptions(),

            'canManageDeal' => $user->isAdmin()
                || $user->isManager(),
        ]);
    }

    public function update(
        UpdateDealRequest $request,
        Deal $deal,
    ): RedirectResponse {
        $user = $request->user();

        abort_unless(
            $user instanceof User,
            403,
        );

        $deal = $this->deals->update(
            $user,
            $deal,
            $request->data(),
        );

        return to_route(
            'deals.show',
            $deal,
        )->with(
            'success',
            'Deal updated successfully.',
        );
    }

    public function destroy(
        Deal $deal,
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $deal,
        );

        $this->deals->delete($deal);

        return to_route(
            'deals.index',
        )->with(
            'success',
            'Deal deleted successfully.',
        );
    }
}
