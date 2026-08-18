import { Head, Link } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';

import {
    CalendarDays,
    CircleDollarSign,
    Pencil,
    Trash2,
    UserRound,
} from 'lucide-react';
import { LeadStatusBadge } from '@/components/crm/status-badges';
import { LeadDeleteDialog } from '@/components/leads/lead-delete-dialog';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { formatCurrency, formatDate } from '@/lib/formatters';
import { edit, index } from '@/routes/leads';
import type { Lead, LeadStatus } from '@/types';

type Props = {
    lead: Lead;
    can: {
        update: boolean;
        delete: boolean;
    };
};

export default function ShowLead({ lead, can }: Props) {
    return (
        <>
            <Head title={lead.title} />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <header className="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    <div>
                        <div className="flex flex-wrap items-center gap-3">
                            <h1 className="text-2xl font-semibold tracking-tight">
                                {lead.title}
                            </h1>

                            <LeadStatusBadge status={lead.status} />
                        </div>

                        <p className="mt-1 text-sm text-muted-foreground">
                            {lead.customer.name}
                            {lead.customer.company
                                ? ` · ${lead.customer.company}`
                                : ''}
                        </p>
                    </div>

                    <div className="flex gap-2">
                        {can.update && (
                            <Button variant="outline" asChild>
                                <Link href={edit(lead.id)}>
                                    <Pencil />
                                    Edit
                                </Link>
                            </Button>
                        )}

                        {can.delete && (
                            <LeadDeleteDialog
                                lead={lead}
                                trigger={
                                    <Button variant="destructive">
                                        <Trash2 />
                                        Delete
                                    </Button>
                                }
                            />
                        )}
                    </div>
                </header>

                <div className="grid gap-4 md:grid-cols-3">
                    <InfoCard
                        title="Estimated value"
                        value={formatCurrency(lead.estimated_value)}
                        icon={CircleDollarSign}
                    />

                    <InfoCard
                        title="Assigned to"
                        value={lead.assigned_user?.name ?? 'Unassigned'}
                        icon={UserRound}
                    />

                    <InfoCard
                        title="Created"
                        value={formatDate(lead.created_at)}
                        icon={CalendarDays}
                    />
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <Card className="xl:col-span-2">
                        <CardHeader>
                            <CardTitle>Opportunity details</CardTitle>

                            <CardDescription>
                                Customer, source and ownership information.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="grid gap-6 sm:grid-cols-2">
                            <Detail
                                label="Customer"
                                value={lead.customer.name}
                            />

                            <Detail
                                label="Company"
                                value={lead.customer.company ?? 'Not provided'}
                            />

                            <Detail
                                label="Source"
                                value={lead.source ?? 'Not provided'}
                            />

                            <Detail
                                label="Status"
                                value={statusLabel(lead.status)}
                            />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Notes</CardTitle>
                        </CardHeader>

                        <CardContent>
                            <p className="text-sm leading-6 whitespace-pre-wrap text-muted-foreground">
                                {lead.notes || 'No notes have been added yet.'}
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

function InfoCard({
    title,
    value,
    icon: Icon,
}: {
    title: string;
    value: string;
    icon: LucideIcon;
}) {
    return (
        <Card>
            <CardContent className="flex items-center gap-4 p-6">
                <div className="flex size-11 items-center justify-center rounded-xl bg-muted">
                    <Icon className="size-5 text-muted-foreground" />
                </div>

                <div>
                    <p className="text-sm text-muted-foreground">{title}</p>

                    <p className="mt-1 font-semibold">{value}</p>
                </div>
            </CardContent>
        </Card>
    );
}

function Detail({ label, value }: { label: string; value: string }) {
    return (
        <div>
            <p className="text-xs text-muted-foreground">{label}</p>

            <p className="mt-1 text-sm font-medium">{value}</p>
        </div>
    );
}

function statusLabel(status: LeadStatus): string {
    const labels: Record<LeadStatus, string> = {
        new: 'New',
        contacted: 'Contacted',
        qualified: 'Qualified',
        won: 'Won',
        lost: 'Lost',
    };

    return labels[status];
}

ShowLead.layout = {
    breadcrumbs: [
        {
            title: 'Leads',
            href: index(),
        },
    ],
};
