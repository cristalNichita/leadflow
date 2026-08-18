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
import { destroy } from '@/routes/customers';
import type { Customer } from '@/types';

type Props = {
    customer: Customer;
    trigger: ReactNode;
};

export function CustomerDeleteDialog({ customer, trigger }: Props) {
    const deleteCustomer = () => {
        router.delete(destroy.url(customer.id), {
            preserveScroll: true,
        });
    };

    return (
        <AlertDialog>
            <AlertDialogTrigger asChild>{trigger}</AlertDialogTrigger>

            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete customer?</AlertDialogTitle>
                    <AlertDialogDescription>
                        You are about to permanently delete{' '}
                        <strong>{customer.name}</strong> This action cannot be
                        undone.
                    </AlertDialogDescription>
                </AlertDialogHeader>

                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction
                        onClick={deleteCustomer}
                        variant="destructive"
                    >
                        Delete customer
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
