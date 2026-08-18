import { Form, Link } from '@inertiajs/react';
import { Building2, Mail, Phone, Save, UserRound } from 'lucide-react';
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
import { index, show, store, update } from '@/routes/customers';
import type { Customer } from '@/types';

type Props = {
    customer?: Customer;
};

export function CustomerForm({ customer }: Props) {
    const editing = customer !== undefined;

    const action = editing ? update(customer.id) : store();

    const cancelHref = editing ? show(customer.id) : index();

    return (
        <Form action={action} className="space-y-6">
            {({ errors, processing }) => (
                <>
                    <Card>
                        <CardHeader>
                            <CardTitle>Customer information</CardTitle>

                            <CardDescription>
                                Basic contact and company information for this
                                customer.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="grid gap-6 md:grid-cols-2">
                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>

                                <div className="relative">
                                    <UserRound className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                                    <Input
                                        id="name"
                                        name="name"
                                        defaultValue={customer?.name ?? ''}
                                        placeholder="Olivia Martin"
                                        className="pl-9"
                                        required
                                        autoFocus
                                    />
                                </div>

                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="company">Company</Label>

                                <div className="relative">
                                    <Building2 className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                                    <Input
                                        id="company"
                                        name="company"
                                        defaultValue={customer?.company ?? ''}
                                        placeholder="Acme Ltd"
                                        className="pl-9"
                                    />
                                </div>

                                <InputError message={errors.company} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Email</Label>

                                <div className="relative">
                                    <Mail className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                                    <Input
                                        id="email"
                                        name="email"
                                        type="email"
                                        defaultValue={customer?.email ?? ''}
                                        placeholder="olivia@acme.com"
                                        className="pl-9"
                                    />
                                </div>

                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="phone">Phone</Label>

                                <div className="relative">
                                    <Phone className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                                    <Input
                                        id="phone"
                                        name="phone"
                                        defaultValue={customer?.phone ?? ''}
                                        placeholder="+373 69 123 456"
                                        className="pl-9"
                                    />
                                </div>

                                <InputError message={errors.phone} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="status">Status</Label>

                                <select
                                    id="status"
                                    name="status"
                                    defaultValue={customer?.status ?? 'active'}
                                    className="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                                >
                                    <option value="active">Active</option>

                                    <option value="inactive">Inactive</option>
                                </select>

                                <InputError message={errors.status} />
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Notes</CardTitle>

                            <CardDescription>
                                Internal information about this customer or
                                relationship.
                            </CardDescription>
                        </CardHeader>

                        <CardContent>
                            <div className="grid gap-2">
                                <Label htmlFor="notes">Customer notes</Label>

                                <textarea
                                    id="notes"
                                    name="notes"
                                    rows={7}
                                    defaultValue={customer?.notes ?? ''}
                                    placeholder="Add notes about this customer..."
                                    className="min-h-36 w-full resize-y rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                                />

                                <InputError message={errors.notes} />
                            </div>
                        </CardContent>
                    </Card>

                    <div className="flex items-center justify-end gap-3">
                        <Button variant="outline" asChild>
                            <Link href={cancelHref}>Cancel</Link>
                        </Button>

                        <Button type="submit" disabled={processing}>
                            <Save />

                            {processing
                                ? 'Saving...'
                                : editing
                                  ? 'Save changes'
                                  : 'Create customer'}
                        </Button>
                    </div>
                </>
            )}
        </Form>
    );
}
