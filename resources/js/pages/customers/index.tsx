import { Head, Link, router } from '@inertiajs/react';
import {
    Building2,
    EyeIcon,
    PenLine,
    Plus,
    Search,
    Trash2,
    UserRoundIcon,
} from 'lucide-react';
import type { SyntheticEvent } from 'react';
import { useState } from 'react';
import { CustomerStatusBadge } from '@/components/crm/status-badges';
import { CustomerDeleteDialog } from '@/components/customers/customer-delete-dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { create, edit, index, show } from '@/routes/customers';
import type { Customer, PaginatedResource } from '@/types';

type Props = {
    customers: PaginatedResource<Customer>;

    filters: {
        search: string;
        status: string;
    };

    can: {
        create: boolean;
    };
};

export default function CustomersIndex({ customers, filters, can }: Props) {
    const [search, setSearch] = useState(filters.search);
    const [status, setStatus] = useState(filters.status);

    const applyFilters = (event: SyntheticEvent<HTMLFormElement>) => {
        event.preventDefault();

        router.get(
            index.url(),
            {
                search: search || undefined,
                status: status || undefined,
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

        router.get(
            index.url(),
            {},
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const hasFilters = filters.search !== '' || filters.status !== '';

    return (
        <>
            <Head title="Customers" />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <header className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <p className="text-sm text-muted-foreground">CRM</p>

                        <h1 className="text-2xl font-semibold tracking-tight">
                            Customers
                        </h1>

                        <p className="mt-1 text-sm text-muted-foreground">
                            Manage your customer relationships, companies and
                            contact information.
                        </p>
                    </div>

                    {can.create && (
                        <Button asChild>
                            <Link href={create()}>
                                <Plus />
                                Add customer
                            </Link>
                        </Button>
                    )}
                </header>

                <form
                    onSubmit={applyFilters}
                    className="flex flex-col gap-3 rounded-xl border bg-card p-4 shadow-xs md:flex-row"
                >
                    <div className="relative flex-1">
                        <Search className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                        <Input
                            value={search}
                            onChange={(event) => setSearch(event.target.value)}
                            placeholder="Search customers..."
                            className="pl-9"
                        />
                    </div>

                    <select
                        value={status}
                        onChange={(event) => setStatus(event.target.value)}
                        className="h-9 rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 md:w-44"
                    >
                        <option value="">All statuses</option>

                        <option value="active">Active</option>

                        <option value="inactive">Inactive</option>
                    </select>

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
                    {customers.data.length === 0 ? (
                        <EmptyState
                            hasFilters={hasFilters}
                            canCreate={can.create}
                        />
                    ) : (
                        <>
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead className="border-b bg-muted/40">
                                        <tr>
                                            <th className="px-5 py-3 text-left font-medium">
                                                Customer
                                            </th>

                                            <th className="px-5 py-3 text-left font-medium">
                                                Contact
                                            </th>

                                            <th className="px-5 py-3 text-left font-medium">
                                                Status
                                            </th>

                                            <th className="px-5 py-3 text-left font-medium">
                                                Leads
                                            </th>

                                            <th className="px-5 py-3 text-left font-medium">
                                                Deals
                                            </th>

                                            <th className="px-5 py-3 text-right font-medium">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody className="divide-y">
                                        {customers.data.map((customer) => (
                                            <tr
                                                key={customer.id}
                                                className="transition-colors hover:bg-muted/30"
                                            >
                                                <td className="px-5 py-4">
                                                    <div className="flex items-center gap-3">
                                                        <div className="flex size-9 shrink-0 items-center justify-center rounded-lg bg-muted">
                                                            <Building2 className="size-4 text-muted-foreground" />
                                                        </div>

                                                        <div>
                                                            <Link
                                                                href={show(
                                                                    customer.id,
                                                                )}
                                                                className="font-medium hover:underline"
                                                            >
                                                                {customer.name}
                                                            </Link>

                                                            <p className="mt-0.5 text-xs text-muted-foreground">
                                                                {customer.company ??
                                                                    'No company'}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td className="px-5 py-4">
                                                    <p>
                                                        {customer.email ?? '—'}
                                                    </p>

                                                    <p className="mt-0.5 text-xs text-muted-foreground">
                                                        {customer.phone ?? '—'}
                                                    </p>
                                                </td>

                                                <td className="px-5 py-4">
                                                    <CustomerStatusBadge
                                                        status={customer.status}
                                                    />
                                                </td>

                                                <td className="px-5 py-4">
                                                    <span className="inline-flex min-w-7 items-center justify-center rounded-md bg-muted px-2 py-1 text-xs font-medium">
                                                        {customer.leads_count ??
                                                            0}
                                                    </span>
                                                </td>

                                                <td className="px-5 py-4">
                                                    <span className="inline-flex min-w-7 items-center justify-center rounded-md bg-muted px-2 py-1 text-xs font-medium">
                                                        {customer.deals_count ??
                                                            0}
                                                    </span>
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
                                                                    customer.id,
                                                                )}
                                                                title="View customer"
                                                            >
                                                                <EyeIcon />
                                                            </Link>
                                                        </Button>

                                                        {can.create && (
                                                            <>
                                                                <Button
                                                                    variant="ghost"
                                                                    size="icon"
                                                                    asChild
                                                                >
                                                                    <Link
                                                                        href={edit(
                                                                            customer.id,
                                                                        )}
                                                                        title="Edit customer"
                                                                    >
                                                                        <PenLine />
                                                                    </Link>
                                                                </Button>

                                                                <CustomerDeleteDialog
                                                                    customer={
                                                                        customer
                                                                    }
                                                                    trigger={
                                                                        <Button
                                                                            type="button"
                                                                            variant="ghost"
                                                                            size="icon"
                                                                            title="Delete customer"
                                                                        >
                                                                            <Trash2 className="text-destructive" />
                                                                        </Button>
                                                                    }
                                                                />
                                                            </>
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            <Pagination customers={customers} />
                        </>
                    )}
                </div>
            </div>
        </>
    );
}

function EmptyState({
    hasFilters,
    canCreate,
}: {
    hasFilters: boolean;
    canCreate: boolean;
}) {
    return (
        <div className="flex min-h-80 flex-col items-center justify-center px-6 text-center">
            <div className="mb-4 flex size-12 items-center justify-center rounded-xl bg-muted">
                <UserRoundIcon className="size-5 text-muted-foreground" />
            </div>

            <h2 className="font-semibold">No customers found</h2>

            <p className="mt-1 max-w-sm text-sm text-muted-foreground">
                {hasFilters
                    ? 'No customers match the current filters.'
                    : 'Add your first customer to start building your CRM.'}
            </p>

            {!hasFilters && canCreate && (
                <Button className="mt-4" asChild>
                    <Link href={create()}>
                        <Plus />
                        Add customer
                    </Link>
                </Button>
            )}
        </div>
    );
}

function Pagination({ customers }: { customers: PaginatedResource<Customer> }) {
    return (
        <div className="flex flex-col justify-between gap-3 border-t px-5 py-4 sm:flex-row sm:items-center">
            <p className="text-sm text-muted-foreground">
                Showing {customers.meta.from ?? 0}–{customers.meta.to ?? 0} of{' '}
                {customers.meta.total} customers
            </p>

            <div className="flex items-center gap-2">
                <Button
                    variant="outline"
                    size="sm"
                    disabled={!customers.links.prev}
                    onClick={() => {
                        if (customers.links.prev) {
                            router.visit(customers.links.prev, {
                                preserveScroll: true,
                                preserveState: true,
                            });
                        }
                    }}
                >
                    Previous
                </Button>

                <span className="px-2 text-sm text-muted-foreground">
                    Page {customers.meta.current_page} of{' '}
                    {customers.meta.last_page}
                </span>

                <Button
                    variant="outline"
                    size="sm"
                    disabled={!customers.links.next}
                    onClick={() => {
                        if (customers.links.next) {
                            router.visit(customers.links.next, {
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
    );
}

CustomersIndex.layout = {
    breadcrumbs: [
        {
            title: 'Customers',
            href: index(),
        },
    ],
};
