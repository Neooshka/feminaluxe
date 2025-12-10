<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from "ziggy-js";
const isMenuOpen = ref(false);
</script>

<template>
  <div class="min-h-screen bg-beige-100 flex flex-col">  <!-- Custom color via var -->
    <header class="bg-primary text-white shadow-md">
      <nav class="container mx-auto px-4 py-4 flex justify-between items-center">
        <Link :href="'/'" class="text-2xl font-bold">Feminaluxe</Link>
        <button @click="isMenuOpen = !isMenuOpen" class="md:hidden">
          <span class="sr-only">Toggle menu</span> <!-- ARIA -->
          <!-- Icon: bars -->
        </button>
        <ul class="hidden md:flex space-x-6">
          <li><Link :href="route('services.index')" class="hover:text-accent">Услуги</Link></li>
          <li><Link :href="route('portfolio')" class="hover:text-accent">Портфолио</Link></li>
          <li><Link :href="route('about')" class="hover:text-accent">О нас</Link></li>
          <li><Link :href="route('blog.index')" class="hover:text-accent">Блог</Link></li>
          <li><Link :href="route('contact')" class="hover:text-accent">Контакты</Link></li>
        </ul>
      </nav>
      <ul v-if="isMenuOpen" class="md:hidden flex flex-col space-y-4 px-4 py-2 bg-primary">
        <!-- Mobile menu items -->
      </ul>
    </header>

    <main class="flex-grow container mx-auto px-4 py-8">
      <slot />  <!-- Page content here -->
    </main>

    <footer class="bg-primary text-white py-4">
      <div class="container mx-auto px-4 text-center">
        &copy; 2025 Feminaluxe. Все права защищены.
      </div>
    </footer>
  </div>
</template>

<style>
:root {
  --color-primary: #FFB6C1;
  --color-accent: #D4AF37;
  --color-beige: #F5F5DC;
}
.bg-primary { background-color: var(--color-primary); }
.text-accent { color: var(--color-accent); }
.bg-beige-100 { background-color: var(--color-beige); }
/* Etc. for Tailwind v4 compatibility */
</style>
