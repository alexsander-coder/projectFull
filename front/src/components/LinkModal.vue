<template>
  <div class="container">
    <div v-if="linkData" class="link-details">
      <button class="close-ribbon" @click="closeModal">X</button>
      <h2>Detalhes do Link</h2>
      <p><strong>ID:</strong> {{ linkData.link.id }}</p>
      <p><strong>Link Original:</strong> {{ linkData.link.original_link }}</p>
      <p><strong>Short Link:</strong> {{ linkData.link.short_link }}</p>
      <p><strong>Clicks:</strong> {{ linkData.link.clicks }}</p>
      <p><strong>Nome Fictício:</strong> {{ linkData.link.nome_ficticio }}</p>
      <p><strong>Criado em:</strong> {{ linkData.link.created_at }}</p>
      <p><strong>Atualizado em:</strong> {{ linkData.link.updated_at }}</p>
      <h3>Acessos</h3>
      <ul>
        <li v-for="access in linkData.accesses" :key="access.id">
          <strong>ID:</strong> {{ access.id }}<br>
          <strong>IP:</strong> {{ access.ip }}<br>
          <strong>User Agent:</strong> {{ access.user_agent }}<br>
          <strong>Criado em:</strong> {{ access.created_at }}<br>
          <strong>Atualizado em:</strong> {{ access.updated_at }}
        </li>
      </ul>

      <!-- <button class="close-ribbon" @click="closeModal">Fechar</button> -->
    </div>
  </div>
</template>

<script>
export default {
  props: {
    linkData: {
      type: Object,
      required: true
    }
  },
  methods: {
    closeModal() {
      this.$emit('close');
    }
  }
};
</script>

<style scoped>
.close-ribbon {
  margin: -4.5rem 0 0 28rem;
  position: absolute;
  cursor: pointer;
}

.container {
  display: flex;
  justify-content: center;
  margin-top: -35rem;
  max-height: 545px;
  z-index: 1;
}

.link-details::-webkit-scrollbar {
  display: none;
}

.link-details {
  padding: 4rem;
  border-radius: 10px;
  max-width: 25rem;
  overflow: auto;
  opacity: 0.9;
  background-color: rgb(187, 187, 187);
  color: black;
}
</style>