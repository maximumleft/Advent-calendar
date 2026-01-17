<template>
  <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-6 overflow-hidden">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-xl font-bold">Календарь событий</h2>
      <button 
        @click="showModal = true"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition"
      >
        + Добавить
      </button>
    </div>

    <div class="calendar-container">
      <FullCalendar :options="calendarOptions" />
    </div>

    <!-- Модалка создания (упрощенная) -->
    <div v-if="showModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-blue-900 border border-white/20 rounded-2xl p-6 w-full max-w-md shadow-2xl">
        <h3 class="text-xl font-bold mb-4 text-white">Новое событие</h3>
        <form @submit.prevent="handleCreate" class="space-y-4">
          <input v-model="form.title" type="text" placeholder="Название" required class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-2 outline-none focus:border-blue-500">
          <input v-model="form.event_date" type="date" required class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-2 outline-none">
          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="showModal = false" class="text-white/60 hover:text-white">Отмена</button>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-xl font-bold">Создать</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import { useEvents } from '../../composables/useEvents';
import { eventService } from '../../services/api';
import { useRouter } from 'vue-router';

const props = defineProps(['groupId']);
const { events, fetchEvents } = useEvents();
const showModal = ref(false);
const router = useRouter();

const form = ref({ title: '', event_date: '' });

const calendarOptions = computed(() => ({
  plugins: [dayGridPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
  events: events.value.map(e => ({
    id: e.id,
    title: e.title,
    start: e.event_date,
    color: e.color || '#3b82f6'
  })),
  eventClick: (info) => router.push(`/events/${info.event.id}`),
  locale: 'ru'
}));

onMounted(() => fetchEvents(props.groupId));

const handleCreate = async () => {
  try {
    await eventService.create(props.groupId, form.value);
    showModal.value = false;
    form.value = { title: '', event_date: '' };
    fetchEvents(props.groupId);
  } catch (err) {
    console.error(err);
  }
};
</script>

<style>
.fc { --fc-border-color: rgba(255,255,255,0.1); --fc-page-bg-color: transparent; }
.fc .fc-toolbar-title { font-size: 1.1rem; font-weight: 800; color: white; }
.fc .fc-button-primary { background: rgba(255,255,255,0.1); border: none; border-radius: 8px; }
.fc .fc-daygrid-day-number { color: rgba(255,255,255,0.6); font-size: 0.8rem; }
.fc .fc-col-header-cell-cushion { color: rgba(255,255,255,0.4); text-transform: uppercase; font-size: 0.7rem; font-weight: 800; }
</style>
