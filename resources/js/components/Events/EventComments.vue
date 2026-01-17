<template>
  <div class="space-y-6 pt-8 border-t border-white/10">
    <h3 class="text-lg font-bold">Комментарии</h3>
    
    <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
      <div v-for="c in comments" :key="c.id" class="flex space-x-3">
        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-[10px] font-bold shrink-0 uppercase">
          {{ c.user.name.charAt(0) }}
        </div>
        <div class="bg-white/5 rounded-2xl px-4 py-2 flex-1 border border-white/5">
          <div class="flex justify-between items-baseline mb-1">
            <span class="text-xs font-bold text-blue-400">{{ c.user.name }}</span>
            <span class="text-[10px] text-white/40">{{ formatDate(c.created_at) }}</span>
          </div>
          <p class="text-sm text-white/80">{{ c.comment }}</p>
        </div>
      </div>
    </div>

    <form @submit.prevent="handleSend" class="relative">
      <input 
        v-model="newComment" 
        type="text" 
        placeholder="Напишите что-нибудь..." 
        class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm outline-none focus:border-blue-500 transition pr-12"
      >
      <button 
        type="submit" 
        class="absolute right-2 top-2 p-2 text-blue-500 hover:text-blue-400"
        :disabled="!newComment.trim()"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
        </svg>
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { commentService } from '../../services/api';
import moment from 'moment';

const props = defineProps(['eventId']);
const comments = ref([]);
const newComment = ref('');

const fetchComments = async () => {
  try {
    const res = await commentService.getByEvent(props.eventId);
    comments.value = res.data;
  } catch (err) {
    console.error(err);
  }
};

const handleSend = async () => {
  if (!newComment.value.trim()) return;
  try {
    const res = await commentService.create(props.eventId, { comment: newComment.value });
    comments.value.push(res.data);
    newComment.value = '';
  } catch (err) {
    console.error(err);
  }
};

const formatDate = (d) => moment(d).fromNow();
onMounted(fetchComments);
</script>
