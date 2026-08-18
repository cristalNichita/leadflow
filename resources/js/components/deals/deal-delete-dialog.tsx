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
import { destroy } from '@/routes/deals';
import type { Deal } from '@/types';

type Props = {
    deal: Deal;
    trigger: ReactNode;
};

export function DealDeleteDialog({ deal, trigger }: Props) {
    const deleteDeal = () => {
        router.delete(destroy.url(deal.id), {
            preserveScroll: true,
        });
    };

    return (
        <AlertDialog>
            <AlertDialogTrigger asChild>{trigger}</AlertDialogTrigger>

            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete deal?</AlertDialogTitle>

                    <AlertDialogDescription>
                        You are about to permanently delete{' '}
                        <strong>{deal.title}</strong>. This action cannot be
                        undone.
                    </AlertDialogDescription>
                </AlertDialogHeader>

                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>

                    <AlertDialogAction
                        variant="destructive"
                        onClick={deleteDeal}
                    >
                        Delete deal
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
