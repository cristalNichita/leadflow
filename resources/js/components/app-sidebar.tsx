import { Link } from '@inertiajs/react';
import {
    LayoutDashboardIcon,
    UsersRound,
    Target,
    Handshake,
    ListTodo,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as customersIndex } from '@/routes/customers';
import { index as dealsIndex } from '@/routes/deals';
import { index as leadsIndex } from '@/routes/leads';
import { index as tasksIndex } from '@/routes/tasks';
import type { NavItem } from '@/types';

const workspaceItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutDashboardIcon,
    },
];

const crmItems: NavItem[] = [
    {
        title: 'Customers',
        href: customersIndex(),
        icon: UsersRound,
    },
    {
        title: 'Leads',
        href: leadsIndex(),
        icon: Target,
    },
    {
        title: 'Deals',
        href: dealsIndex(),
        icon: Handshake,
    },
    {
        title: 'Tasks',
        href: tasksIndex(),
        icon: ListTodo,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain label="Workspace" items={workspaceItems} />

                <NavMain label="CRM" items={crmItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
