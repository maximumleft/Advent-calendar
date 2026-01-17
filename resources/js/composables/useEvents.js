import { ref } from 'vue';
import { eventService } from '../services/api';

export function useEvents() {
    const events = ref([]);
    const loading = ref(false);

    const fetchEvents = async (groupId) => {
        loading.value = true;
        try {
            const response = await eventService.getByGroup(groupId);
            events.value = response.data;
        } catch (err) {
            console.error(err);
        } finally {
            loading.value = false;
        }
    };

    return { events, loading, fetchEvents };
}
