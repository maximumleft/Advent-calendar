import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from './composables/useAuth';

import GroupList from './components/Groups/GroupList.vue';
import GroupShow from './views/Groups/Show.vue';
import EventShow from './views/Events/Show.vue';
import Login from './views/Auth/Login.vue';
import Register from './views/Auth/Register.vue';

const routes = [
    { path: '/', name: 'home', component: GroupList, meta: { requiresAuth: true } },
    { path: '/groups/:id', name: 'group.show', component: GroupShow, meta: { requiresAuth: true } },
    { path: '/events/:id', name: 'event.show', component: EventShow, meta: { requiresAuth: true } },
    { path: '/login', name: 'login', component: Login, meta: { guestOnly: true } },
    { path: '/register', name: 'register', component: Register, meta: { guestOnly: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const { isAuthenticated, initialized, init } = useAuth();
    
    if (!initialized.value) {
        await init();
    }

    if (to.meta.requiresAuth && !isAuthenticated.value) {
        return next('/login');
    }

    if (to.meta.guestOnly && isAuthenticated.value) {
        return next('/');
    }

    next();
});

export default router;
