import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    withCredentials: true,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    }
});

export const authService = {
    csrf: () => axios.get('/sanctum/csrf-cookie'),
    register: (data) => api.post('/register', data),
    login: (data) => api.post('/login', data),
    logout: () => api.post('/logout'),
    me: () => api.get('/user'),
};

export const groupService = {
    getAll: () => api.get('/groups'),
    getById: (id) => api.get(`/groups/${id}`),
    create: (data) => api.post('/groups', data),
    join: (id) => api.post(`/groups/${id}/join`),
    subscribe: (id) => api.post(`/groups/${id}/subscribe`),
    unsubscribe: (id) => api.post(`/groups/${id}/unsubscribe`),
};

export const eventService = {
    getByGroup: (groupId) => api.get(`/groups/${groupId}/events`),
    getById: (id) => api.get(`/events/${id}`),
    create: (groupId, data) => api.post(`/groups/${groupId}/events`, data),
    participate: (id, status) => api.post(`/events/${id}/participate`, { status }),
};

export const commentService = {
    getByEvent: (eventId) => api.get(`/events/${eventId}/comments`),
    create: (eventId, data) => api.post(`/events/${eventId}/comments`, data),
};

export const notificationService = {
    send: (groupId, data) => api.post(`/groups/${groupId}/notifications`, data),
};

export default api;
