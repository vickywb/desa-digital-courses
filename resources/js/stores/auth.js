import { defineStore } from 'pinia';
import client from '../api/client';
import { router } from '../router';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user') ?? 'null'),
        token: localStorage.getItem('token') ?? null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        userRole: (state) => state.user?.role ?? null,
    },

    actions: {
        async login(credentials) {
            const response = await client.post('/login', credentials);
            const { user, token } = response.data.data;

            this.user = user;
            this.token = token;

            localStorage.setItem('user', JSON.stringify(user));
            localStorage.setItem('token', token);

            return user;
        },

        async logout() {
            try {
                await client.post('/auth/logout');
            } finally {
                this.user = null;
                this.token = null;

                localStorage.removeItem('user');
                localStorage.removeItem('token');

                router.push('/login');
            }
        },

        async fetchMe() {
            const response = await client.get('/auth/me');
            this.user = response.data.data.user;

            localStorage.setItem('user', JSON.stringify(this.user));
        },
    },
});
