<template>
  <div class="input-component">
    <input v-model="input1" placeholder="LINK" :class="{ 'rounded-input': true, 'focused': input1Focused }"
      @focus="input1Focused = true" @blur="input1Focused = false" />
    <input v-model="input2" placeholder="Nome para identificar a URL"
      :class="{ 'rounded-input': true, 'focused': input2Focused }" @focus="input2Focused = true"
      @blur="input2Focused = false" />
    <input style="max-width: 4rem;" v-model="input3" placeholder="SLUG"
      :class="{ 'rounded-input': true, 'focused': input3Focused }" @focus="input3Focused = true"
      @blur="input3Focused = false" />
    <button @click="handleButtonClick">Encurtar</button>
    <p v-if="successMessage" class="success-message">{{ successMessage }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const input1 = ref('');
const input2 = ref('');
const input3 = ref('');

const input1Focused = ref(false);
const input2Focused = ref(false);
const input3Focused = ref(false);

const successMessage = ref('');


const handleButtonClick = async () => {
  const linkData = {
    original_link: input1.value,
    short_link: input3.value,
    nome_ficticio: input2.value
  };

  try {
    const response = await fetch('http://localhost:8000/api/links/store', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(linkData)
    });

    if (response.ok) {
      // successMessage.value = 'Link encurtado com sucesso!';
      // await LinksListComponent.fetchLinks();
      // // await fetchLinks();
    } else {
    }
  } catch (error) {
    console.error('Erro na requisição:', error);
  }

};
</script>

<style scoped>
.rounded-input {
  border: 1px solid #ccc;
  border-radius: 20px;
  padding: 8px;
  margin-right: 10px;
  transition: border-color 0.3s ease-in-out;
}

.rounded-input.focused {
  border-color: #3498db;
}

.header button {
  border: none;
  background-color: #3498db;
  color: white;
  border-radius: 20px;
  padding: 8px 15px;
  cursor: pointer;
}

.success-message {
  color: green;
  margin-top: 10px;
}
</style>
