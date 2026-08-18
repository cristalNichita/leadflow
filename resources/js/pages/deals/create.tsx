import { Head } from '@inertiajs/react';
import { DealForm } from '@/components/deals/deal-form';
import { create, index } from '@/routes/deals';
import type { SelectOption } from '@/types';

type Props = {
    customers: SelectOption[];
    users: SelectOption[];
};

export default function CreateDeal({ customers, users }: Props) {
    return (
        <>
            <Head title="Create deal" />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div>
                    <p className="text-sm text-muted-foreground">CRM / Deals</p>

                    <h1 className="mt-1 text-2xl font-semibold tracking-tight">
                        Create deal
                    </h1>

                    <p className="mt-1 text-sm text-muted-foreground">
                        Add a new commercial opportunity to your sales pipeline.
                    </p>
                </div>

                <div className="max-w-5xl">
                    <DealForm customers={customers} users={users} />
                </div>
            </div>
        </>
    );
}

CreateDeal.layout = {
    breadcrumbs: [
        {
            title: 'Deals',
            href: index(),
        },
        {
            title: 'Create',
            href: create(),
        },
    ],
};
