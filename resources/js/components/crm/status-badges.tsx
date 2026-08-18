import { Badge } from '@/components/ui/badge';
import type {
    CustomerStatus,
    DealStatus,
    LeadStatus,
    TaskPriority,
} from '@/types';

export function LeadStatusBadge({ status }: { status: LeadStatus }) {
    const labels: Record<LeadStatus, string> = {
        new: 'New',
        contacted: 'Contacted',
        qualified: 'Qualified',
        won: 'Won',
        lost: 'Lost',
    };

    const classes: Record<LeadStatus, string> = {
        new: 'bg-slate-500/15 text-slate-400 hover:bg-slate-500/15',
        contacted: 'bg-blue-500/15 text-blue-400 hover:bg-blue-500/15',
        qualified: 'bg-amber-500/15 text-amber-400 hover:bg-amber-500/15',
        won: 'bg-emerald-500/15 text-emerald-400 hover:bg-emerald-500/15',
        lost: 'bg-red-500/15 text-red-400 hover:bg-red-500/15',
    };

    return (
        <Badge variant="secondary" className={classes[status]}>
            {labels[status]}
        </Badge>
    );
}

export function DealStatusBadge({ status }: { status: DealStatus }) {
    const labels: Record<DealStatus, string> = {
        open: 'Open',
        won: 'Won',
        lost: 'Lost',
    };

    const classes: Record<DealStatus, string> = {
        open: 'bg-blue-500/15 text-blue-400 hover:bg-blue-500/15',
        won: 'bg-emerald-500/15 text-emerald-400 hover:bg-emerald-500/15',
        lost: 'bg-red-500/15 text-red-400 hover:bg-red-500/15',
    };

    return (
        <Badge variant="secondary" className={classes[status]}>
            {labels[status]}
        </Badge>
    );
}

export function TaskPriorityBadge({ priority }: { priority: TaskPriority }) {
    const labels: Record<TaskPriority, string> = {
        low: 'Low',
        medium: 'Medium',
        high: 'High',
    };

    const classes: Record<TaskPriority, string> = {
        low: 'bg-slate-500/15 text-slate-400 hover:bg-slate-500/15',
        medium: 'bg-amber-500/15 text-amber-400 hover:bg-amber-500/15',
        high: 'bg-red-500/15 text-red-400 hover:bg-red-500/15',
    };

    return (
        <Badge variant="secondary" className={classes[priority]}>
            {labels[priority]}
        </Badge>
    );
}

export function CustomerStatusBadge({ status }: { status: CustomerStatus }) {
    const labels: Record<CustomerStatus, string> = {
        active: 'Active',
        inactive: 'Inactive',
    };

    const classes: Record<CustomerStatus, string> = {
        active: 'bg-emerald-500/15 text-emerald-400 hover:bg-emerald-500/15',
        inactive: 'bg-slate-500/15 text-slate-400 hover:bg-slate-500/15',
    };

    return (
        <Badge variant="secondary" className={classes[status]}>
            {labels[status]}
        </Badge>
    );
}
