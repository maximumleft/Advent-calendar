<template>
  <div class="min-h-screen bg-blue-900 text-white">
    <nav v-if="initialized" class="bg-blue-800 border-b border-blue-700 shadow-xl">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
          <div class="flex items-center">
            <router-link to="/" class="flex-shrink-0 flex items-center group">
              <span class="text-3xl font-black text-white tracking-tighter group-hover:text-blue-400 transition">ADVENT</span>
              <span class="ml-1 text-blue-400 font-bold">Calendar</span>
            </router-link>
          </div>
          
          <div class="flex items-center space-x-6">
            <template v-if="isAuthenticated">
              <div class="flex items-center space-x-3">
                <div class="text-right hidden sm:block">
                  <p class="text-sm font-black text-white leading-none mb-1">{{ user.name }}</p>
                  <p class="text-[10px] text-blue-400 font-bold uppercase tracking-widest">{{ user.email }}</p>
                </div>
                <div class="h-10 w-10 rounded-2xl bg-blue-600 flex items-center justify-center text-sm font-black shadow-lg shadow-blue-500/20 uppercase">
                  {{ user.name[0] }}
                </div>
              </div>
              <button 
                @click="logout" 
                class="bg-white/5 hover:bg-white/10 text-white/60 hover:text-white px-4 py-2 rounded-xl text-sm font-bold transition border border-white/5"
              >
                Выйти
              </button>
            </template>
            <template v-else>
              <router-link to="/login" class="text-sm font-bold text-white/60 hover:text-white transition">Вход</router-link>
              <router-link to="/register" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-black shadow-lg shadow-blue-500/20 transition">Регистрация</router-link>
            </template>
            <a href="/admin" class="text-white/40 hover:text-white transition p-2" title="Админка">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </nav>

    <main v-if="initialized" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <router-view></router-view>
    </main>
    
    <div v-else class="min-h-screen flex items-center justify-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
    </div>
    
    <footer v-if="initialized" class="bg-blue-950/50 py-12 mt-12 border-t border-white/5">
      <div class="max-w-7xl mx-auto px-4 text-center text-white/20 text-xs font-bold uppercase tracking-widest">
        &copy; 2026 Advent Calendar App • Happy Holidays
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuth } from './composables/useAuth';

const { user, isAuthenticated, initialized, init, logout } = useAuth();

onMounted(init);
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
body {
  font-family: 'Inter', sans-serif;
  -webkit-font-smoothing: antialiased;
}
</style>
