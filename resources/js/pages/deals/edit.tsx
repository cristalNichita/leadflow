import { Head } from '@inertiajs/react';
import { DealForm } from '@/components/deals/deal-form';
import { index } from '@/routes/deals';
import type { Deal, SelectOption } from '@/types';

type Props = {
    deal: Deal;
    customers: SelectOption[];
    users: SelectOption[];
    canManageDeal: boolean;
};

export default function EditDeal({
    deal,
    customers,
    users,
    canManageDeal,
}: Props) {
    return (
        <>
            <Head title={`Edit ${deal.title}`} />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div>
                    <p className="text-sm text-muted-foreground">CRM / Deals</p>

                    <h1 className="mt-1 text-2xl font-semibold tracking-tight">
                        Edit deal
                    </h1>

                    <p className="mt-1 text-sm text-muted-foreground">
                        Update{' '}
                        <span className="font-medium text-foreground">
                            {deal.title}
                        </span>
                        .
                    </p>
                </div>

                <div className="max-w-5xl">
                    <DealForm
                        deal={deal}
                        customers={customers}
                        users={users}
                        canManageDeal={canManageDeal}
                    />
                </div>
            </div>
        </>
    );
}

EditDeal.layout = {
    breadcrumbs: [
        {
            title: 'Deals',
            href: index(),
        },
    ],
};
