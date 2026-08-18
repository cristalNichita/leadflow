<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerIndexRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\User;
use App\Services\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customers,
    ) {}

    public function index(CustomerIndexRequest $request): Response
    {
        $user = $request->user();

        abort_unless($user instanceof User, 403);

        $filters = $request->filters();

        $customers = $this->customers->paginate(
            $user,
            $filters,
        );

        return Inertia::render('customers/index', [
            'customers' => CustomerResource::collection($customers),

            'filters' => $filters->toArray(),

            'can' => [
                'create' => Gate::allows(
                    'create',
                    Customer::class,
                ),
            ],
        ]);
    }

    public function create(): Response
    {
        Gate::authorize(
            'create',
            Customer::class,
        );

        return Inertia::render('customers/create');
    }

    public function store(
        StoreCustomerRequest $request,
    ): RedirectResponse {
        $customer = $this->customers->create(
            $request->data(),
        );

        return to_route(
            'customers.show',
            $customer,
        )->with(
            'success',
            'Customer created successfully.',
        );
    }

    public function show(Customer $customer): Response
    {
        Gate::authorize(
            'view',
            $customer,
        );

        $customer = $this->customers->details($customer);

        return Inertia::render('customers/show', [
            'customer' => CustomerResource::make($customer),

            'can' => [
                'update' => Gate::allows(
                    'update',
                    $customer,
                ),

                'delete' => Gate::allows(
                    'delete',
                    $customer,
                ),
            ],
        ]);
    }

    public function edit(Customer $customer): Response
    {
        Gate::authorize(
            'update',
            $customer,
        );

        return Inertia::render('customers/edit', [
            'customer' => CustomerResource::make($customer),
        ]);
    }

    public function update(
        UpdateCustomerRequest $request,
        Customer $customer,
    ): RedirectResponse {
        $customer = $this->customers->update(
            $customer,
            $request->data(),
        );

        return to_route(
            'customers.show',
            $customer,
        )->with(
            'success',
            'Customer updated successfully.',
        );
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        Gate::authorize(
            'delete',
            $customer,
        );

        $this->customers->delete($customer);

        return to_route(
            'customers.index',
        )->with(
            'success',
            'Customer deleted successfully.',
        );
    }
}
