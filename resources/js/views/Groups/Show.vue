<template>
  <div v-if="currentGroup" class="space-y-8">
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 flex flex-col md:flex-row justify-between items-center gap-6 shadow-2xl">
      <div class="flex items-center space-x-6">
        <div :style="{ backgroundColor: currentGroup.color }" class="w-4 h-16 rounded-full shadow-lg"></div>
        <div>
          <h1 class="text-4xl font-black">{{ currentGroup.name }}</h1>
          <p class="text-white/60">{{ currentGroup.description || 'Нет описания' }}</p>
        </div>
      </div>
      <div class="flex space-x-3">
        <button @click="showNotifModal = true" class="bg-purple-600 hover:bg-purple-700 px-6 py-2.5 rounded-xl font-bold transition shadow-lg shadow-purple-500/20">
          📢 Рассылка
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2">
        <CalendarView :group-id="currentGroup.id" />
      </div>
      <div class="space-y-6">
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
          <h3 class="font-bold mb-4 flex justify-between">Участники <span class="opacity-40 text-xs">{{ currentGroup.members?.length }}</span></h3>
          <div class="space-y-3">
            <div v-for="m in currentGroup.members" :key="m.id" class="flex items-center space-x-3 text-sm">
              <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center font-bold">{{ m.user.name[0] }}</div>
              <span>{{ m.user.name }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Рассылка -->
    <div v-if="showNotifModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-blue-900 border border-white/20 rounded-3xl p-8 w-full max-w-lg shadow-2xl">
        <h3 class="text-2xl font-bold mb-6">Создать рассылку</h3>
        <form @submit.prevent="handleSendNotif" class="space-y-4">
          <input v-model="notifForm.subject" type="text" placeholder="Тема письма" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 outline-none focus:border-purple-500">
          <textarea v-model="notifForm.message" placeholder="Текст сообщения..." required rows="4" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 outline-none focus:border-purple-500"></textarea>
          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="showNotifModal = false" class="text-white/60">Отмена</button>
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 px-8 py-3 rounded-2xl font-bold transition">Отправить друзьям 🚀</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useGroups } from '../../composables/useGroups';
import { notificationService } from '../../services/api';
import CalendarView from '../../components/Events/CalendarView.vue';

const route = useRoute();
const { currentGroup, fetchGroup } = useGroups();
const showNotifModal = ref(false);
const notifForm = ref({ subject: '', message: '' });

onMounted(() => fetchGroup(route.params.id));

const handleSendNotif = async () => {
  try {
    await notificationService.send(currentGroup.value.id, notifForm.value);
    showNotifModal.value = false;
    alert('Письма отправлены всем подписчикам!');
  } catch (err) { console.error(err); }
};
</script>
