import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: () => import('../pages/auth/Login.vue'),
        meta: { guest: true },
    },
    {
        path: '/',
        component: () => import('../layouts/AdminLayout.vue'),
        meta: { auth: true },
        children: [
            {
                path: '',
                name: 'Dashboard',
                component: () => import('../pages/dashboard/Index.vue'),
                meta: { role: 'kd' },
            },
            {
                path: '',
                name: 'DashboardKK',
                component: () => import('../pages/dashboard/KKIndex.vue'),
                meta: { role: 'kk' },
            },
            {
                path: '/users',
                name: 'Users',
                component: () => import('../pages/users/Index.vue'),
                meta: { role: 'admin' },
            },
            {
                path: '/family-members',
                name: 'FamilyMembersKK',
                component: () => import('../pages/family-members/KKIndex.vue'),
            },
            {
                path: '/head-families',
                name: 'HeadFamilies',
                component: () => import('../pages/head-families/Index.vue'),
            },
            {
                path: '/head-families/:id/members',
                name: 'FamilyMembers',
                component: () => import('../pages/family-members/Index.vue'),
            },
            {
                path: '/events',
                name: 'Events',
                component: () => import('../pages/events/Index.vue'),
            },
            {
                path: '/social-assistances',
                name: 'SocialAssistances',
                component: () => import('../pages/social-assistances/Index.vue'),
            },
            {
                path: '/social-assistances/:id',
                name: 'SocialAssistanceDetail',
                component: () => import('../pages/social-assistances/Detail.vue'),
            },
            {
                path: '/social-assistances/recipients',
                name: 'SocialAssistanceRecipients',
                component: () => import('../pages/social-assistances/Recipients.vue'),
            },
            {
                path: '/developments',
                name: 'Developments',
                component: () => import('../pages/developments/Index.vue'),
            },
            {
                path: '/developments/:id',
                name: 'DevelopmentDetail',
                component: () => import('../pages/developments/Detail.vue'),
            },
            {
                path: '/village-profile',
                name: 'VillageProfile',
                component: () => import('../pages/village-profile/Index.vue'),
            },
            {
                path: '/village-profile/create',
                name: 'VillageProfileCreate',
                component: () => import('../pages/village-profile/Create.vue'),
            },
            {
                path: '/village-profile/edit',
                name: 'VillageProfileEdit',
                component: () => import('../pages/village-profile/Edit.vue'),
            },
        ],
    },
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const auth = useAuthStore();

    if (to.meta.auth && !auth.isAuthenticated) {
        return next('/login');
    }

    if (to.meta.guest && auth.isAuthenticated) {
        return next('/');
    }

    // Role-based dashboard routing
    if (to.name === 'Dashboard' && auth.userRole === 'head_of_family') {
        return next({ name: 'DashboardKK' });
    }
    if (to.name === 'DashboardKK' && auth.userRole !== 'head_of_family') {
        return next({ name: 'Dashboard' });
    }

    // Specific role guard
    if (to.meta.role === 'admin' && auth.userRole !== 'admin') {
        return next('/');
    }

    next();
});
