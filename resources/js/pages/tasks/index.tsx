import { Head, Link, router } from '@inertiajs/react';
import { Eye, ListTodo, Pencil, Plus, Search, Trash2 } from 'lucide-react';
import type { SyntheticEvent } from 'react';
import { useState } from 'react';
import { TaskPriorityBadge } from '@/components/crm/status-badges';
import { TaskCompletionButton } from '@/components/tasks/task-completion-button';
import { TaskDeleteDialog } from '@/components/tasks/task-delete-dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { formatDate } from '@/lib/formatters';
import { show as customerShow } from '@/routes/customers';
import { show as dealShow } from '@/routes/deals';
import { create, edit, index, show } from '@/routes/tasks';
import type { PaginatedResource, SelectOption, Task } from '@/types';

type Props = {
    tasks: PaginatedResource<Task>;

    filters: {
        search: string;
        priority: string;
        completed: boolean | null;
        assigned_user_id: number | null;
    };

    users: SelectOption[];

    can: {
        create: boolean;
        manage: boolean;
    };
};

export default function TasksIndex({ tasks, filters, users, can }: Props) {
    const [search, setSearch] = useState(filters.search);

    const [priority, setPriority] = useState(filters.priority);

    const [completed, setCompleted] = useState(
        filters.completed === null ? '' : filters.completed ? '1' : '0',
    );

    const [assignedUserId, setAssignedUserId] = useState(
        filters.assigned_user_id?.toString() ?? '',
    );

    const applyFilters = (event: SyntheticEvent<HTMLFormElement>) => {
        event.preventDefault();

        router.get(
            index.url(),
            {
                search: search || undefined,
                priority: priority || undefined,
                completed: completed === '' ? undefined : completed,
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
        setPriority('');
        setCompleted('');
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
        filters.priority !== '' ||
        filters.completed !== null ||
        filters.assigned_user_id !== null;

    return (
        <>
            <Head title="Tasks" />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <header className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <p className="text-sm text-muted-foreground">CRM</p>

                        <h1 className="text-2xl font-semibold tracking-tight">
                            Tasks
                        </h1>

                        <p className="mt-1 text-sm text-muted-foreground">
                            Track follow-ups, deadlines and assigned work.
                        </p>
                    </div>

                    {can.create && (
                        <Button asChild>
                            <Link href={create()}>
                                <Plus />
                                Add task
                            </Link>
                        </Button>
                    )}
                </header>

                <form
                    onSubmit={applyFilters}
                    className="flex flex-col gap-3 rounded-xl border bg-card p-4 shadow-xs xl:flex-row"
                >
                    <div className="relative flex-1">
                        <Search className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                        <Input
                            value={search}
                            onChange={(event) => setSearch(event.target.value)}
                            placeholder="Search tasks..."
                            className="pl-9"
                        />
                    </div>

                    <select
                        value={priority}
                        onChange={(event) => setPriority(event.target.value)}
                        className="h-9 rounded-md border border-input bg-background px-3 text-sm xl:w-40"
                    >
                        <option value="">All priorities</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>

                    <select
                        value={completed}
                        onChange={(event) => setCompleted(event.target.value)}
                        className="h-9 rounded-md border border-input bg-background px-3 text-sm xl:w-40"
                    >
                        <option value="">All statuses</option>

                        <option value="0">Open</option>

                        <option value="1">Completed</option>
                    </select>

                    {can.manage && (
                        <select
                            value={assignedUserId}
                            onChange={(event) =>
                                setAssignedUserId(event.target.value)
                            }
                            className="h-9 rounded-md border border-input bg-background px-3 text-sm xl:w-48"
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
                    {tasks.data.length === 0 ? (
                        <div className="flex min-h-80 flex-col items-center justify-center px-6 text-center">
                            <div className="mb-4 flex size-12 items-center justify-center rounded-xl bg-muted">
                                <ListTodo className="size-5 text-muted-foreground" />
                            </div>

                            <h2 className="font-semibold">No tasks found</h2>

                            <p className="mt-1 max-w-sm text-sm text-muted-foreground">
                                {hasFilters
                                    ? 'No tasks match the current filters.'
                                    : 'Create your first task to start tracking follow-ups and deadlines.'}
                            </p>
                        </div>
                    ) : (
                        <>
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead className="border-b bg-muted/40">
                                        <tr>
                                            <th className="px-5 py-3 text-left font-medium">
                                                Task
                                            </th>
                                            <th className="px-5 py-3 text-left font-medium">
                                                Priority
                                            </th>
                                            <th className="px-5 py-3 text-left font-medium">
                                                Related to
                                            </th>
                                            <th className="px-5 py-3 text-left font-medium">
                                                Due
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
                                        {tasks.data.map((task) => (
                                            <tr
                                                key={task.id}
                                                className="transition-colors hover:bg-muted/30"
                                            >
                                                <td className="px-5 py-4">
                                                    <Link
                                                        href={show(task.id)}
                                                        className={
                                                            task.completed
                                                                ? 'font-medium text-muted-foreground line-through'
                                                                : 'font-medium hover:underline'
                                                        }
                                                    >
                                                        {task.title}
                                                    </Link>

                                                    <p className="mt-1 text-xs text-muted-foreground">
                                                        {task.completed
                                                            ? 'Completed'
                                                            : 'Open'}
                                                    </p>
                                                </td>

                                                <td className="px-5 py-4">
                                                    <TaskPriorityBadge
                                                        priority={task.priority}
                                                    />
                                                </td>

                                                <td className="px-5 py-4">
                                                    <TaskRelation task={task} />
                                                </td>

                                                <td className="px-5 py-4">
                                                    <DueDate task={task} />
                                                </td>

                                                <td className="px-5 py-4">
                                                    {task.assigned_user?.name ??
                                                        'Unassigned'}
                                                </td>

                                                <td className="px-5 py-4">
                                                    <div className="flex justify-end gap-1">
                                                        <TaskCompletionButton
                                                            task={task}
                                                            compact
                                                        />

                                                        <Button
                                                            variant="ghost"
                                                            size="icon"
                                                            asChild
                                                        >
                                                            <Link
                                                                href={show(
                                                                    task.id,
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
                                                                    task.id,
                                                                )}
                                                            >
                                                                <Pencil />
                                                            </Link>
                                                        </Button>

                                                        {can.manage && (
                                                            <TaskDeleteDialog
                                                                task={task}
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
                                    Showing {tasks.meta.from ?? 0}–
                                    {tasks.meta.to ?? 0} of {tasks.meta.total}{' '}
                                    tasks
                                </p>

                                <div className="flex items-center gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        disabled={!tasks.links.prev}
                                        onClick={() => {
                                            if (tasks.links.prev) {
                                                router.visit(tasks.links.prev, {
                                                    preserveScroll: true,
                                                    preserveState: true,
                                                });
                                            }
                                        }}
                                    >
                                        Previous
                                    </Button>

                                    <span className="px-2 text-sm text-muted-foreground">
                                        Page {tasks.meta.current_page} of{' '}
                                        {tasks.meta.last_page}
                                    </span>

                                    <Button
                                        variant="outline"
                                        size="sm"
                                        disabled={!tasks.links.next}
                                        onClick={() => {
                                            if (tasks.links.next) {
                                                router.visit(tasks.links.next, {
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

function DueDate({ task }: { task: Task }) {
    if (!task.due_date) {
        return <span className="text-muted-foreground">—</span>;
    }

    const overdue =
        !task.completed && new Date(`${task.due_date}T23:59:59`) < new Date();

    return (
        <span className={overdue ? 'font-medium text-destructive' : ''}>
            {formatDate(task.due_date)}
            {overdue && ' · Overdue'}
        </span>
    );
}

function TaskRelation({ task }: { task: Task }) {
    if (task.customer) {
        return (
            <Link
                href={customerShow(task.customer.id)}
                className="font-medium hover:underline"
            >
                {task.customer.name}
            </Link>
        );
    }

    if (task.deal) {
        return (
            <Link
                href={dealShow(task.deal.id)}
                className="font-medium hover:underline"
            >
                {task.deal.title}
            </Link>
        );
    }

    return <span className="text-muted-foreground">—</span>;
}

TasksIndex.layout = {
    breadcrumbs: [
        {
            title: 'Tasks',
            href: index(),
        },
    ],
};
