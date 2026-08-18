import { Head } from '@inertiajs/react';
import { TaskForm } from '@/components/tasks/task-form';
import { index } from '@/routes/tasks';
import type { DealOption, SelectOption, Task } from '@/types';

type Props = {
    task: Task;
    customers: SelectOption[];
    deals: DealOption[];
    users: SelectOption[];
    canManageTask: boolean;
};

export default function EditTask({
    task,
    customers,
    deals,
    users,
    canManageTask,
}: Props) {
    return (
        <>
            <Head title={`Edit ${task.title}`} />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div>
                    <p className="text-sm text-muted-foreground">CRM / Tasks</p>

                    <h1 className="mt-1 text-2xl font-semibold tracking-tight">
                        Edit task
                    </h1>

                    <p className="mt-1 text-sm text-muted-foreground">
                        Update{' '}
                        <span className="font-medium text-foreground">
                            {task.title}
                        </span>
                        .
                    </p>
                </div>

                <div className="max-w-5xl">
                    <TaskForm
                        task={task}
                        customers={customers}
                        deals={deals}
                        users={users}
                        canManageTask={canManageTask}
                    />
                </div>
            </div>
        </>
    );
}

EditTask.layout = {
    breadcrumbs: [
        {
            title: 'Tasks',
            href: index(),
        },
    ],
};
