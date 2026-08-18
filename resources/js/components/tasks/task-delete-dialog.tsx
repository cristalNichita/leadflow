import { router } from '@inertiajs/react';
import type { ReactNode } from 'react';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { destroy } from '@/routes/tasks';
import type { Task } from '@/types';

type Props = {
    task: Task;
    trigger: ReactNode;
};

export function TaskDeleteDialog({ task, trigger }: Props) {
    const deleteTask = () => {
        router.delete(destroy.url(task.id), {
            preserveScroll: true,
        });
    };

    return (
        <AlertDialog>
            <AlertDialogTrigger asChild>{trigger}</AlertDialogTrigger>

            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete task?</AlertDialogTitle>

                    <AlertDialogDescription>
                        You are about to permanently delete{' '}
                        <strong>{task.title}</strong>. This action cannot be
                        undone.
                    </AlertDialogDescription>
                </AlertDialogHeader>

                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>

                    <AlertDialogAction
                        variant="destructive"
                        onClick={deleteTask}
                    >
                        Delete task
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
