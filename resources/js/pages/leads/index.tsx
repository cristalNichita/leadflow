import { Head, Link, router } from '@inertiajs/react';
import { Eye, Pencil, Plus, Search, Target, Trash2 } from 'lucide-react';
import type { SyntheticEvent } from 'react';
import { useState } from 'react';
import { LeadStatusBadge } from '@/components/crm/status-badges';
import { LeadDeleteDialog } from '@/components/leads/lead-delete-dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { formatCurrency } from '@/lib/formatters';
import { show as customerShow } from '@/routes/customers';
import { create, edit, index, show } from '@/routes/leads';
import type { Lead, PaginatedResource, SelectOption } from '@/types';

type Props = {
    leads: PaginatedResource<Lead>;

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

export default function LeadsIndex({ leads, filters, users, can }: Props) {
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
            <Head title="Leads" />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <header className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <p className="text-sm text-muted-foreground">CRM</p>

                        <h1 className="text-2xl font-semibold tracking-tight">
                            Leads
                        </h1>

                        <p className="mt-1 text-sm text-muted-foreground">
                            Track sales opportunities and pipeline progress.
                        </p>
                    </div>

                    {can.create && (
                        <Button asChild>
                            <Link href={create()}>
                                <Plus />
                                Add lead
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
                            placeholder="Search leads..."
                            className="pl-9"
                        />
                    </div>

                    <select
                        value={status}
                        onChange={(event) => setStatus(event.target.value)}
                        className="h-9 rounded-md border border-input bg-background px-3 text-sm lg:w-44"
                    >
                        <option value="">All statuses</option>
                        <option value="new">New</option>
                        <option value="contacted">Contacted</option>
                        <option value="qualified">Qualified</option>
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
                    {leads.data.length === 0 ? (
                        <div className="flex min-h-80 flex-col items-center justify-center px-6 text-center">
                            <div className="mb-4 flex size-12 items-center justify-center rounded-xl bg-muted">
                                <Target className="size-5 text-muted-foreground" />
                            </div>

                            <h2 className="font-semibold">No leads found</h2>

                            <p className="mt-1 text-sm text-muted-foreground">
                                {hasFilters
                                    ? 'No leads match the current filters.'
                                    : 'Create your first opportunity to start building the pipeline.'}
                            </p>
                        </div>
                    ) : (
                        <>
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead className="border-b bg-muted/40">
                                        <tr>
                                            <th className="px-5 py-3 text-left font-medium">
                                                Lead
                                            </th>
                                            <th className="px-5 py-3 text-left font-medium">
                                                Customer
                                            </th>
                                            <th className="px-5 py-3 text-right font-medium">
                                                Value
                                            </th>
                                            <th className="px-5 py-3 text-left font-medium">
                                                Status
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
                                        {leads.data.map((lead) => (
                                            <tr
                                                key={lead.id}
                                                className="transition-colors hover:bg-muted/30"
                                            >
                                                <td className="px-5 py-4">
                                                    <Link
                                                        href={show(lead.id)}
                                                        className="font-medium hover:underline"
                                                    >
                                                        {lead.title}
                                                    </Link>

                                                    <p className="mt-0.5 text-xs text-muted-foreground">
                                                        {lead.source ??
                                                            'No source'}
                                                    </p>
                                                </td>

                                                <td className="px-5 py-4">
                                                    <Link
                                                        href={customerShow(
                                                            lead.customer.id,
                                                        )}
                                                        className="font-medium hover:underline"
                                                    >
                                                        {lead.customer.name}
                                                    </Link>

                                                    {lead.customer.company && (
                                                        <p className="mt-0.5 text-xs text-muted-foreground">
                                                            {
                                                                lead.customer
                                                                    .company
                                                            }
                                                        </p>
                                                    )}
                                                </td>

                                                <td className="px-5 py-4 text-right font-medium tabular-nums">
                                                    {formatCurrency(
                                                        lead.estimated_value,
                                                    )}
                                                </td>

                                                <td className="px-5 py-4">
                                                    <LeadStatusBadge
                                                        status={lead.status}
                                                    />
                                                </td>

                                                <td className="px-5 py-4">
                                                    {lead.assigned_user ? (
                                                        lead.assigned_user.name
                                                    ) : (
                                                        <span className="text-muted-foreground">
                                                            Unassigned
                                                        </span>
                                                    )}
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
                                                                    lead.id,
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
                                                                    lead.id,
                                                                )}
                                                            >
                                                                <Pencil />
                                                            </Link>
                                                        </Button>

                                                        {can.manage && (
                                                            <LeadDeleteDialog
                                                                lead={lead}
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
                                    Showing {leads.meta.from ?? 0}–
                                    {leads.meta.to ?? 0} of {leads.meta.total}{' '}
                                    leads
                                </p>

                                <div className="flex items-center gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        disabled={!leads.links.prev}
                                        onClick={() => {
                                            if (leads.links.prev) {
                                                router.visit(leads.links.prev, {
                                                    preserveScroll: true,
                                                    preserveState: true,
                                                });
                                            }
                                        }}
                                    >
                                        Previous
                                    </Button>

                                    <span className="px-2 text-sm text-muted-foreground">
                                        Page {leads.meta.current_page} of{' '}
                                        {leads.meta.last_page}
                                    </span>

                                    <Button
                                        variant="outline"
                                        size="sm"
                                        disabled={!leads.links.next}
                                        onClick={() => {
                                            if (leads.links.next) {
                                                router.visit(leads.links.next, {
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

LeadsIndex.layout = {
    breadcrumbs: [
        {
            title: 'Leads',
            href: index(),
        },
    ],
};
