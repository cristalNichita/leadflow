import { Head, Link } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import {
    Activity as ActivityIcon,
    CircleDollarSign,
    Clock3,
    Handshake,
    Target,
    UsersRound,
} from 'lucide-react';
import { TaskPriorityBadge } from '@/components/crm/status-badges';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { formatCurrency, formatDate, formatDateTime } from '@/lib/formatters';
import { dashboard } from '@/routes';
import { index as customersIndex } from '@/routes/customers';
import { index as dealsIndex } from '@/routes/deals';
import { index as leadsIndex } from '@/routes/leads';
import { index as tasksIndex, show as taskShow } from '@/routes/tasks';
import type { NavItem } from '@/types';
import type {
    Activity,
    DashboardMetrics,
    DealStatusBreakdown,
    LeadStatusBreakdown,
    Task,
} from '@/types';

type Props = {
    metrics: DashboardMetrics;
    leadStatus: LeadStatusBreakdown;
    dealStatus: DealStatusBreakdown;
    recentActivities: Activity[];
    upcomingTasks: Task[];
};

export default function Dashboard({
    metrics,
    leadStatus,
    dealStatus,
    recentActivities,
    upcomingTasks,
}: Props) {
    return (
        <>
            <Head title="Dashboard" />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <header>
                    <p className="text-sm text-muted-foreground">Workspace</p>

                    <h1 className="text-2xl font-semibold tracking-tight">
                        Dashboard
                    </h1>

                    <p className="mt-1 text-sm text-muted-foreground">
                        An overview of your sales pipeline and current CRM
                        activity.
                    </p>
                </header>

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <MetricCard
                        title="Total customers"
                        value={metrics.total_customers.toString()}
                        description="Customers in your workspace"
                        icon={UsersRound}
                        href={customersIndex()}
                    />

                    <MetricCard
                        title="Active leads"
                        value={metrics.active_leads.toString()}
                        description="New, contacted and qualified"
                        icon={Target}
                        href={leadsIndex()}
                    />

                    <MetricCard
                        title="Open deals"
                        value={metrics.open_deals.toString()}
                        description="Deals still in progress"
                        icon={Handshake}
                        href={dealsIndex()}
                    />

                    <MetricCard
                        title="Won revenue"
                        value={formatCurrency(metrics.won_revenue)}
                        description="Value of won deals"
                        icon={CircleDollarSign}
                        href={dealsIndex()}
                    />
                </div>

                <div className="grid gap-6 xl:grid-cols-2">
                    <StatusCard
                        title="Lead pipeline"
                        description="Distribution across lead statuses."
                        items={[
                            {
                                label: 'New',
                                value: leadStatus.new,
                                barClass: 'bg-slate-500',
                            },
                            {
                                label: 'Contacted',
                                value: leadStatus.contacted,
                                barClass: 'bg-blue-500',
                            },
                            {
                                label: 'Qualified',
                                value: leadStatus.qualified,
                                barClass: 'bg-amber-500',
                            },
                            {
                                label: 'Won',
                                value: leadStatus.won,
                                barClass: 'bg-emerald-500',
                            },
                            {
                                label: 'Lost',
                                value: leadStatus.lost,
                                barClass: 'bg-red-500',
                            },
                        ]}
                    />

                    <StatusCard
                        title="Deal performance"
                        description="Current deal status distribution."
                        items={[
                            {
                                label: 'Open',
                                value: dealStatus.open,
                                barClass: 'bg-blue-500',
                            },
                            {
                                label: 'Won',
                                value: dealStatus.won,
                                barClass: 'bg-emerald-500',
                            },
                            {
                                label: 'Lost',
                                value: dealStatus.lost,
                                barClass: 'bg-red-500',
                            },
                        ]}
                    />
                </div>

                <div className="grid gap-6 xl:grid-cols-5">
                    <Card className="xl:col-span-3">
                        <CardHeader>
                            <div className="flex items-center justify-between gap-4">
                                <div>
                                    <CardTitle>Recent activity</CardTitle>

                                    <CardDescription>
                                        Latest changes across the CRM.
                                    </CardDescription>
                                </div>

                                <ActivityIcon className="size-5 text-muted-foreground" />
                            </div>
                        </CardHeader>

                        <CardContent>
                            {recentActivities.length === 0 ? (
                                <EmptyMessage>
                                    No activity has been recorded yet.
                                </EmptyMessage>
                            ) : (
                                <div className="divide-y">
                                    {recentActivities.map((activity) => (
                                        <div
                                            key={activity.id}
                                            className="flex gap-3 py-4 first:pt-0 last:pb-0"
                                        >
                                            <div className="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-full bg-muted">
                                                <ActivityIcon className="size-4 text-muted-foreground" />
                                            </div>

                                            <div className="min-w-0 flex-1">
                                                <p className="text-sm leading-5">
                                                    {activity.description}
                                                </p>

                                                <p className="mt-1 text-xs text-muted-foreground">
                                                    {formatDateTime(
                                                        activity.created_at,
                                                    )}
                                                </p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card className="xl:col-span-2">
                        <CardHeader>
                            <div className="flex items-center justify-between gap-4">
                                <div>
                                    <CardTitle>Upcoming tasks</CardTitle>

                                    <CardDescription>
                                        Your closest CRM deadlines.
                                    </CardDescription>
                                </div>

                                <Link
                                    href={tasksIndex()}
                                    className="text-xs text-muted-foreground hover:text-foreground"
                                >
                                    View all
                                </Link>
                            </div>
                        </CardHeader>

                        <CardContent>
                            {upcomingTasks.length === 0 ? (
                                <EmptyMessage>No upcoming tasks.</EmptyMessage>
                            ) : (
                                <div className="space-y-3">
                                    {upcomingTasks.map((task) => (
                                        <Link
                                            key={task.id}
                                            href={taskShow(task.id)}
                                            className="block rounded-lg border p-3 transition-colors hover:bg-muted/50"
                                        >
                                            <div className="flex items-start justify-between gap-3">
                                                <div className="min-w-0">
                                                    <p className="truncate text-sm font-medium">
                                                        {task.title}
                                                    </p>

                                                    <p className="mt-1 truncate text-xs text-muted-foreground">
                                                        {taskRelation(task)}
                                                    </p>
                                                </div>

                                                <TaskPriorityBadge
                                                    priority={task.priority}
                                                />
                                            </div>

                                            <div
                                                className={`mt-3 flex items-center gap-1.5 text-xs ${
                                                    isOverdue(task)
                                                        ? 'font-medium text-destructive'
                                                        : 'text-muted-foreground'
                                                }`}
                                            >
                                                <Clock3 className="size-3.5" />

                                                {task.due_date
                                                    ? formatDate(task.due_date)
                                                    : 'No deadline'}

                                                {isOverdue(task) &&
                                                    ' · Overdue'}
                                            </div>
                                        </Link>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

function MetricCard({
    title,
    value,
    description,
    icon: Icon,
    href,
}: {
    title: string;
    value: string;
    description: string;
    icon: LucideIcon;
    href: NavItem['href'];
}) {
    return (
        <Link href={href}>
            <Card className="group h-full transition-all hover:-translate-y-0.5 hover:border-foreground/20 hover:shadow-md">
                <CardContent className="flex items-start justify-between p-6">
                    <div>
                        <p className="text-sm text-muted-foreground">{title}</p>

                        <p className="mt-2 text-3xl font-semibold tracking-tight">
                            {value}
                        </p>

                        <p className="mt-2 text-xs text-muted-foreground">
                            {description}
                        </p>
                    </div>

                    <div className="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <Icon className="size-5" />
                    </div>
                </CardContent>
            </Card>
        </Link>
    );
}

function StatusCard({
    title,
    description,
    items,
}: {
    title: string;
    description: string;
    items: {
        label: string;
        value: number;
        barClass: string;
    }[];
}) {
    const total = items.reduce((sum, item) => sum + item.value, 0);

    return (
        <Card>
            <CardHeader>
                <CardTitle>{title}</CardTitle>

                <CardDescription>{description}</CardDescription>
            </CardHeader>

            <CardContent className="space-y-5">
                {items.map((item) => {
                    const percentage =
                        total === 0 ? 0 : (item.value / total) * 100;

                    return (
                        <div key={item.label} className="space-y-2">
                            <div className="flex items-center justify-between text-sm">
                                <span>{item.label}</span>

                                <span className="text-muted-foreground">
                                    {item.value}
                                </span>
                            </div>

                            <div className="h-2 overflow-hidden rounded-full bg-muted">
                                <div
                                    className={`h-full rounded-full transition-all ${item.barClass}`}
                                    style={{
                                        width: `${percentage}%`,
                                    }}
                                />
                            </div>
                        </div>
                    );
                })}

                {total === 0 && (
                    <p className="text-sm text-muted-foreground">
                        No data available yet.
                    </p>
                )}
            </CardContent>
        </Card>
    );
}

function EmptyMessage({ children }: { children: React.ReactNode }) {
    return (
        <div className="flex min-h-28 items-center justify-center text-center text-sm text-muted-foreground">
            {children}
        </div>
    );
}

function taskRelation(task: Task): string {
    if (task.customer) {
        return task.customer.name;
    }

    if (task.deal) {
        return task.deal.title;
    }

    return 'No relation';
}

function isOverdue(task: Task): boolean {
    if (!task.due_date || task.completed) {
        return false;
    }

    return new Date(`${task.due_date}T23:59:59`) < new Date();
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
