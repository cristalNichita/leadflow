import { Head, Link } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import { CalendarDays, Pencil, Trash2, UserRound } from 'lucide-react';
import { TaskPriorityBadge } from '@/components/crm/status-badges';
import { TaskCompletionButton } from '@/components/tasks/task-completion-button';
import { TaskDeleteDialog } from '@/components/tasks/task-delete-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { formatDate } from '@/lib/formatters';
import { edit, index } from '@/routes/tasks';
import type { Task, TaskPriority } from '@/types';

type Props = {
    task: Task;

    can: {
        update: boolean;
        delete: boolean;
    };
};

export default function ShowTask({ task, can }: Props) {
    return (
        <>
            <Head title={task.title} />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <header className="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    <div>
                        <div className="flex flex-wrap items-center gap-3">
                            <h1
                                className={`text-2xl font-semibold tracking-tight ${
                                    task.completed
                                        ? 'text-muted-foreground line-through'
                                        : ''
                                }`}
                            >
                                {task.title}
                            </h1>

                            <TaskPriorityBadge priority={task.priority} />

                            <Badge
                                variant={
                                    task.completed ? 'secondary' : 'outline'
                                }
                            >
                                {task.completed ? 'Completed' : 'Open'}
                            </Badge>
                        </div>

                        <p className="mt-1 text-sm text-muted-foreground">
                            {relationLabel(task)}
                        </p>
                    </div>

                    <div className="flex flex-wrap gap-2">
                        {can.update && (
                            <>
                                <TaskCompletionButton task={task} />

                                <Button variant="outline" asChild>
                                    <Link href={edit(task.id)}>
                                        <Pencil />
                                        Edit
                                    </Link>
                                </Button>
                            </>
                        )}

                        {can.delete && (
                            <TaskDeleteDialog
                                task={task}
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
                        title="Assigned to"
                        value={task.assigned_user?.name ?? 'Unassigned'}
                        icon={UserRound}
                    />

                    <InfoCard
                        title="Due date"
                        value={
                            task.due_date
                                ? formatDate(task.due_date)
                                : 'No deadline'
                        }
                        icon={CalendarDays}
                    />

                    <InfoCard
                        title="Related to"
                        value={relationLabel(task)}
                        icon={UserRound}
                    />
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <Card className="xl:col-span-2">
                        <CardHeader>
                            <CardTitle>Task details</CardTitle>

                            <CardDescription>
                                Assignment, priority and scheduling information.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="grid gap-6 sm:grid-cols-2">
                            <Detail
                                label="Priority"
                                value={priorityLabel(task.priority)}
                            />

                            <Detail
                                label="Status"
                                value={task.completed ? 'Completed' : 'Open'}
                            />

                            <Detail
                                label="Assigned to"
                                value={task.assigned_user?.name ?? 'Unassigned'}
                            />

                            <Detail
                                label="Related to"
                                value={relationLabel(task)}
                            />

                            <Detail
                                label="Due date"
                                value={
                                    task.due_date
                                        ? formatDate(task.due_date)
                                        : 'No deadline'
                                }
                            />

                            <Detail
                                label="Created"
                                value={formatDate(task.created_at)}
                            />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Description</CardTitle>
                        </CardHeader>

                        <CardContent>
                            <p className="text-sm leading-6 whitespace-pre-wrap text-muted-foreground">
                                {task.description ||
                                    'No description has been added.'}
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

function relationLabel(task: Task): string {
    if (task.customer) {
        return task.customer.company
            ? `${task.customer.name} · ${task.customer.company}`
            : task.customer.name;
    }

    if (task.deal) {
        return task.deal.title;
    }

    return 'No relation';
}

function priorityLabel(priority: TaskPriority): string {
    const labels: Record<TaskPriority, string> = {
        low: 'Low',
        medium: 'Medium',
        high: 'High',
    };

    return labels[priority];
}

ShowTask.layout = {
    breadcrumbs: [
        {
            title: 'Tasks',
            href: index(),
        },
    ],
};
