import { Head } from '@inertiajs/react';
import { CustomerForm } from '@/components/customers/customer-form';
import { index } from '@/routes/customers';
import type { Customer } from '@/types';

type Props = {
    customer: Customer;
};

export default function EditCustomer({ customer }: Props) {
    return (
        <>
            <Head title={`Edit ${customer.name}`} />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div>
                    <p className="text-sm text-muted-foreground">
                        CRM / Customers
                    </p>

                    <h1 className="mt-1 text-2xl font-semibold tracking-tight">
                        Edit customer
                    </h1>

                    <p className="mt-1 text-sm text-muted-foreground">
                        Update information for{' '}
                        <span className="font-medium text-foreground">
                            {customer.name}
                        </span>
                        .
                    </p>
                </div>

                <div className="max-w-5xl">
                    <CustomerForm customer={customer} />
                </div>
            </div>
        </>
    );
}

EditCustomer.layout = {
    breadcrumbs: [
        {
            title: 'Customers',
            href: index(),
        },
    ],
};
