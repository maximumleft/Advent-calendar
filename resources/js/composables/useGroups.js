import { ref } from 'vue';
import { groupService } from '../services/api';

export function useGroups() {
    const groups = ref([]);
    const currentGroup = ref(null);
    const loading = ref(false);
    const error = ref(null);

    const fetchGroups = async () => {
        loading.value = true;
        try {
            const response = await groupService.getAll();
            groups.value = response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch groups';
        } finally {
            loading.value = false;
        }
    };

    const fetchGroup = async (id) => {
        loading.value = true;
        try {
            const response = await groupService.getById(id);
            currentGroup.value = response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch group';
        } finally {
            loading.value = false;
        }
    };

    const createGroup = async (data) => {
        loading.value = true;
        try {
            const response = await groupService.create(data);
            groups.value.push(response.data);
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to create group';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return { groups, currentGroup, loading, error, fetchGroups, fetchGroup, createGroup };
}
