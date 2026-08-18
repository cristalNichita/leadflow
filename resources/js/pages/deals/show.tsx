import { Head, Link } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import {
    CalendarDays,
    CircleDollarSign,
    Pencil,
    Trash2,
    UserRound,
} from 'lucide-react';
import { DealDeleteDialog } from '@/components/deals/deal-delete-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { edit, index } from '@/routes/deals';
import type { Deal, DealStatus } from '@/types';

type Props = {
    deal: Deal;

    can: {
        update: boolean;
        delete: boolean;
    };
};

export default function ShowDeal({ deal, can }: Props) {
    return (
        <>
            <Head title={deal.title} />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <header className="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    <div>
                        <div className="flex flex-wrap items-center gap-3">
                            <h1 className="text-2xl font-semibold tracking-tight">
                                {deal.title}
                            </h1>

                            <DealStatusBadge status={deal.status} />
                        </div>

                        <p className="mt-1 text-sm text-muted-foreground">
                            {deal.customer.name}

                            {deal.customer.company
                                ? ` · ${deal.customer.company}`
                                : ''}
                        </p>
                    </div>

                    <div className="flex gap-2">
                        {can.update && (
                            <Button variant="outline" asChild>
                                <Link href={edit(deal.id)}>
                                    <Pencil />
                                    Edit
                                </Link>
                            </Button>
                        )}

                        {can.delete && (
                            <DealDeleteDialog
                                deal={deal}
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
                        title="Deal value"
                        value={formatMoney(deal.value)}
                        icon={CircleDollarSign}
                    />

                    <InfoCard
                        title="Assigned to"
                        value={deal.assigned_user?.name ?? 'Unassigned'}
                        icon={UserRound}
                    />

                    <InfoCard
                        title="Expected close"
                        value={
                            deal.expected_close_date
                                ? formatDate(deal.expected_close_date)
                                : 'Not scheduled'
                        }
                        icon={CalendarDays}
                    />
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <Card className="xl:col-span-2">
                        <CardHeader>
                            <CardTitle>Deal details</CardTitle>

                            <CardDescription>
                                Customer and commercial information.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="grid gap-6 sm:grid-cols-2">
                            <Detail
                                label="Customer"
                                value={deal.customer.name}
                            />

                            <Detail
                                label="Company"
                                value={deal.customer.company ?? 'Not provided'}
                            />

                            <Detail
                                label="Value"
                                value={formatMoney(deal.value)}
                            />

                            <Detail
                                label="Status"
                                value={statusLabel(deal.status)}
                            />

                            <Detail
                                label="Created"
                                value={formatDate(deal.created_at)}
                            />

                            <Detail
                                label="Expected close"
                                value={
                                    deal.expected_close_date
                                        ? formatDate(deal.expected_close_date)
                                        : 'Not scheduled'
                                }
                            />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Notes</CardTitle>
                        </CardHeader>

                        <CardContent>
                            <p className="text-sm leading-6 whitespace-pre-wrap text-muted-foreground">
                                {deal.notes || 'No notes have been added yet.'}
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

function DealStatusBadge({ status }: { status: DealStatus }) {
    const classes: Record<DealStatus, string> = {
        open: 'bg-blue-500/15 text-blue-500 hover:bg-blue-500/15',
        won: 'bg-emerald-500/15 text-emerald-500 hover:bg-emerald-500/15',
        lost: 'bg-red-500/15 text-red-500 hover:bg-red-500/15',
    };

    return (
        <Badge variant="secondary" className={classes[status]}>
            {statusLabel(status)}
        </Badge>
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

function statusLabel(status: DealStatus): string {
    const labels: Record<DealStatus, string> = {
        open: 'Open',
        won: 'Won',
        lost: 'Lost',
    };

    return labels[status];
}

function formatMoney(value: string): string {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(Number(value));
}

function formatDate(value: string): string {
    return new Intl.DateTimeFormat('en', {
        dateStyle: 'medium',
    }).format(new Date(value));
}

ShowDeal.layout = {
    breadcrumbs: [
        {
            title: 'Deals',
            href: index(),
        },
    ],
};
