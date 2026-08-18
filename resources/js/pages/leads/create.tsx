import { Head } from '@inertiajs/react';
import { LeadForm } from '@/components/leads/lead-form';
import { create, index } from '@/routes/leads';
import type { SelectOption } from '@/types';

type Props = {
    customers: SelectOption[];
    users: SelectOption[];
};

export default function CreateLead({ customers, users }: Props) {
    return (
        <>
            <Head title="Create lead" />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div>
                    <p className="text-sm text-muted-foreground">CRM / Leads</p>

                    <h1 className="mt-1 text-2xl font-semibold tracking-tight">
                        Create lead
                    </h1>

                    <p className="mt-1 text-sm text-muted-foreground">
                        Add a new sales opportunity to the pipeline.
                    </p>
                </div>

                <div className="max-w-5xl">
                    <LeadForm customers={customers} users={users} />
                </div>
            </div>
        </>
    );
}

CreateLead.layout = {
    breadcrumbs: [
        {
            title: 'Leads',
            href: index(),
        },
        {
            title: 'Create',
            href: create(),
        },
    ],
};
