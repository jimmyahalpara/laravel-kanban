<script setup>
import { nextTick, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { PlusIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
  column: Object,
  parent: {
    type: Object,
    default: null,
    required: false,
  },
  openTrigger: {
    type: Number,
    default: 0,
    required: false,
  },
});

const emit = defineEmits(['created', 'clearParentId']);

const form = useForm({
  content: '',
  title: '',
  deadline: '',
  parent_id: props?.parent && props?.parent?.id ? props.parent.id : null,
  total_quantity: null,
});

// add watcher on props.parent_id 
watch(
  () => props.parent,
  (value) => {
    form.parent_id = value?.id ? value.id : null;
  }
);

// watch openTrigger
watch(
  () => props.openTrigger,
  () => {
    if (props.openTrigger) {
      showForm(false);
    }
  }
);

const inputCardContentRef = ref();
const inputCardTitleRef = ref();
const isCreating = ref(false);

const onSubmit = () => {
  form.post(route('columns.cards.store', { column: props?.column?.id }), {
    onSuccess: () => {
      form.reset();
      isCreating.value = false;
      emit('created');
    },
  });
};

const showForm = async (withoutParent = true) => {
  if (withoutParent) {
    emit('clearParentId');
  }
  isCreating.value = true;
  await nextTick(); // wait for form to be rendered
  inputCardTitleRef.value.focus();
};
</script>

<template>
  <div>
    <form v-if="isCreating" @keydown.esc="isCreating = false" @submit.prevent="onSubmit">
      <small v-if="props.parent && props.parent.title">
        Adding card to <strong>{{ props.parent.title }}</strong>
      </small>
      <input v-model="form.title" type="text" placeholder="Card title ..."
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        ref="inputCardTitleRef" />
      <textarea v-model="form.content" type="text" @keydown.enter.prevent="onSubmit" placeholder="Card content ..."
        ref="inputCardContentRef" rows="3"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
      </textarea>
      <input v-model="form.deadline" type="datetime-local"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
      <small @click="form.deadline = null" v-if="form.deadline" class="text-xs text-red-800 font-bold cursor-pointer">
        Clear
      </small>
      <label for="total_quantity" class="block text-sm font-medium text-gray-700 mt-2">
        Total Quantity
      </label>
      <input type="number" v-model="form.total_quantity" placeholder="Total Quantity ..."
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
      <!-- small on hover underline click clear total quantity to null -->
      <small @click="form.total_quantity = null" v-if="form.total_quantity"
        class="text-xs text-red-800 font-bold cursor-pointer">
        Clear
      </small>
      <div class="mt-2 space-x-2">
        <button type="submit"
          class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
          Add card
        </button>
        <button @click.prevent="isCreating = false; emit('clearParentId')" type="button"
          class="inline-flex items-center rounded-md border border-transparent px-4 py-2 text-sm font-medium text-gray-700 hover:text-black focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
          Cancel
        </button>
      </div>
    </form>
    <button v-if="!isCreating" @click.prevent="showForm" type="button"
      class="flex items-center p-2 text-sm rounded-md font-medium bg-gray-200 text-gray-600 hover:text-black hover:bg-gray-300 w-full">
      <PlusIcon class="w-5 h-5" />
      <span class="ml-1">Add card</span>
    </button>
  </div>
</template>
