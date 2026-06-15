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
        path: '/403',
        name: 'Forbidden',
        component: () => import('../pages/errors/Forbidden.vue'),
    },
    {
        path: '/',
        redirect: '/login',
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/login',
    },
    {
        path: '/staff',
        component: () => import('../layouts/StaffLayout.vue'),
        children: [
            {
                path: '',
                redirect: 'dashboard',
            },
            {
                path: 'dashboard',
                name: 'Dashboard',
                component: () => import('../pages/dashboard/Index.vue'),
            },
            {
                path: 'users',
                name: 'Users',
                component: () => import('../pages/users/Index.vue'),
                meta: { role: 'admin' },
            },
            {
                path: 'head-families',
                name: 'HeadFamilies',
                component: () => import('../pages/head-families/Index.vue'),
            },
            {
                path: 'head-families/:id',
                name: 'HeadFamilyDetail',
                component: () => import('../pages/head-families/StaffDetail.vue'),
            },
            {
                path: 'head-families/:id/members',
                name: 'FamilyMembers',
                component: () => import('../pages/family-members/Index.vue'),
            },
            {
                path: 'head-families/:headId/members/:memberId',
                name: 'FamilyMemberDetail',
                component: () => import('../pages/family-members/StaffDetail.vue'),
            },
            {
                path: 'events',
                name: 'Events',
                component: () => import('../pages/events/Index.vue'),
            },
            {
                path: 'events/:id',
                name: 'EventDetail',
                component: () => import('../pages/events/Detail.vue'),
            },
            {
                path: 'social-assistances',
                name: 'SocialAssistances',
                component: () => import('../pages/social-assistances/Index.vue'),
            },
            {
                path: 'social-assistances/recipients',
                name: 'SocialAssistanceRecipients',
                component: () => import('../pages/social-assistances/Recipients.vue'),
            },
            {
                path: 'social-assistances/:id',
                name: 'SocialAssistanceDetail',
                component: () => import('../pages/social-assistances/Detail.vue'),
            },
            {
                path: 'developments',
                name: 'Developments',
                component: () => import('../pages/developments/Index.vue'),
            },
            {
                path: 'developments/:id',
                name: 'DevelopmentDetail',
                component: () => import('../pages/developments/Detail.vue'),
            },
            {
                path: 'village-profile',
                name: 'VillageProfile',
                component: () => import('../pages/village-profile/Index.vue'),
            },
            {
                path: 'village-profile/create',
                name: 'VillageProfileCreate',
                component: () => import('../pages/village-profile/Create.vue'),
            },
            {
                path: 'village-profile/edit',
                name: 'VillageProfileEdit',
                component: () => import('../pages/village-profile/Edit.vue'),
            },
            {
                path: 'kas',
                name: 'Kas',
                component: () => import('../pages/kas/Index.vue'),
            },
            {
                path: 'kas/edit',
                name: 'KasEdit',
                component: () => import('../pages/kas/Edit.vue'),
            },
        ],
    },
    {
        path: '/warga',
        component: () => import('../layouts/CitizenLayout.vue'),
        children: [
            {
                path: '',
                redirect: 'dashboard',
            },
            {
                path: 'dashboard',
                name: 'DashboardKK',
                component: () => import('../pages/dashboard/KKIndex.vue'),
            },
            {
                path: 'family-members',
                name: 'FamilyMembersKK',
                component: () => import('../pages/family-members/KKIndex.vue'),
            },
            {
                path: 'family-members/:id',
                name: 'FamilyMemberDetailKK',
                component: () => import('../pages/family-members/KKDetail.vue'),
            },
            {
                path: 'bansos',
                name: 'SocialAssistancesKK',
                component: () => import('../pages/social-assistances/KKIndex.vue'),
            },
            {
                path: 'bansos/detail/:id',
                name: 'SocialAssistanceKKApply',
                component: () => import('../pages/social-assistances/KKApply.vue'),
            },
            {
                path: 'bansos/pengajuan-saya',
                name: 'SocialAssistanceKKRecipients',
                component: () => import('../pages/social-assistances/KKRecipients.vue'),
            },
            {
                path: 'bansos/pengajuan-saya/:id',
                name: 'SocialAssistanceKKRecipientDetail',
                component: () => import('../pages/social-assistances/KKRecipientDetail.vue'),
            },
            {
                path: 'pembangunan',
                name: 'DevelopmentsKK',
                component: () => import('../pages/developments/KKIndex.vue'),
            },
            {
                path: 'pembangunan/:id',
                name: 'DevelopmentDetailKK',
                component: () => import('../pages/developments/KKDetail.vue'),
            },
            {
                path: 'events',
                name: 'EventsKK',
                component: () => import('../pages/events/KKIndex.vue'),
            },
            {
                path: 'events/:id',
                name: 'EventDetailKK',
                component: () => import('../pages/events/KKDetail.vue'),
            },
            {
                path: 'village-profile',
                name: 'VillageProfileKK',
                component: () => import('../pages/village-profile/Index.vue'),
            },
            {
                path: 'kas',
                name: 'KasKK',
                component: () => import('../pages/kas/Index.vue'),
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

    if (!auth.isAuthenticated && !to.meta.guest) {
        return next('/login');
    }

    if (auth.isAuthenticated && to.meta.guest) {
        const target = auth.userRole === 'head_of_family' ? '/warga/dashboard' : '/staff/dashboard';
        return next(target);
    }

    if (to.path.startsWith('/staff/') && auth.userRole === 'head_of_family') {
        return next('/403');
    }

    if (to.path.startsWith('/warga/') && auth.userRole !== 'head_of_family') {
        return next('/403');
    }

    if (to.name === 'Users' && auth.userRole !== 'admin') {
        return next('/403');
    }

    next();
});
