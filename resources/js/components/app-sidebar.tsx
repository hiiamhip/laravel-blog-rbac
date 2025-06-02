import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { BookOpen, BookOpenText, FolderPlus, Lock, NotebookPen } from 'lucide-react';
import { useMemo } from 'react';
import AppLogo from './app-logo';

const adminNavItems: NavItem[] = [
    {
        title: 'Edit users blogs',
        href: '/admin/edit',
        icon: NotebookPen,
    },
    {
        title: 'Categories',
        href: '/admin/categories',
        icon: FolderPlus,
    },
];

const userNavItems: NavItem[] = [
    {
        title: 'Blogs',
        href: '/posts',
        icon: BookOpen,
    },
    {
        title: 'My blogs',
        href: '/posts/create',
        icon: BookOpenText,
    },
];

export function AppSidebar() {
    const page = usePage<PageProps>();
    const user = page.props.auth.user;
    const url = page.url;

    const isAdminPage = useMemo(() => {
        return url.startsWith('/admin/');
    }, [url]);

    const mainNavItems: NavItem[] = user.role === 'admin' && isAdminPage ? adminNavItems : userNavItems;

    const footerNavItems: NavItem[] = useMemo(() => {
        if (user.role === 'admin' && isAdminPage) {
            return [
                {
                    title: 'Back to Blogs',
                    href: '/posts/create',
                    icon: BookOpen,
                },
            ];
        }
        if (user.role === 'admin') {
            return [
                {
                    title: 'Admin Dashboard',
                    href: '/admin/categories',
                    icon: Lock,
                },
            ];
        }
    }, [user.role, isAdminPage]);

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={user.role === 'admin' ? footerNavItems : []} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
