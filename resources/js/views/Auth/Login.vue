<template>
  <div class="max-w-md mx-auto mt-12 bg-white/5 border border-white/10 rounded-3xl p-8 backdrop-blur-xl shadow-2xl">
    <h2 class="text-3xl font-black mb-6 text-center text-white">Вход</h2>
    
    <form @submit.prevent="handleLogin" class="space-y-4">
      <div>
        <label class="block text-sm font-bold text-white/60 mb-1">Email</label>
        <input 
          v-model="form.email" 
          type="email" 
          required 
          class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white outline-none focus:border-blue-500 transition"
        >
      </div>
      
      <div>
        <label class="block text-sm font-bold text-white/60 mb-1">Пароль</label>
        <input 
          v-model="form.password" 
          type="password" 
          required 
          class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white outline-none focus:border-blue-500 transition"
        >
      </div>

      <div v-if="error" class="text-red-400 text-sm bg-red-400/10 border border-red-400/20 rounded-xl p-3">
        {{ error }}
      </div>

      <button 
        type="submit" 
        :disabled="loading"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-blue-500/20 transition disabled:opacity-50"
      >
        {{ loading ? 'Загрузка...' : 'Войти' }}
      </button>
    </form>

    <p class="mt-6 text-center text-white/40 text-sm">
      Нет аккаунта? 
      <router-link to="/register" class="text-blue-400 font-bold hover:underline">Зарегистрироваться</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuth } from '../../composables/useAuth';

const { login, loading } = useAuth();
const form = ref({ email: '', password: '' });
const error = ref('');

const handleLogin = async () => {
  error.value = '';
  try {
    await login(form.value);
  } catch (err) {
    error.value = err;
  }
};
</script>
