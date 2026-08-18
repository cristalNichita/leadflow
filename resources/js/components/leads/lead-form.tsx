import { Form, Link } from '@inertiajs/react';
import { CircleDollarSign, Save, Target } from 'lucide-react';
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
import { index, show, store, update } from '@/routes/leads';
import type { Lead, SelectOption } from '@/types';

type Props = {
    lead?: Lead;
    customers: SelectOption[];
    users: SelectOption[];
    canManageLead?: boolean;
};

export function LeadForm({
    lead,
    customers,
    users,
    canManageLead = true,
}: Props) {
    const editing = lead !== undefined;

    const action = editing ? update(lead.id) : store();

    const cancelHref = editing ? show(lead.id) : index();

    return (
        <Form action={action} className="space-y-6">
            {({ errors, processing }) => (
                <>
                    <Card>
                        <CardHeader>
                            <CardTitle>Lead information</CardTitle>

                            <CardDescription>
                                {canManageLead
                                    ? 'Opportunity, customer and assignment details.'
                                    : 'You can update the status and notes of this assigned lead.'}
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="grid gap-6 md:grid-cols-2">
                            {canManageLead ? (
                                <>
                                    <div className="grid gap-2 md:col-span-2">
                                        <Label htmlFor="title">Title</Label>

                                        <div className="relative">
                                            <Target className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                                            <Input
                                                id="title"
                                                name="title"
                                                defaultValue={lead?.title ?? ''}
                                                placeholder="Website redesign project"
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
                                                lead?.customer_id ?? ''
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
                                                lead?.assigned_user_id ?? ''
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
                                        <Label htmlFor="estimated_value">
                                            Estimated value
                                        </Label>

                                        <div className="relative">
                                            <CircleDollarSign className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                                            <Input
                                                id="estimated_value"
                                                name="estimated_value"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                defaultValue={
                                                    lead?.estimated_value ?? ''
                                                }
                                                placeholder="5000"
                                                className="pl-9"
                                                required
                                            />
                                        </div>

                                        <InputError
                                            message={errors.estimated_value}
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="source">Source</Label>

                                        <Input
                                            id="source"
                                            name="source"
                                            defaultValue={lead?.source ?? ''}
                                            placeholder="Referral, Website, LinkedIn..."
                                        />

                                        <InputError message={errors.source} />
                                    </div>
                                </>
                            ) : (
                                <>
                                    <input
                                        type="hidden"
                                        name="title"
                                        value={lead?.title ?? ''}
                                    />

                                    <input
                                        type="hidden"
                                        name="customer_id"
                                        value={lead?.customer_id ?? ''}
                                    />

                                    <input
                                        type="hidden"
                                        name="assigned_user_id"
                                        value={lead?.assigned_user_id ?? ''}
                                    />

                                    <input
                                        type="hidden"
                                        name="estimated_value"
                                        value={lead?.estimated_value ?? ''}
                                    />

                                    <input
                                        type="hidden"
                                        name="source"
                                        value={lead?.source ?? ''}
                                    />

                                    <div className="rounded-lg border bg-muted/40 p-4 md:col-span-2">
                                        <p className="font-medium">
                                            {lead?.title}
                                        </p>

                                        <p className="mt-1 text-sm text-muted-foreground">
                                            {lead?.customer.name}
                                            {lead?.customer.company
                                                ? ` · ${lead.customer.company}`
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
                                    defaultValue={lead?.status ?? 'new'}
                                    className="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                                >
                                    <option value="new">New</option>
                                    <option value="contacted">Contacted</option>
                                    <option value="qualified">Qualified</option>
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
                                Internal context and follow-up information for
                                this opportunity.
                            </CardDescription>
                        </CardHeader>

                        <CardContent>
                            <div className="grid gap-2">
                                <Label htmlFor="notes">Lead notes</Label>

                                <textarea
                                    id="notes"
                                    name="notes"
                                    rows={7}
                                    defaultValue={lead?.notes ?? ''}
                                    placeholder="Add notes about the opportunity..."
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
                                  : 'Create lead'}
                        </Button>
                    </div>
                </>
            )}
        </Form>
    );
}
