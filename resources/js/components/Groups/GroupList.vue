<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold">Мои группы</h2>
      <button 
        @click="showCreateModal = true"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition shadow-lg shadow-blue-500/30 font-bold"
      >
        Создать группу
      </button>
    </div>

    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
    </div>

    <div v-else-if="groups.length === 0" class="text-center py-12 bg-white/5 rounded-xl border-2 border-dashed border-white/10">
      <p class="text-gray-300">У вас пока нет групп. Создайте свою или присоединитесь к существующей!</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="group in groups" 
        :key="group.id"
        class="bg-white/10 backdrop-blur-md rounded-xl shadow-xl border border-white/10 overflow-hidden hover:bg-white/20 transition cursor-pointer"
        @click="$router.push(`/groups/${group.id}`)"
      >
        <div :style="{ backgroundColor: group.color || '#3b82f6' }" class="h-2"></div>
        <div class="p-6">
          <h3 class="font-bold text-xl mb-2 text-white">{{ group.name }}</h3>
          <p class="text-gray-300 text-sm line-clamp-2 mb-4">{{ group.description || 'Нет описания' }}</p>
          <div class="flex items-center text-xs text-gray-400 space-x-4">
            <span>Владелец: {{ group.owner?.name }}</span>
            <span v-if="group.is_public" class="bg-green-500/20 text-green-400 px-2 py-0.5 rounded border border-green-500/30">Публичная</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Group Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-blue-900 border border-white/20 rounded-2xl w-full max-w-md p-6 shadow-2xl">
        <h3 class="text-xl font-bold mb-4 text-white">Создать новую группу</h3>
        <form @submit.prevent="handleCreate">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1 text-white/60">Название</label>
              <input v-model="form.name" type="text" required class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-2 text-white outline-none focus:border-blue-500 transition">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-white/60">Описание</label>
              <textarea v-model="form.description" class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-2 text-white outline-none focus:border-blue-500 transition" rows="3"></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-white/60">Цвет группы</label>
              <input v-model="form.color" type="color" class="w-full h-10 bg-white/10 border border-white/20 rounded-xl p-1">
            </div>
            <div class="flex items-center">
              <input v-model="form.is_public" type="checkbox" id="is_public" class="mr-2 rounded border-white/20 bg-white/10 text-blue-600">
              <label for="is_public" class="text-sm text-white/80">Сделать группу публичной</label>
            </div>
          </div>
          <div class="flex justify-end space-x-3 mt-6">
            <button 
              type="button" 
              @click="showCreateModal = false"
              class="px-4 py-2 text-white/60 hover:text-white transition"
            >
              Отмена
            </button>
            <button 
              type="submit"
              class="px-6 py-2 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition"
              :disabled="loading"
            >
              Создать
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useGroups } from '../../composables/useGroups';

const { groups, loading, fetchGroups, createGroup } = useGroups();
const showCreateModal = ref(false);

const form = ref({
  name: '',
  description: '',
  color: '#3b82f6',
  is_public: false
});

onMounted(fetchGroups);

const handleCreate = async () => {
  try {
    await createGroup(form.value);
    showCreateModal.value = false;
    form.value = { name: '', description: '', color: '#3b82f6', is_public: false };
    await fetchGroups(); // Refresh list
  } catch (err) {
    console.error(err);
  }
};
</script>
