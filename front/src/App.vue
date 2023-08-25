<template>
  <div class="header">
    <div class="input-group">
      <label for="input1">URL a ser encurtada</label>
      <input id="input1" v-model="input1" placeholder="URL a ser encurtada"
        :class="{ 'rounded-input': true, 'focused': input1Focused }" @focus="input1Focused = true"
        @blur="input1Focused = false" />
    </div>

    <div class="input-group">
      <label for="input2">Identificador da URL</label>
      <input id="input2" v-model="input2" placeholder="Nome para identificar a URL"
        :class="{ 'rounded-input': true, 'focused': input2Focused }" @focus="input2Focused = true"
        @blur="input2Focused = false" />
    </div>

    <div class="input-group">
      <label for="input3">SLUG</label>
      <input id="input3" style="max-width: 4rem;" v-model="input3" placeholder="SLUG"
        :class="{ 'rounded-input': true, 'focused': input3Focused }" @focus="input3Focused = true"
        @blur="input3Focused = false" />
    </div>

    <button @click="handleButtonClick">{{ editingLink ? 'Editar' : 'Encurtar' }}</button>
  </div>

  <div style="text-align: center;">
    <p v-if="successMessage" class="success-message">{{ successMessage }}</p>
    <p v-if="deleteSuccessMessage" class="success-message-delete">{{ deleteSuccessMessage }}</p>
  </div>
  <div style="text-align: center; margin-top: 4%; color: black;">Insira seu dominio</div>
  <div class="domain">
    <input v-model="domain" placeholder="Domínio" :class="{ 'rounded-input': true, 'focused': input2Focused }" />
  </div>
  <h2 style="text-align: center; margin-top: 5%; color: rgb(81, 81, 81);">LINKS CRIADOS</h2>
  <div class="links-list">
    <div>
      <div v-for="link in links" :key="link.id">
        <div class="linksEdit">
          <div>
            <span style="font-weight: 600;">Nome de identificação URL</span><br>
            {{ link.nome_ficticio }}
          </div>
          <div style="padding: 0 4.5rem 0 4.5rem;">
            URL Encurtada<br>
            {{ domain }}/api/{{ link.short_link }}
            <img class="linkCopy" src="./components/icons/link.svg" alt="link"
              @click="copyUrl(`${domain}/api/${link.short_link}`)">
          </div>
          <div>
            Clicks<br>
            {{ link.clicks }}
          </div>
          <div style="margin-left: 2rem;">
            <img class="editar" src="./components/icons/edit.svg" alt="Editar" @click="editLink(link)">
            <img class="visualizar" src="./components/icons/icons8-lupa.svg" alt="Visualizar" @click="viewLink(link.id)">
            <img class="excluir" src="./components/icons/icons8-trash.svg" alt="Excluir" @click="deleteLink(link.id)">
          </div>
        </div>
      </div>
    </div>
  </div>

  <LinkModal v-if="selectedLink" :linkData="selectedLink" @close="selectedLink = null" />
  <!-- <div v-if="selectedLink" class="link-details">
    <h2>Detalhes do Link</h2>
    <p><strong>ID:</strong> {{ selectedLink.link.id }}</p>
    <p><strong>Link Original:</strong> {{ selectedLink.link.original_link }}</p>
    <p><strong>Short Link:</strong> {{ selectedLink.link.short_link }}</p>
    <p><strong>Clicks:</strong> {{ selectedLink.link.clicks }}</p>
    <p><strong>Nome Fictício:</strong> {{ selectedLink.link.nome_ficticio }}</p>
    <p><strong>Criado em:</strong> {{ selectedLink.link.created_at }}</p>
    <p><strong>Atualizado em:</strong> {{ selectedLink.link.updated_at }}</p>
    <h3>Acessos</h3>
    <ul>
      <li v-for="access in selectedLink.accesses" :key="access.id">
        <strong>ID:</strong> {{ access.id }}<br>
        <strong>IP:</strong> {{ access.ip }}<br>
        <strong>User Agent:</strong> {{ access.user_agent }}<br>
        <strong>Criado em:</strong> {{ access.created_at }}<br>
        <strong>Atualizado em:</strong> {{ access.updated_at }}
      </li>
    </ul>
  </div> -->
</template>

<script setup>
import { ref, onMounted } from 'vue';
import LinkModal from './components/LinkModal.vue';

