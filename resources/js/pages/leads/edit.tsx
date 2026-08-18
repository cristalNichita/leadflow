import { Head } from '@inertiajs/react';
import { LeadForm } from '@/components/leads/lead-form';
import { index } from '@/routes/leads';
import type { Lead, SelectOption } from '@/types';

type Props = {
    lead: Lead;
    customers: SelectOption[];
    users: SelectOption[];
    canManageLead: boolean;
};

export default function EditLead({
    lead,
    customers,
    users,
    canManageLead,
}: Props) {
    return (
        <>
            <Head title={`Edit ${lead.title}`} />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div>
                    <p className="text-sm text-muted-foreground">CRM / Leads</p>

                    <h1 className="mt-1 text-2xl font-semibold tracking-tight">
                        Edit lead
                    </h1>

                    <p className="mt-1 text-sm text-muted-foreground">
                        Update{' '}
                        <span className="font-medium text-foreground">
                            {lead.title}
                        </span>
                        .
                    </p>
                </div>

                <div className="max-w-5xl">
                    <LeadForm
                        lead={lead}
                        customers={customers}
                        users={users}
                        canManageLead={canManageLead}
                    />
                </div>
            </div>
        </>
    );
}

EditLead.layout = {
    breadcrumbs: [
        {
            title: 'Leads',
            href: index(),
        },
    ],
};
