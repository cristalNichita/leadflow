export type CustomerStatus = 'active' | 'inactive';

export type LeadStatus = 'new' | 'contacted' | 'qualified' | 'won' | 'lost';

export type Customer = {
    id: number;
    name: string;
    company: string | null;
    email: string | null;
    phone: string | null;
    status: CustomerStatus;
    notes: string | null;
    leads_count?: number;
    deals_count?: number;
    tasks_count?: number;
    created_at: string;
    updated_at: string;
};

export type SelectOption = {
    id: number;
    name: string;
};

export type LeadCustomer = {
    id: number;
    name: string;
    company: string | null;
};

export type LeadAssignedUser = {
    id: number;
    name: string;
    email: string;
};

export type Lead = {
    id: number;
    title: string;
    customer_id: number;
    customer: LeadCustomer;
    assigned_user_id: number | null;
    assigned_user: LeadAssignedUser | null;
    estimated_value: string;
    source: string | null;
    status: LeadStatus;
    notes: string | null;
    created_at: string;
    updated_at: string;
};

export type PaginationLinks = {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
};

export type PaginationMeta = {
    current_page: number;
    from: number | null;
    last_page: number;
    path: string;
    per_page: number;
    to: number | null;
    total: number;
};

export type PaginatedResource<T> = {
    data: T[];
    links: PaginationLinks;
    meta: PaginationMeta;
};
