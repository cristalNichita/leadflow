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
import { destroy } from '@/routes/leads';
import type { Lead } from '@/types';

type Props = {
    lead: Lead;
    trigger: ReactNode;
};

export function LeadDeleteDialog({ lead, trigger }: Props) {
    const deleteLead = () => {
        router.delete(destroy.url(lead.id), {
            preserveScroll: true,
        });
    };

    return (
        <AlertDialog>
            <AlertDialogTrigger asChild>{trigger}</AlertDialogTrigger>

            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete lead?</AlertDialogTitle>

                    <AlertDialogDescription>
                        You are about to permanently delete{' '}
                        <strong>{lead.title}</strong>. This action cannot be
                        undone.
                    </AlertDialogDescription>
                </AlertDialogHeader>

                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>

                    <AlertDialogAction
                        variant="destructive"
                        onClick={deleteLead}
                    >
                        Delete lead
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
