import { Head } from '@inertiajs/react';
import { CustomerForm } from '@/components/customers/customer-form';
import { create, index } from '@/routes/customers';

export default function CreateCustomer() {
    return (
        <>
            <Head title="Create customer" />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div>
                    <p className="text-sm text-muted-foreground">
                        CRM / Customers
                    </p>

                    <h1 className="mt-1 text-2xl font-semibold tracking-tight">
                        Create customer
                    </h1>

                    <p className="mt-1 text-sm text-muted-foreground">
                        Add a new customer to your LeadFlow workspace.
                    </p>
                </div>

                <div className="max-w-5xl">
                    <CustomerForm />
                </div>
            </div>
        </>
    );
}

CreateCustomer.layout = {
    breadcrumbs: [
        {
            title: 'Customers',
            href: index(),
        },
        {
            title: 'Create',
            href: create(),
        },
    ],
};
