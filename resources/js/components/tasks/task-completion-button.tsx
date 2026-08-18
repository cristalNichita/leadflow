import { Form } from '@inertiajs/react';
import { Check, RotateCcw } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { completion } from '@/routes/tasks';
import type { Task } from '@/types';

type Props = {
    task: Task;
    compact?: boolean;
};

export function TaskCompletionButton({ task, compact = false }: Props) {
    return (
        <Form action={completion(task.id)}>
            {({ processing }) => (
                <>
                    <input
                        type="hidden"
                        name="completed"
                        value={task.completed ? '0' : '1'}
                    />

                    <Button
                        type="submit"
                        variant={task.completed ? 'outline' : 'default'}
                        size={compact ? 'icon' : 'default'}
                        disabled={processing}
                        title={task.completed ? 'Reopen task' : 'Complete task'}
                    >
                        {task.completed ? <RotateCcw /> : <Check />}

                        {!compact && (task.completed ? 'Reopen' : 'Complete')}
                    </Button>
                </>
            )}
        </Form>
    );
}
