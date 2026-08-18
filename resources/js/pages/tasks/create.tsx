import { Head } from '@inertiajs/react';
import { TaskForm } from '@/components/tasks/task-form';
import { create, index } from '@/routes/tasks';
import type { DealOption, SelectOption } from '@/types';

type Props = {
    customers: SelectOption[];
    deals: DealOption[];
    users: SelectOption[];
};

export default function CreateTask({ customers, deals, users }: Props) {
    return (
        <>
            <Head title="Create task" />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div>
                    <p className="text-sm text-muted-foreground">CRM / Tasks</p>

                    <h1 className="mt-1 text-2xl font-semibold tracking-tight">
                        Create task
                    </h1>

                    <p className="mt-1 text-sm text-muted-foreground">
                        Create and assign a new CRM task.
                    </p>
                </div>

                <div className="max-w-5xl">
                    <TaskForm
                        customers={customers}
                        deals={deals}
                        users={users}
                    />
                </div>
            </div>
        </>
    );
}

CreateTask.layout = {
    breadcrumbs: [
        {
            title: 'Tasks',
            href: index(),
        },
        {
            title: 'Create',
            href: create(),
        },
    ],
};
