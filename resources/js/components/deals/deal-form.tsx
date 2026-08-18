import { Form, Link } from '@inertiajs/react';
import { CalendarDays, CircleDollarSign, Handshake, Save } from 'lucide-react';
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
import { index, show, store, update } from '@/routes/deals';
import type { Deal, SelectOption } from '@/types';

type Props = {
    deal?: Deal;
    customers: SelectOption[];
    users: SelectOption[];
    canManageDeal?: boolean;
};

export function DealForm({
    deal,
    customers,
    users,
    canManageDeal = true,
}: Props) {
    const editing = deal !== undefined;

    const action = editing ? update(deal.id) : store();

    const cancelHref = editing ? show(deal.id) : index();

    return (
        <Form action={action} className="space-y-6">
            {({ errors, processing }) => (
                <>
                    <Card>
                        <CardHeader>
                            <CardTitle>Deal information</CardTitle>

                            <CardDescription>
                                {canManageDeal
                                    ? 'Commercial details, ownership and expected closing information.'
                                    : 'You can update the status and notes of this assigned deal.'}
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="grid gap-6 md:grid-cols-2">
                            {canManageDeal ? (
                                <>
                                    <div className="grid gap-2 md:col-span-2">
                                        <Label htmlFor="title">Title</Label>

                                        <div className="relative">
                                            <Handshake className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                                            <Input
                                                id="title"
                                                name="title"
                                                defaultValue={deal?.title ?? ''}
                                                placeholder="Annual support contract"
                                                className="pl-9"
                                                required
                                                autoFocus
                                            />
                                        </div>

                                        <InputError message={errors.title} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="customer_id">
                                            Customer
                                        </Label>

                                        <select
                                            id="customer_id"
                                            name="customer_id"
                                            defaultValue={
                                                deal?.customer_id ?? ''
                                            }
                                            className="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                                            required
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

                                    <div className="grid gap-2">
                                        <Label htmlFor="assigned_user_id">
                                            Assigned to
                                        </Label>

                                        <select
                                            id="assigned_user_id"
                                            name="assigned_user_id"
                                            defaultValue={
                                                deal?.assigned_user_id ?? ''
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
                                        <Label htmlFor="value">
                                            Deal value
                                        </Label>

                                        <div className="relative">
                                            <CircleDollarSign className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                                            <Input
                                                id="value"
                                                name="value"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                defaultValue={deal?.value ?? ''}
                                                placeholder="12500"
                                                className="pl-9"
                                                required
                                            />
                                        </div>

                                        <InputError message={errors.value} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="expected_close_date">
                                            Expected close
                                        </Label>

                                        <div className="relative">
                                            <CalendarDays className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                                            <Input
                                                id="expected_close_date"
                                                name="expected_close_date"
                                                type="date"
                                                defaultValue={
                                                    deal?.expected_close_date ??
                                                    ''
                                                }
                                                className="pl-9"
                                            />
                                        </div>

                                        <InputError
                                            message={errors.expected_close_date}
                                        />
                                    </div>
                                </>
                            ) : (
                                <>
                                    <input
                                        type="hidden"
                                        name="title"
                                        value={deal?.title ?? ''}
                                    />

                                    <input
                                        type="hidden"
                                        name="customer_id"
                                        value={deal?.customer_id ?? ''}
                                    />

                                    <input
                                        type="hidden"
                                        name="assigned_user_id"
                                        value={deal?.assigned_user_id ?? ''}
                                    />

                                    <input
                                        type="hidden"
                                        name="value"
                                        value={deal?.value ?? ''}
                                    />

                                    <input
                                        type="hidden"
                                        name="expected_close_date"
                                        value={deal?.expected_close_date ?? ''}
                                    />

                                    <div className="rounded-lg border bg-muted/40 p-4 md:col-span-2">
                                        <p className="font-medium">
                                            {deal?.title}
                                        </p>

                                        <p className="mt-1 text-sm text-muted-foreground">
                                            {deal?.customer.name}

                                            {deal?.customer.company
                                                ? ` · ${deal.customer.company}`
                                                : ''}
                                        </p>
                                    </div>
                                </>
                            )}

                            <div className="grid gap-2">
                                <Label htmlFor="status">Status</Label>

                                <select
                                    id="status"
                                    name="status"
                                    defaultValue={deal?.status ?? 'open'}
                                    className="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                                >
                                    <option value="open">Open</option>

                                    <option value="won">Won</option>

                                    <option value="lost">Lost</option>
                                </select>

                                <InputError message={errors.status} />
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Notes</CardTitle>

                            <CardDescription>
                                Internal context, negotiation details and
                                follow-up information.
                            </CardDescription>
                        </CardHeader>

                        <CardContent>
                            <div className="grid gap-2">
                                <Label htmlFor="notes">Deal notes</Label>

                                <textarea
                                    id="notes"
                                    name="notes"
                                    rows={7}
                                    defaultValue={deal?.notes ?? ''}
                                    placeholder="Add notes about this deal..."
                                    className="min-h-36 w-full resize-y rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                                />

                                <InputError message={errors.notes} />
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
                                  : 'Create deal'}
                        </Button>
                    </div>
                </>
            )}
        </Form>
    );
}
