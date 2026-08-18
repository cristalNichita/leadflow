<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadIndexRequest;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use App\Models\User;
use App\Services\LeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class LeadController extends Controller
{
    public function __construct(
        private readonly LeadService $leads,
    ) {}

    public function index(LeadIndexRequest $request): Response
    {
        $user = $request->user();

        abort_unless(
            $user instanceof User,
            403,
        );

        $filters = $request->filters();

        $leads = $this->leads->paginate(
            $user,
            $filters,
        );

        return Inertia::render('leads/index', [
            'leads' => LeadResource::collection($leads),

            'filters' => $filters->toArray(),

            'users' => $this->leads->assigneeOptions(),

            'can' => [
                'create' => Gate::allows(
                    'create',
                    Lead::class,
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
            Lead::class,
        );

        return Inertia::render(
            'leads/create',
            $this->leads->formOptions(),
        );
    }

    public function store(
        StoreLeadRequest $request,
    ): RedirectResponse {
        $lead = $this->leads->create(
            $request->data(),
        );

        return to_route(
            'leads.show',
            $lead,
        )->with(
            'success',
            'Lead created successfully.',
        );
    }

    public function show(Lead $lead): Response
    {
        Gate::authorize(
            'view',
            $lead,
        );

        $lead = $this->leads->details(
            $lead,
        );

        return Inertia::render('leads/show', [
            'lead' => LeadResource::make($lead)->resolve(),

            'can' => [
                'update' => Gate::allows(
                    'update',
                    $lead,
                ),

                'delete' => Gate::allows(
                    'delete',
                    $lead,
                ),
            ],
        ]);
    }

    public function edit(Lead $lead): Response
    {
        Gate::authorize(
            'update',
            $lead,
        );

        $user = request()->user();

        abort_unless(
            $user instanceof User,
            403,
        );

        return Inertia::render('leads/edit', [
            'lead' => LeadResource::make(
                $this->leads->details($lead),
            )->resolve(),

            ...$this->leads->formOptions(),

            'canManageLead' => $user->isAdmin()
                || $user->isManager(),
        ]);
    }

    public function update(
        UpdateLeadRequest $request,
        Lead $lead,
    ): RedirectResponse {
        $user = $request->user();

        abort_unless(
            $user instanceof User,
            403,
        );

        $lead = $this->leads->update(
            $user,
            $lead,
            $request->data(),
        );

        return to_route(
            'leads.show',
            $lead,
        )->with(
            'success',
            'Lead updated successfully.',
        );
    }

    public function destroy(
        Lead $lead,
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $lead,
        );

        $this->leads->delete($lead);

        return to_route(
            'leads.index',
        )->with(
            'success',
            'Lead deleted successfully.',
        );
    }
}
