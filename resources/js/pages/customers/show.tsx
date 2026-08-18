import { Head, Link } from '@inertiajs/react';
import {
    Building2,
    CalendarDays,
    CircleDollarSign,
    ClipboardCheck,
    Mail,
    Pencil,
    Phone,
    Trash2,
    UserRound,
} from 'lucide-react';
import { CustomerDeleteDialog } from '@/components/customers/customer-delete-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { edit, index } from '@/routes/customers';
import type { Customer } from '@/types';

type Props = {
    customer: Customer;

    can: {
        update: boolean;
        delete: boolean;
    };
};

export default function ShowCustomer({ customer, can }: Props) {
    return (
        <>
            <Head title={customer.name} />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <header className="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    <div>
                        <div className="flex flex-wrap items-center gap-3">
                            <h1 className="text-2xl font-semibold tracking-tight">
                                {customer.name}
                            </h1>

                            <Badge
                                variant={
                                    customer.status === 'active'
                                        ? 'default'
                                        : 'secondary'
                                }
                            >
                                {customer.status === 'active'
                                    ? 'Active'
                                    : 'Inactive'}
                            </Badge>
                        </div>

                        <p className="mt-1 text-sm text-muted-foreground">
                            {customer.company ?? 'Individual customer'}
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        {can.update && (
                            <Button variant="outline" asChild>
                                <Link href={edit(customer.id)}>
                                    <Pencil />
                                    Edit
                                </Link>
                            </Button>
                        )}

                        {can.delete && (
                            <CustomerDeleteDialog
                                customer={customer}
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
                    <StatCard
                        title="Leads"
                        value={customer.leads_count ?? 0}
                        description="Associated leads"
                        icon={UserRound}
                    />

                    <StatCard
                        title="Deals"
                        value={customer.deals_count ?? 0}
                        description="Customer deals"
                        icon={CircleDollarSign}
                    />

                    <StatCard
                        title="Tasks"
                        value={customer.tasks_count ?? 0}
                        description="Related tasks"
                        icon={ClipboardCheck}
                    />
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <Card className="xl:col-span-2">
                        <CardHeader>
                            <CardTitle>Customer details</CardTitle>

                            <CardDescription>
                                Contact and company information.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="grid gap-6 sm:grid-cols-2">
                            <Detail
                                icon={Building2}
                                label="Company"
                                value={customer.company ?? 'Not provided'}
                            />

                            <Detail
                                icon={Mail}
                                label="Email"
                                value={customer.email ?? 'Not provided'}
                            />

                            <Detail
                                icon={Phone}
                                label="Phone"
                                value={customer.phone ?? 'Not provided'}
                            />

                            <Detail
                                icon={CalendarDays}
                                label="Customer since"
                                value={formatDate(customer.created_at)}
                            />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Notes</CardTitle>

                            <CardDescription>
                                Internal CRM notes.
                            </CardDescription>
                        </CardHeader>

                        <CardContent>
                            {customer.notes ? (
                                <p className="text-sm leading-6 whitespace-pre-wrap">
                                    {customer.notes}
                                </p>
                            ) : (
                                <p className="text-sm text-muted-foreground">
                                    No notes have been added for this customer.
                                </p>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

function StatCard({
    title,
    value,
    description,
    icon: Icon,
}: {
    title: string;
    value: number;
    description: string;
    icon: typeof UserRound;
}) {
    return (
        <Card>
            <CardContent className="flex items-center justify-between p-6">
                <div>
                    <p className="text-sm text-muted-foreground">{title}</p>

                    <p className="mt-1 text-3xl font-semibold tracking-tight">
                        {value}
                    </p>

                    <p className="mt-1 text-xs text-muted-foreground">
                        {description}
                    </p>
                </div>

                <div className="flex size-11 items-center justify-center rounded-xl bg-muted">
                    <Icon className="size-5 text-muted-foreground" />
                </div>
            </CardContent>
        </Card>
    );
}

function Detail({
    icon: Icon,
    label,
    value,
}: {
    icon: typeof Building2;
    label: string;
    value: string;
}) {
    return (
        <div className="flex items-start gap-3">
            <div className="flex size-9 shrink-0 items-center justify-center rounded-lg bg-muted">
                <Icon className="size-4 text-muted-foreground" />
            </div>

            <div className="min-w-0">
                <p className="text-xs text-muted-foreground">{label}</p>

                <p className="mt-1 text-sm font-medium break-words">{value}</p>
            </div>
        </div>
    );
}

function formatDate(value: string): string {
    return new Intl.DateTimeFormat('en', {
        dateStyle: 'medium',
    }).format(new Date(value));
}

ShowCustomer.layout = {
    breadcrumbs: [
        {
            title: 'Customers',
            href: index(),
        },
    ],
};
