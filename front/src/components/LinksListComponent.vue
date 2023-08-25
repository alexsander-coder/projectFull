<template>
  <div class="links-list">
    <h2>Links Criados:</h2>
    <ul>
      <li v-for="link in links" :key="link.id">
        {{ link.nome_ficticio }}: {{ link.short_link }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const links = ref([]);

onMounted(async () => {
  await fetchLinks();
});

const fetchLinks = async () => {
  try {
    const response = await fetch('http://localhost:8000/api');
    if (response.ok) {
      const responseData = await response.json();
      links.value = responseData.data;
    } else {
      console.error('Erro ao buscar links');
    }
  } catch (error) {
    console.error('Erro na requisição:', error);
  }
};

</script>

<style scoped>
.links-list {
  margin-top: 20px;
}
</style>
