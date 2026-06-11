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
                meta: { role: 'kd' },
            },
            {
                path: '/events',
                name: 'EventsKK',
                component: () => import('../pages/events/KKIndex.vue'),
                meta: { role: 'kk' },
            },
            {
                path: '/events/:id',
                name: 'EventDetail',
                component: () => import('../pages/events/Detail.vue'),
                meta: { role: 'kd' },
            },
            {
                path: '/events/:id',
                name: 'EventDetailKK',
                component: () => import('../pages/events/KKDetail.vue'),
                meta: { role: 'kk' },
            },

            {
                path: '/social-assistances',
                name: 'SocialAssistances',
                component: () => import('../pages/social-assistances/Index.vue'),
                meta: { role: 'kd' },
            },
            {
                path: '/social-assistances',
                name: 'SocialAssistancesKK',
                component: () => import('../pages/social-assistances/KKIndex.vue'),
                meta: { role: 'kk' },
            },
            {
                path: '/social-assistances/detail/:id',
                name: 'SocialAssistanceKKApply',
                component: () => import('../pages/social-assistances/KKApply.vue'),
                meta: { role: 'kk' },
            },
            {
                path: '/social-assistances/my-recipients',
                name: 'SocialAssistanceKKRecipients',
                component: () => import('../pages/social-assistances/KKRecipients.vue'),
                meta: { role: 'kk' },
            },
            {
                path: '/social-assistances/my-recipients/:id',
                name: 'SocialAssistanceKKRecipientDetail',
                component: () => import('../pages/social-assistances/KKRecipientDetail.vue'),
                meta: { role: 'kk' },
            },
            {
                path: '/social-assistances/:id',
                name: 'SocialAssistanceDetail',
                component: () => import('../pages/social-assistances/Detail.vue'),
                meta: { role: 'kd' },
            },
            {
                path: '/social-assistances/recipients',
                name: 'SocialAssistanceRecipients',
                component: () => import('../pages/social-assistances/Recipients.vue'),
                meta: { role: 'kd' },
            },
            {
                path: '/developments',
                name: 'Developments',
                component: () => import('../pages/developments/Index.vue'),
                meta: { role: 'kd' },
            },
            {
                path: '/developments',
                name: 'DevelopmentsKK',
                component: () => import('../pages/developments/KKIndex.vue'),
                meta: { role: 'kk' },
            },
            {
                path: '/developments/:id',
                name: 'DevelopmentDetail',
                component: () => import('../pages/developments/Detail.vue'),
                meta: { role: 'kd' },
            },
            {
                path: '/developments/:id',
                name: 'DevelopmentDetailKK',
                component: () => import('../pages/developments/KKDetail.vue'),
                meta: { role: 'kk' },
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

    // Role-based bansos routing
    if (to.name === 'SocialAssistances' && auth.userRole === 'head_of_family') {
        return next({ name: 'SocialAssistancesKK' });
    }
    if (to.name === 'SocialAssistancesKK' && auth.userRole !== 'head_of_family') {
        return next({ name: 'SocialAssistances' });
    }

    // Role-based development/event routing
    if (to.name === 'Developments' && auth.userRole === 'head_of_family') {
        return next({ name: 'DevelopmentsKK' });
    }
    if (to.name === 'DevelopmentsKK' && auth.userRole !== 'head_of_family') {
        return next({ name: 'Developments' });
    }
    if (to.name === 'DevelopmentDetail' && auth.userRole === 'head_of_family') {
        return next({ name: 'DevelopmentDetailKK' });
    }
    if (to.name === 'DevelopmentDetailKK' && auth.userRole !== 'head_of_family') {
        return next({ name: 'DevelopmentDetail' });
    }
    if (to.name === 'Events' && auth.userRole === 'head_of_family') {
        return next({ name: 'EventsKK' });
    }
    if (to.name === 'EventsKK' && auth.userRole !== 'head_of_family') {
        return next({ name: 'Events' });
    }
    if (to.name === 'EventDetail' && auth.userRole === 'head_of_family') {
        return next({ name: 'EventDetailKK' });
    }
    if (to.name === 'EventDetailKK' && auth.userRole !== 'head_of_family') {
        return next({ name: 'EventDetail' });
    }

    // Specific role guard
    if (to.meta.role === 'admin' && auth.userRole !== 'admin') {
        return next('/');
    }
    if (to.meta.role === 'kk' && auth.userRole !== 'head_of_family') {
        return next('/');
    }
    if (to.meta.role === 'kd' && auth.userRole === 'head_of_family') {
        return next({ name: 'SocialAssistancesKK' });
    }

    next();
});
