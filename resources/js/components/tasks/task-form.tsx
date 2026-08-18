import { Form, Link } from '@inertiajs/react';
import { CalendarDays, ListTodo, Save } from 'lucide-react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index, show, store, update } from '@/routes/tasks';
import type { DealOption, SelectOption, Task } from '@/types';

type RelationType = 'customer' | 'deal';

type Props = {
    task?: Task;
    customers: SelectOption[];
    deals: DealOption[];
    users: SelectOption[];
    canManageTask?: boolean;
};

export function TaskForm({
    task,
    customers,
    deals,
    users,
    canManageTask = true,
}: Props) {
    const editing = task !== undefined;

    const initialRelation: RelationType =
        task?.deal_id !== null && task?.deal_id !== undefined
            ? 'deal'
            : 'customer';

    const [relationType, setRelationType] =
        useState<RelationType>(initialRelation);

    const action = editing ? update(task.id) : store();

    const cancelHref = editing ? show(task.id) : index();

    return (
        <Form action={action} className="space-y-6">
            {({ errors, processing }) => (
                <>
                    <Card>
                        <CardHeader>
                            <CardTitle>Task information</CardTitle>

                            <CardDescription>
                                {canManageTask
                                    ? 'Define the work, ownership, priority and deadline.'
                                    : 'Update your progress and task description.'}
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="grid gap-6 md:grid-cols-2">
                            {canManageTask ? (
                                <>
                                    <div className="grid gap-2 md:col-span-2">
                                        <Label htmlFor="title">Title</Label>

                                        <div className="relative">
                                            <ListTodo className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                                            <Input
                                                id="title"
                                                name="title"
                                                defaultValue={task?.title ?? ''}
                                                placeholder="Follow up with customer"
                                                className="pl-9"
                                                required
                                                autoFocus
                                            />
                                        </div>

                                        <InputError message={errors.title} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="assigned_user_id">
                                            Assigned to
                                        </Label>

                                        <select
                                            id="assigned_user_id"
                                            name="assigned_user_id"
                                            defaultValue={
                                                task?.assigned_user_id ?? ''
                                            }
                                            className="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                                        >
                                            <option value="">Unassigned</option>

                                            {users.map((user) => (
                                                <option
                                                    key={user.id}
                                                    value={user.id}
                                                >
                                                    {user.name}
                                                </option>
                                            ))}
                                        </select>

                                        <InputError
                                            message={errors.assigned_user_id}
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="priority">
                                            Priority
                                        </Label>

                                        <select
                                            id="priority"
                                            name="priority"
                                            defaultValue={
                                                task?.priority ?? 'medium'
                                            }
                                            className="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                                        >
                                            <option value="low">Low</option>

                                            <option value="medium">
                                                Medium
                                            </option>

                                            <option value="high">High</option>
                                        </select>

                                        <InputError message={errors.priority} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="due_date">
                                            Due date
                                        </Label>

                                        <div className="relative">
                                            <CalendarDays className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                                            <Input
                                                id="due_date"
                                                name="due_date"
                                                type="date"
                                                defaultValue={
                                                    task?.due_date ?? ''
                                                }
                                                className="pl-9"
                                            />
                                        </div>

                                        <InputError message={errors.due_date} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="completed">
                                            Status
                                        </Label>

                                        <select
                                            id="completed"
                                            name="completed"
                                            defaultValue={
                                                task?.completed ? '1' : '0'
                                            }
                                            className="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                                        >
                                            <option value="0">Open</option>

                                            <option value="1">Completed</option>
                                        </select>
                                    </div>

                                    <div className="grid gap-2 md:col-span-2">
                                        <Label>Related to</Label>

                                        <div className="flex gap-2">
                                            <Button
                                                type="button"
                                                variant={
                                                    relationType === 'customer'
                                                        ? 'default'
                                                        : 'outline'
                                                }
                                                onClick={() =>
                                                    setRelationType('customer')
                                                }
                                            >
                                                Customer
                                            </Button>

                                            <Button
                                                type="button"
                                                variant={
                                                    relationType === 'deal'
                                                        ? 'default'
                                                        : 'outline'
                                                }
                                                onClick={() =>
                                                    setRelationType('deal')
                                                }
                                            >
                                                Deal
                                            </Button>
                                        </div>
                                    </div>

                                    {relationType === 'customer' ? (
                                        <div className="grid gap-2 md:col-span-2">
                                            <Label htmlFor="customer_id">
                                                Customer
                                            </Label>

                                            <select
                                                id="customer_id"
                                                name="customer_id"
                                                defaultValue={
                                                    task?.customer_id ?? ''
                                                }
                                                required
                                                className="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                                            >
                                                <option value="" disabled>
                                                    Select customer
                                                </option>

                                                {customers.map((customer) => (
                                                    <option
                                                        key={customer.id}
                                                        value={customer.id}
                                                    >
                                                        {customer.name}
                                                    </option>
                                                ))}
                                            </select>

                                            <InputError
                                                message={errors.customer_id}
                                            />
                                        </div>
                                    ) : (
                                        <div className="grid gap-2 md:col-span-2">
                                            <Label htmlFor="deal_id">
                                                Deal
                                            </Label>

                                            <select
                                                id="deal_id"
                                                name="deal_id"
                                                defaultValue={
                                                    task?.deal_id ?? ''
                                                }
                                                required
                                                className="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                                            >
                                                <option value="" disabled>
                                                    Select deal
                                                </option>

                                                {deals.map((deal) => (
                                                    <option
                                                        key={deal.id}
                                                        value={deal.id}
                                                    >
                                                        {deal.title}
                                                    </option>
                                                ))}
                                            </select>

                                            <InputError
                                                message={errors.deal_id}
                                            />
                                        </div>
                                    )}
                                </>
                            ) : (
                                <>
                                    <input
                                        type="hidden"
                                        name="title"
                                        value={task?.title ?? ''}
                                    />

                                    <input
                                        type="hidden"
                                        name="assigned_user_id"
                                        value={task?.assigned_user_id ?? ''}
                                    />

                                    <input
                                        type="hidden"
                                        name="customer_id"
                                        value={task?.customer_id ?? ''}
                                    />

                                    <input
                                        type="hidden"
                                        name="deal_id"
                                        value={task?.deal_id ?? ''}
                                    />

                                    <input
                                        type="hidden"
                                        name="priority"
                                        value={task?.priority ?? 'medium'}
                                    />

                                    <input
                                        type="hidden"
                                        name="due_date"
                                        value={task?.due_date ?? ''}
                                    />

                                    <div className="rounded-lg border bg-muted/40 p-4 md:col-span-2">
                                        <p className="font-medium">
                                            {task?.title}
                                        </p>

                                        <p className="mt-1 text-sm text-muted-foreground">
                                            {task?.customer?.name ??
                                                task?.deal?.title ??
                                                'No relation'}
                                        </p>
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="completed">
                                            Status
                                        </Label>

                                        <select
                                            id="completed"
                                            name="completed"
                                            defaultValue={
                                                task?.completed ? '1' : '0'
                                            }
                                            className="h-9 w-full rounded-md border border-input bg-background px-3 text-sm"
                                        >
                                            <option value="0">Open</option>

                                            <option value="1">Completed</option>
                                        </select>
                                    </div>
                                </>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Description</CardTitle>

                            <CardDescription>
                                Work details and useful context.
                            </CardDescription>
                        </CardHeader>

                        <CardContent>
                            <div className="grid gap-2">
                                <Label htmlFor="description">
                                    Task description
                                </Label>

                                <textarea
                                    id="description"
                                    name="description"
                                    rows={7}
                                    defaultValue={task?.description ?? ''}
                                    placeholder="Describe what needs to be done..."
                                    className="min-h-36 w-full resize-y rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                                />

                                <InputError message={errors.description} />
                            </div>
                        </CardContent>
                    </Card>

                    <div className="flex justify-end gap-3">
                        <Button variant="outline" asChild>
                            <Link href={cancelHref}>Cancel</Link>
                        </Button>

                        <Button type="submit" disabled={processing}>
                            <Save />

                            {processing
                                ? 'Saving...'
                                : editing
                                  ? 'Save changes'
                                  : 'Create task'}
                        </Button>
                    </div>
                </>
            )}
        </Form>
    );
}