const input1 = ref('');
const input2 = ref('');
const input3 = ref('');

const input1Focused = ref(false);
const input2Focused = ref(false);
const input3Focused = ref(false);

const domain = ref('');
const successMessage = ref('');
const deleteSuccessMessage = ref('');
const links = ref([]);
const editingLink = ref(null);
const selectedLink = ref(null);

onMounted(async () => {
  await fetchLinks();
});

const handleButtonClick = async () => {
  const linkData = {
    original_link: input1.value,
    nome_ficticio: input2.value,
    short_link: input3.value
  };

  try {
    let response;

    if (editingLink.value) {
      console.log(editingLink.value.id, 'o qe vem no iD')
      // Se estiver editando, faça uma requisição PUT para o endpoint de edição
      response = await fetch(`http://localhost:8000/api/links/${editingLink.value.id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(linkData)
      });
    } else {
      // Caso contrário, faça uma requisição POST para o endpoint de criação
      response = await fetch('http://localhost:8000/api/links/store', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(linkData)
      });
    }

    if (response.ok) {
      successMessage.value = `Link ${editingLink.value ? 'editado' : 'encurtado'} com sucesso!`;
      setTimeout(() => {
        successMessage.value = '';
      }, 3500);
      await fetchLinks();
      clearInputs();
      editingLink.value = null;
    } else {
      console.log('error fetch store')
    }
  } catch (error) {
    console.error('Erro na requisição:', error);
  }
};

const editLink = (link) => {
  input1.value = link.original_link;
  // input2.value = link.nome_ficticio;
  input3.value = link.short_link;
  editingLink.value = link;
};


const fetchLinks = async () => {
  try {
    const response = await fetch('http://localhost:8000/api');
    if (response.ok) {
      const responseData = await response.json();
      links.value = responseData.data;

      console.log(responseData.data, 'eae fih de deusi')
    } else {
      console.error('Erro ao buscar links');
    }
  } catch (error) {
    console.error('Erro na requisição:', error);
  }
};

const deleteLink = async (linkId) => {
  try {
    const response = await fetch(`http://localhost:8000/api/links/${linkId}`, {
      method: 'DELETE'
    });

    if (response.ok) {
      await fetchLinks();
      deleteSuccessMessage.value = 'Deletado com sucesso!';
      setTimeout(() => {
        deleteSuccessMessage.value = '';
      }, 3500);
    } else {
      console.log('error ao deletar')
    }
  } catch (error) {
    console.error('Erro na requisição:', error);
  }
};

const viewLink = async (link) => {
  try {
    const response = await fetch(`http://localhost:8000/api/links/${link}`);
    if (response.ok) {
      const responseData = await response.json();
      selectedLink.value = responseData;
    } else {
      console.error('Erro ao buscar detalhes do link');
    }
  } catch (error) {
    console.error('Erro na requisição:', error);
  }
};

const copyUrl = async (url) => {
  try {
    await navigator.clipboard.writeText(url);
    // corrigir uma exibicao de msg
  } catch (error) {
    console.error('Erro ao copiar URL:', error);
  }
};


const clearInputs = () => {
  input1.value = '';
  input2.value = '';
  input3.value = '';
};
</script>


<style scoped>
.header {
  display: flex;
  /* flex-direction: column; */
  justify-content: space-evenly;
  align-items: center;
  gap: 1rem;
}

.input-group {
  display: flex;
  flex-direction: column;
  color: black;
}

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

.linkCopy {
  margin-left: 9px;
  cursor: pointer;
}

.editar {
  padding: 4.5px 3px 4.5px 3px;
  cursor: pointer;
}

.excluir {
  padding: 2px 4px 3px 2px;
  cursor: pointer;
  border-radius: 5px;
  background-color: rgb(255, 81, 81);
}

.visualizar {
  padding: 2px 4px 3px 2px;
  cursor: pointer;
  border-radius: 5px;
  /* background-color: #3498db; */
  margin: 0 4px 0 4px;
}

.success-message {
  color: green;
  margin-top: 10px;
}

.links-list {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.linksEdit {
  display: flex;
  padding: 2rem;
  margin: 15px;
  border-radius: 8px;
  color: black;
  background-color: rgb(255, 255, 255);
}

.domain {
  display: flex;
  justify-content: center;
}

.success-message-delete {
  color: red;
}
</style>