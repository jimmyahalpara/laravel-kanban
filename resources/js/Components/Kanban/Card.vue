<script setup>
import { computed, nextTick, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { PencilIcon } from '@heroicons/vue/24/solid';
import { TrashIcon } from '@heroicons/vue/24/solid';
import { useEditCard } from '@/Composables/useEditCard';
import ConfirmDialog from '@/Components/Kanban/ConfirmDialog.vue';

const props = defineProps({
  card: Object,
});

// TODO: Move to composable useModal
const isOpen = ref(false);
const closeModal = confirm => {
  isOpen.value = false;
  if (confirm) {
    router.delete(
      route('columns.cards.destroy', {
        card: props?.card?.id,
        column: props?.card?.column,
      })
    );
  }
};

const changeStatus = () => {
  router.put(
    route('columns.cards.update', {
      column: props?.card?.column,
      card: props?.card?.id,
    }),
    {
        is_completed: !props.card.is_completed,
    }
  );
};
const openModal = () => (isOpen.value = true);

const form = useForm({
  content: props?.card?.content,
  title: props?.card?.title,
  deadline: props?.card?.deadline,
});

const inputCardContentRef = ref();
const inputCardTitleRef = ref();
const isEditing = computed(
  () => props?.card?.id === useEditCard?.value?.currentCard
);

const cardContent = computed(() => props.card?.content);

const onSubmit = () => {
  form.put(
    route('columns.cards.update', {
      column: props?.card?.column,
      card: props?.card?.id,
    }),
    {
      onSuccess: () => {
        useEditCard.value.currentCard = null;
      },
    }
  );
};

const onCancel = () => {
  useEditCard.value.currentCard = null;
  form.reset();
};

const showForm = async () => {
  useEditCard.value.currentCard = props?.card?.id;
  await nextTick(); // wait for form to be rendered
  inputCardTitleRef.value.focus();
};

const greyborder = computed(() => {
  // return true if card.is_completed is not true and card.deadline more than today
  return !props.card.is_completed && new Date(props.card.deadline) > new Date();
});
// green border 
// return true if card.is_completed is true 
const greenborder = computed(() => {
  return props.card.is_completed;
});
// red border
// return true if card.is_completed is not true and card.deadline is today
const redborder = computed(() => {
  console.log('new Date(props.card.deadline).toDateString() :>> ', new Date(props.card.deadline).toDateString());
  return !props.card.is_completed && (new Date(props.card.deadline) <= new Date() || new Date(props.card.deadline).toDateString() === new Date().toDateString());
});
</script>

<template>
  <div
    :class="[
      'cursor-move relative group p-2 bg-white shadow rounded-md border hover:bg-gray-50 animated-border',
      {
        'border-gray-300' : greyborder,
        'border-green-500' : greenborder,
        'border-red-animation' : redborder,
        'border-2' : greenborder || redborder,
        'border-b' : greyborder && !greenborder && !redborder,
      }
    ]"
  >
    <form v-if="isEditing" @keydown.esc="onCancel" @submit.prevent="onSubmit">
      <input 
        type="text" 
        v-model="form.title" 
        placeholder="Card title ..." 
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        ref="inputCardTitleRef"
      />
      <textarea
        v-model="form.content"
        type="text"
        @keydown.enter.prevent="onSubmit"
        placeholder="Card content ..."
        ref="inputCardContentRef"
        rows="3"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
      >
      </textarea>
      <!-- deadline datetime -->
      <label for="deadline" class="block text-sm font-medium text-gray-700 mt-2">
        Deadline
      </label>
      <input
        type="datetime-local"
        v-model="form.deadline"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
      />
      <div class="mt-2 space-x-2">
        <button
          type="submit"
          class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
          Save card
        </button>
        <button
          @click.prevent="onCancel"
          type="button"
          class="inline-flex items-center rounded-md border border-transparent px-4 py-2 text-sm font-medium text-gray-700 hover:text-black focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
          Cancel
        </button>
      </div>
    </form>
    <div v-else>
      <b>
        {{ props.card.title }}
      </b>
      <p class="text-sm">{{ cardContent }}</p>
      <!-- deadline in human readable format using moment js -->
      <small v-if="props.card.deadline" class="text-xs text-red-800 font-bold">
        {{  $filters.humanFormatDate(new Date(props.card.deadline)) }}
      </small>
      <div
        class="hidden absolute right-1 inset-0 group-hover:flex justify-end space-x-2 items-center"
      >
        <button
          @click.prevent="showForm"
          class="w-8 h-8 bg-gray-50 text-gray-600 hover:text-black hover:bg-gray-200 rounded-md grid place-content-center"
        >
          <PencilIcon class="w-5 h-5" />
        </button>
        <button
          @click.prevent="changeStatus"
          class="w-8 h-8 bg-gray-50 text-green-600 hover:text-green-700 hover:bg-gray-200 rounded-md grid place-content-center"
          v-if="!props.card.is_completed"
        >
          <svg height="20px" width="20px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1792 1792" xml:space="preserve" fill="#00b32d" stroke="#00b32d"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <circle cx="896" cy="896" r="782.9"></circle> <path class="st0" d="M1322.7,592.9c-40.6-40.6-106.3-40.6-146.9,0l-455.7,456L549.4,878.6c-40.6-40.6-106.3-40.6-146.9,0 c-40.6,40.6-40.6,106.3,0,146.9l233.6,232.4c3.1,4.1,6.5,8.1,10.2,11.8c20.5,20.5,47.5,30.6,74.4,30.4c26.8,0.2,53.7-9.9,74.2-30.4 c3.8-3.8,7.3-7.9,10.4-12.1l517.4-517.8C1363.3,699.3,1363.3,633.5,1322.7,592.9z"></path> </g></svg>
        </button>
        <button
          @click.prevent="changeStatus"
          class="w-8 h-8 bg-gray-50 text-red-600 hover:text-red-700 hover:bg-gray-200 rounded-md grid place-content-center"
          v-else
        >
          <svg fill="rgb(220, 38, 38)" width="20px" height="20px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" id="cross-circle" class="icon glyph" stroke="rgb(220, 38, 38)" stroke-width="0.00024000000000000003"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M12,2A10,10,0,1,0,22,12,10,10,0,0,0,12,2Zm3.71,12.29a1,1,0,0,1,0,1.42,1,1,0,0,1-1.42,0L12,13.42,9.71,15.71a1,1,0,0,1-1.42,0,1,1,0,0,1,0-1.42L10.58,12,8.29,9.71A1,1,0,0,1,9.71,8.29L12,10.58l2.29-2.29a1,1,0,0,1,1.42,1.42L13.42,12Z"></path></g></svg>
        </button>
        <button
          @click.prevent="openModal"
          class="w-8 h-8 bg-gray-50 text-red-600 hover:text-red-700 hover:bg-gray-200 rounded-md grid place-content-center"
        >
          <TrashIcon class="w-5 h-5" />
        </button>
      </div>
    </div>
  </div>
  <ConfirmDialog
    :show="isOpen"
    @confirm="closeModal($event)"
    title="Remove Card"
    message="Are you sure you want to delete this card?"
  />
</template>

<style>
@keyframes animate-border-width {
  0% {
    border-color: #d1d5db;
    background-color: #f3f4f6;
  }
  50% {
    border-color: #ef4444;
    background-color: #ffd8d8;
  }
  100% {
    border-color: #d1d5db;
    background-color: #f3f4f6;
  }
}

/* for div with class animated-border.border-red-500 */
.border-red-animation {
  animation: animate-border-width 2s infinite;
}

.st0{fill:#ffffff;} 
</style>
