<template>
  <div v-if="event" class="max-w-3xl mx-auto space-y-8">
    <div class="bg-white/5 border border-white/10 rounded-3xl overflow-hidden shadow-2xl">
      <div :style="{ backgroundColor: event.color }" class="h-4 w-full"></div>
      <div class="p-8">
        <div class="flex flex-col md:flex-row justify-between items-start gap-6 mb-8">
          <div class="space-y-2">
            <span class="text-xs font-bold text-blue-400 uppercase tracking-widest">{{ event.group?.name }}</span>
            <h1 class="text-4xl font-black">{{ event.title }}</h1>
            <div class="flex items-center space-x-2 text-white/60">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span class="text-sm font-bold">{{ formatDate(event.event_date) }} {{ event.event_time }}</span>
            </div>
          </div>
          
          <div class="bg-white/5 border border-white/10 rounded-2xl p-4 w-full md:w-auto">
            <p class="text-xs font-bold opacity-40 uppercase mb-3">Ваше участие</p>
            <div class="flex gap-2">
              <button @click="handleStatus('confirmed')" :class="myStatus === 'confirmed' ? 'bg-green-600' : 'bg-white/10'" class="px-6 py-2 rounded-xl text-sm font-bold transition">Да</button>
              <button @click="handleStatus('declined')" :class="myStatus === 'declined' ? 'bg-red-600' : 'bg-white/10'" class="px-6 py-2 rounded-xl text-sm font-bold transition">Нет</button>
            </div>
          </div>
        </div>

        <div class="prose prose-invert max-w-none">
          <p class="text-white/80 leading-relaxed">{{ event.description || 'Описания нет' }}</p>
        </div>

        <EventComments :event-id="event.id" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { eventService } from '../../services/api';
import EventComments from '../../components/Events/EventComments.vue';
import moment from 'moment';

const route = useRoute();
const event = ref(null);
const myStatus = ref(null);

const fetchEvent = async () => {
  try {
    const res = await eventService.getById(route.params.id);
    event.value = res.data;
    // Mock user id
    const me = event.value.participants?.find(p => p.user_id === 1);
    if (me) myStatus.value = me.status;
  } catch (err) { console.error(err); }
};

const handleStatus = async (status) => {
  try {
    await eventService.participate(event.value.id, status);
    myStatus.value = status;
    fetchEvent();
  } catch (err) { console.error(err); }
};

const formatDate = (d) => moment(d).format('DD MMMM YYYY');
onMounted(fetchEvent);
</script>
