<script setup>
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import Column from '@/Components/Kanban/Column.vue';
import ColumnCreate from '@/Components/Kanban/ColumnCreate.vue';
import EditableText from '@/Components/EditableText.vue';
import CategoryList from '@/Components/CategoryList.vue';

const props = defineProps({
  board: Object,
});

const columns = computed(() => props.board?.data?.columns);
const boardTitle = computed(() => props.board?.data?.title);

const columnsWithOrder = ref([]);

const confirmDelete = ref(false);

const onReorderChange = column => {
  columnsWithOrder.value?.push(column);
};

const confirmDeleteHandler = () => {
  if (confirmDelete.value) {
    router.delete(route('boards.destroy', { board: props.board.data.id }));
  }
  confirmDelete.value = true;
};

const onReorderCommit = () => {
  if (!columnsWithOrder?.value?.length) {
    return;
  }

  router.put(route('cards.reorder'), {
    columns: columnsWithOrder.value,
  });
};
</script>

<template>
  <Head>
    <title>Kanban Board</title>
  </Head>

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-start items-center">
        <h2 class="font-black text-2xl text-gray-800 leading-tight">
          <EditableText 
            :value="boardTitle" 
            @textinput="router.post(route('boards.update.title', {
            board: board.data.id,
          }), { title: $event })"/>
        </h2>
        <!-- delete button -->
        <button
          class="px-4 py-2 bg-red-500 text-white rounded-md mx-4"
          @click="confirmDeleteHandler()"
        >
          {{ confirmDelete ? 'Are you sure?' : 'Delete' }}
        </button>
        <!-- cancel button -->
        <button
          v-if="confirmDelete"
          class="px-4 py-2 bg-gray-500 text-white rounded-md"
          @click="confirmDelete = false"
        >
          Cancel
        </button>
        <CategoryList :board="board" />
      </div>
    </template>

    <div class="flex-1 flex flex-col h-full overflow-hidden">
      <div class="flex-1 h-full overflow-x-auto">
        <div
          class="inline-flex h-full items-start p-4 space-x-4 overflow-hidden"
        >
          <Column
            v-for="column in columns"
            :key="column.title"
            :column="column"
            @reorder-change="onReorderChange"
            @reorder-commit="onReorderCommit"
          />
          <div class="w-72">
            <ColumnCreate :board="board.data" />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
