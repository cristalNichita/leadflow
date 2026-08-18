import { Head, Link, router } from '@inertiajs/react';
import { Eye, Handshake, Pencil, Plus, Search, Trash2 } from 'lucide-react';
import type { SyntheticEvent } from 'react';
import { useState } from 'react';
import { DealDeleteDialog } from '@/components/deals/deal-delete-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { create, edit, index, show } from '@/routes/deals';
import type {
    Deal,
    DealStatus,
    PaginatedResource,
    SelectOption,
} from '@/types';

type Props = {
    deals: PaginatedResource<Deal>;

    filters: {
        search: string;
        status: string;
        assigned_user_id: number | null;
    };

    users: SelectOption[];

    can: {
        create: boolean;
        manage: boolean;
    };
};

export default function DealsIndex({ deals, filters, users, can }: Props) {
    const [search, setSearch] = useState(filters.search);

    const [status, setStatus] = useState(filters.status);

    const [assignedUserId, setAssignedUserId] = useState(
        filters.assigned_user_id?.toString() ?? '',
    );

    const applyFilters = (event: SyntheticEvent<HTMLFormElement>) => {
        event.preventDefault();

        router.get(
            index.url(),
            {
                search: search || undefined,
                status: status || undefined,
                assigned_user_id: assignedUserId || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    const resetFilters = () => {
        setSearch('');
        setStatus('');
        setAssignedUserId('');

        router.get(
            index.url(),
            {},
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const hasFilters =
        filters.search !== '' ||
        filters.status !== '' ||
        filters.assigned_user_id !== null;

    return (
        <>
            <Head title="Deals" />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <header className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <p className="text-sm text-muted-foreground">CRM</p>

                        <h1 className="text-2xl font-semibold tracking-tight">
                            Deals
                        </h1>

                        <p className="mt-1 text-sm text-muted-foreground">
                            Manage active opportunities, wins and lost deals.
                        </p>
                    </div>

                    {can.create && (
                        <Button asChild>
                            <Link href={create()}>
                                <Plus />
                                Add deal
                            </Link>
                        </Button>
                    )}
                </header>

                <form
                    onSubmit={applyFilters}
                    className="flex flex-col gap-3 rounded-xl border bg-card p-4 shadow-xs lg:flex-row"
                >
                    <div className="relative flex-1">
                        <Search className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                        <Input
                            value={search}
                            onChange={(event) => setSearch(event.target.value)}
                            placeholder="Search deals..."
                            className="pl-9"
                        />
                    </div>

                    <select
                        value={status}
                        onChange={(event) => setStatus(event.target.value)}
                        className="h-9 rounded-md border border-input bg-background px-3 text-sm lg:w-44"
                    >
                        <option value="">All statuses</option>

                        <option value="open">Open</option>

                        <option value="won">Won</option>

                        <option value="lost">Lost</option>
                    </select>

                    {can.manage && (
                        <select
                            value={assignedUserId}
                            onChange={(event) =>
                                setAssignedUserId(event.target.value)
                            }
                            className="h-9 rounded-md border border-input bg-background px-3 text-sm lg:w-48"
                        >
                            <option value="">All assignees</option>

                            {users.map((user) => (
                                <option key={user.id} value={user.id}>
                                    {user.name}
                                </option>
                            ))}
                        </select>
                    )}

                    <Button type="submit">Filter</Button>

                    {hasFilters && (
                        <Button
                            type="button"
                            variant="outline"
                            onClick={resetFilters}
                        >
                            Reset
                        </Button>
                    )}
                </form>

                <div className="overflow-hidden rounded-xl border bg-card shadow-xs">
                    {deals.data.length === 0 ? (
                        <div className="flex min-h-80 flex-col items-center justify-center px-6 text-center">
                            <div className="mb-4 flex size-12 items-center justify-center rounded-xl bg-muted">
                                <Handshake className="size-5 text-muted-foreground" />
                            </div>

                            <h2 className="font-semibold">No deals found</h2>

                            <p className="mt-1 max-w-sm text-sm text-muted-foreground">
                                {hasFilters
                                    ? 'No deals match the current filters.'
                                    : 'Create your first deal to start tracking revenue opportunities.'}
                            </p>

                            {!hasFilters && can.create && (
                                <Button className="mt-4" asChild>
                                    <Link href={create()}>
                                        <Plus />
                                        Add deal
                                    </Link>
                                </Button>
                            )}
                        </div>
                    ) : (
                        <>
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead className="border-b bg-muted/40">
                                        <tr>
                                            <th className="px-5 py-3 text-left font-medium">
                                                Deal
                                            </th>

                                            <th className="px-5 py-3 text-left font-medium">
                                                Customer
                                            </th>

                                            <th className="px-5 py-3 text-left font-medium">
                                                Value
                                            </th>

                                            <th className="px-5 py-3 text-left font-medium">
                                                Status
                                            </th>

                                            <th className="px-5 py-3 text-left font-medium">
                                                Expected close
                                            </th>

                                            <th className="px-5 py-3 text-left font-medium">
                                                Assigned
                                            </th>

                                            <th className="px-5 py-3 text-right font-medium">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody className="divide-y">
                                        {deals.data.map((deal) => (
                                            <tr
                                                key={deal.id}
                                                className="transition-colors hover:bg-muted/30"
                                            >
                                                <td className="px-5 py-4">
                                                    <Link
                                                        href={show(deal.id)}
                                                        className="font-medium hover:underline"
                                                    >
                                                        {deal.title}
                                                    </Link>
                                                </td>

                                                <td className="px-5 py-4">
                                                    {deal.customer.name}
                                                </td>

                                                <td className="px-5 py-4 font-medium">
                                                    {formatMoney(deal.value)}
                                                </td>

                                                <td className="px-5 py-4">
                                                    <DealBadge
                                                        status={deal.status}
                                                    />
                                                </td>

                                                <td className="px-5 py-4">
                                                    {deal.expected_close_date
                                                        ? formatDate(
                                                              deal.expected_close_date,
                                                          )
                                                        : '—'}
                                                </td>

                                                <td className="px-5 py-4">
                                                    {deal.assigned_user?.name ??
                                                        'Unassigned'}
                                                </td>

                                                <td className="px-5 py-4">
                                                    <div className="flex justify-end gap-1">
                                                        <Button
                                                            variant="ghost"
                                                            size="icon"
                                                            asChild
                                                        >
                                                            <Link
                                                                href={show(
                                                                    deal.id,
                                                                )}
                                                            >
                                                                <Eye />
                                                            </Link>
                                                        </Button>

                                                        <Button
                                                            variant="ghost"
                                                            size="icon"
                                                            asChild
                                                        >
                                                            <Link
                                                                href={edit(
                                                                    deal.id,
                                                                )}
                                                            >
                                                                <Pencil />
                                                            </Link>
                                                        </Button>

                                                        {can.manage && (
                                                            <DealDeleteDialog
                                                                deal={deal}
                                                                trigger={
                                                                    <Button
                                                                        variant="ghost"
                                                                        size="icon"
                                                                    >
                                                                        <Trash2 className="text-destructive" />
                                                                    </Button>
                                                                }
                                                            />
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            <div className="flex flex-col justify-between gap-3 border-t px-5 py-4 sm:flex-row sm:items-center">
                                <p className="text-sm text-muted-foreground">
                                    Showing {deals.meta.from ?? 0}–
                                    {deals.meta.to ?? 0} of {deals.meta.total}{' '}
                                    deals
                                </p>

                                <div className="flex items-center gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        disabled={!deals.links.prev}
                                        onClick={() => {
                                            if (deals.links.prev) {
                                                router.visit(deals.links.prev, {
                                                    preserveScroll: true,
                                                    preserveState: true,
                                                });
                                            }
                                        }}
                                    >
                                        Previous
                                    </Button>

                                    <span className="px-2 text-sm text-muted-foreground">
                                        Page {deals.meta.current_page} of{' '}
                                        {deals.meta.last_page}
                                    </span>

                                    <Button
                                        variant="outline"
                                        size="sm"
                                        disabled={!deals.links.next}
                                        onClick={() => {
                                            if (deals.links.next) {
                                                router.visit(deals.links.next, {
                                                    preserveScroll: true,
                                                    preserveState: true,
                                                });
                                            }
                                        }}
                                    >
                                        Next
                                    </Button>
                                </div>
                            </div>
                        </>
                    )}
                </div>
            </div>
        </>
    );
}

function DealBadge({ status }: { status: DealStatus }) {
    const labels: Record<DealStatus, string> = {
        open: 'Open',
        won: 'Won',
        lost: 'Lost',
    };

    return <Badge variant="secondary">{labels[status]}</Badge>;
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

DealsIndex.layout = {
    breadcrumbs: [
        {
            title: 'Deals',
            href: index(),
        },
    ],
};
