<script setup>
import { computed, nextTick, ref, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { PencilIcon } from '@heroicons/vue/24/solid';
import { TrashIcon } from '@heroicons/vue/24/solid';
import { useEditCard } from '@/Composables/useEditCard';
import ConfirmDialog from '@/Components/Kanban/ConfirmDialog.vue';
// import { usePage } from '@inertiajs/inertia-vue3';

const props = defineProps({
  card: Object,
  board: Object,
});

const emit = defineEmits(['moveToTop', 'addChild']);

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

const scrollToCard = (card) => {
  const cardElement = document.getElementById(`card-${card.id}-column-${card.column_id}`);
  cardElement.scrollIntoView({ behavior: 'smooth', block: 'center' });

  // add class golden-blink to cardElement
  cardElement.classList.add('golden-blink');
  // remove the class after 2 seconds
  setTimeout(() => {
    cardElement.classList.remove('golden-blink');
  }, 2000);
};

const form = useForm({
  content: props?.card?.content,
  title: props?.card?.title,
  deadline: props?.card?.deadline,
  total_quantity: props?.card?.total_quantity,
});

// if any of the props changes, update the form
watch(() => props.card, () => {
  form.content = props.card.content;
  form.title = props.card.title;
  form.deadline = props.card.deadline;
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


const card_categories = computed(() => {
  return usePage().props.board.data.card_categories;
});
// red border
// return true if card.is_completed is not true and card.deadline is today
const redborder = computed(() => {
  return props.card.deadline && !props.card.is_completed && (new Date(props.card.deadline) <= new Date() || new Date(props.card.deadline).toDateString() === new Date().toDateString());
});

const progressBarWidth = computed(() => {
  return props.card.completed_quantity ? `${(props.card.completed_quantity / props.card.total_quantity) * 100}%` : '0%';
});

const toggleCardCategory = function ()  {
  console.log('$page :>> ', card_categories);
  // iterate over card_categories and log 
  // find category_id in card_categories
  // if its null or 0, set it to first category_id
  debugger;
  if (props.card.card_category_id) {
    const index = card_categories.value.findIndex(category => category.id === props.card.card_category_id);
    if (index === card_categories.value.length - 1) {
      router.put(
        route('columns.cards.update', {
          column: props?.card?.column,
          card: props?.card?.id,
        }),
        {
          card_category_id: null,
        }
      );
    } else {
      router.put(
        route('columns.cards.update', {
          column: props?.card?.column,
          card: props?.card?.id,
        }),
        {
          card_category_id: card_categories.value[index + 1].id,
        }
      );
    }
  } else {
    router.put(
      route('columns.cards.update', {
        column: props?.card?.column,
        card: props?.card?.id,
      }),
      {
        card_category_id: card_categories.value[0].id,
      }
    );
  }
  
};
</script>

<template>
  <div :class="[
    'cursor-move relative p-2 bg-white shadow rounded-md border hover:bg-gray-50 animated-border overflow-hidden',
    {
      'border-gray-300': greyborder,
      'border-green-500': greenborder,
      'border-red-animation': redborder,
      'border-2': greenborder || redborder,
      'border-b': greyborder && !greenborder && !redborder,
    }
  ]" 
  :style="{
    color: props.card.card_category?.color,
    backgroundColor: props.card.card_category?.background_color,
  }"
  :id="`card-${props.card.id}-column-${props.card.column}`"
  
  >
    <form v-if="isEditing" @keydown.esc="onCancel" @submit.prevent="onSubmit">
      <input type="text" v-model="form.title" placeholder="Card title ..."
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        ref="inputCardTitleRef" />
      <textarea v-model="form.content" type="text" @keydown.enter.prevent="onSubmit" placeholder="Card content ..."
        ref="inputCardContentRef" rows="3"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
      </textarea>
      <!-- deadline datetime -->
      <label for="deadline" class="block text-sm font-medium text-gray-700 mt-2">
        Deadline
      </label>
      <input type="datetime-local" v-model="form.deadline"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
      <!-- small on hover underline click clear deadline to null -->
      <small @click="form.deadline = null" v-if="form.deadline" class="text-xs text-red-800 font-bold cursor-pointer">
        Clear
      </small>
      <!-- total quantity with clear option -->
      <label for="total_quantity" class="block text-sm font-medium text-gray-700 mt-2">
        Total Quantity
      </label>
      <input type="number" v-model="form.total_quantity" placeholder="Total Quantity ..."
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
      <!-- small on hover underline click clear total quantity to null -->
      <small @click="form.total_quantity = null" v-if="form.total_quantity" class="text-xs text-red-800 font-bold cursor-pointer">
        Clear
      </small>

      <div class="mt-2 space-x-2">
        <button type="submit"
          class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
          Save card
        </button>
        <button @click.prevent="onCancel" type="button"
          class="inline-flex items-center rounded-md border border-transparent px-4 py-2 text-sm font-medium text-gray-700 hover:text-black focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
          Cancel
        </button>
      </div>
    </form>
    <div v-else>
      <p>
        <span class="text-blue-700 text-xs hover:underline cursor-pointer" v-if="card.parent && card.parent.title"
          @click="scrollToCard(card.parent)">
          <strong>{{ card.parent.title }}</strong>
        </span>
      </p>
      <div class="group relative">
        <div class="pb-2">
          <b>
            {{ props.card.title }}
          </b>
          <span class="flex items-center" v-if="card.card_category">
            <span class="pr-1">
              <svg width="12px" height="12px" viewBox="0 0 33 33" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>tag 2</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage"> <g id="Icon-Set-Filled" sketch:type="MSLayerGroup" transform="translate(-362.000000, -776.000000)" fill="#000000"> <path d="M386.816,789.835 C385.257,791.396 382.728,791.396 381.168,789.835 C379.608,788.274 379.608,785.743 381.168,784.183 C382.728,782.621 385.257,782.621 386.816,784.183 C388.376,785.743 388.376,788.274 386.816,789.835 L386.816,789.835 Z M392.097,776.993 L378.683,776.993 C377.624,776.993 377.431,777.455 376.422,778.511 L366.437,788.466 L382.552,804.581 L392.563,794.602 C393.412,793.754 394.014,793.332 394.014,792.276 L394.014,778.903 C394.014,777.849 393.155,776.993 392.097,776.993 L392.097,776.993 Z M382.58,785.596 C381.8,786.376 381.8,787.642 382.58,788.422 C383.36,789.202 384.625,789.202 385.404,788.422 C386.185,787.642 386.185,786.376 385.404,785.596 C384.625,784.815 383.36,784.815 382.58,785.596 L382.58,785.596 Z M363.217,791.676 C361.596,793.291 361.596,795.911 363.217,797.526 L373.487,807.767 C375.108,809.382 377.735,809.382 379.356,807.767 L381.135,805.993 L365.02,789.878 L363.217,791.676 L363.217,791.676 Z" id="tag-2" sketch:type="MSShapeGroup"> </path> </g> </g> </g></svg>
            </span>
            <span class="text-xs">
              {{ card.card_category?.name }}
            </span>
          </span>
        </div>
        <p class="text-sm">{{ cardContent }}
        </p>
        <div class="hidden absolute right-1 inset-0 group-hover:flex justify-end items-center">
          <button v-if="card_categories && card_categories.length > 0" @click.prevent="toggleCardCategory" title="Change Category"
            class="w-8 h-8 bg-gray-50 text-gray-600 hover:text-black hover:bg-gray-200 rounded-md grid place-content-center">
            <svg width="20px" height="20px" viewBox="0 0 33 33" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>tag 2</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage"> <g id="Icon-Set-Filled" sketch:type="MSLayerGroup" transform="translate(-362.000000, -776.000000)" fill="#000000"> <path d="M386.816,789.835 C385.257,791.396 382.728,791.396 381.168,789.835 C379.608,788.274 379.608,785.743 381.168,784.183 C382.728,782.621 385.257,782.621 386.816,784.183 C388.376,785.743 388.376,788.274 386.816,789.835 L386.816,789.835 Z M392.097,776.993 L378.683,776.993 C377.624,776.993 377.431,777.455 376.422,778.511 L366.437,788.466 L382.552,804.581 L392.563,794.602 C393.412,793.754 394.014,793.332 394.014,792.276 L394.014,778.903 C394.014,777.849 393.155,776.993 392.097,776.993 L392.097,776.993 Z M382.58,785.596 C381.8,786.376 381.8,787.642 382.58,788.422 C383.36,789.202 384.625,789.202 385.404,788.422 C386.185,787.642 386.185,786.376 385.404,785.596 C384.625,784.815 383.36,784.815 382.58,785.596 L382.58,785.596 Z M363.217,791.676 C361.596,793.291 361.596,795.911 363.217,797.526 L373.487,807.767 C375.108,809.382 377.735,809.382 379.356,807.767 L381.135,805.993 L365.02,789.878 L363.217,791.676 L363.217,791.676 Z" sketch:type="MSShapeGroup"> </path> </g> </g> </g></svg>
          </button>
          <button @click.prevent="emit('addChild', props.card);"
            title="Add Child"
            class="w-8 h-8 bg-gray-50 text-gray-600 hover:text-black hover:bg-gray-200 rounded-md grid place-content-center">
            <svg fill="#000000" width="20px" height="20px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
              <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
              <g id="SVGRepo_iconCarrier">
                <path
                  d="M20,9H16a1,1,0,0,0-1,1v1H7V7H8A1,1,0,0,0,9,6V2A1,1,0,0,0,8,1H4A1,1,0,0,0,3,2V6A1,1,0,0,0,4,7H5V20a1,1,0,0,0,1,1h9v1a1,1,0,0,0,1,1h4a1,1,0,0,0,1-1V18a1,1,0,0,0-1-1H16a1,1,0,0,0-1,1v1H7V13h8v1a1,1,0,0,0,1,1h4a1,1,0,0,0,1-1V10A1,1,0,0,0,20,9ZM5,3H7V5H5ZM17,19h2v2H17Zm2-6H17V11h2Z">
                </path>
              </g>
            </svg>
          </button>
          <button @click.prevent="emit('moveToTop', props.card);"
            title="Move to Top"
            class="w-8 h-8 bg-gray-50 text-gray-600 hover:text-black hover:bg-gray-200 rounded-md grid place-content-center">
            <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
              <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
              <g id="SVGRepo_iconCarrier">
                <path d="M12 5V19M12 5L6 11M12 5L18 11" stroke="#000000" stroke-width="2.4" stroke-linecap="round"
                  stroke-linejoin="round"></path>
              </g>
            </svg>
          </button>
          <button @click.prevent="showForm"
            title="Edit"
            class="w-8 h-8 bg-gray-50 text-gray-600 hover:text-black hover:bg-gray-200 rounded-md grid place-content-center">
            <PencilIcon class="w-5 h-5" />
          </button>
          <button @click.prevent="changeStatus"
            title="Mark as Completed"
            class="w-8 h-8 bg-gray-50 text-green-600 hover:text-green-700 hover:bg-gray-200 rounded-md grid place-content-center"
            v-if="!props.card.is_completed">
            <svg height="20px" width="20px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
              xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1792 1792" xml:space="preserve" fill="#00b32d"
              stroke="#00b32d">
              <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
              <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
              <g id="SVGRepo_iconCarrier">
                <circle cx="896" cy="896" r="782.9"></circle>
                <path class="st0"
                  d="M1322.7,592.9c-40.6-40.6-106.3-40.6-146.9,0l-455.7,456L549.4,878.6c-40.6-40.6-106.3-40.6-146.9,0 c-40.6,40.6-40.6,106.3,0,146.9l233.6,232.4c3.1,4.1,6.5,8.1,10.2,11.8c20.5,20.5,47.5,30.6,74.4,30.4c26.8,0.2,53.7-9.9,74.2-30.4 c3.8-3.8,7.3-7.9,10.4-12.1l517.4-517.8C1363.3,699.3,1363.3,633.5,1322.7,592.9z">
                </path>
              </g>
            </svg>
          </button>
          <button @click.prevent="changeStatus"
            title="Mark as Incomplete"
            class="w-8 h-8 bg-gray-50 text-red-600 hover:text-red-700 hover:bg-gray-200 rounded-md grid place-content-center"
            v-else>
            <svg fill="rgb(220, 38, 38)" width="20px" height="20px" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg" id="cross-circle" class="icon glyph" stroke="rgb(220, 38, 38)"
              stroke-width="0.00024000000000000003">
              <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
              <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
              <g id="SVGRepo_iconCarrier">
                <path
                  d="M12,2A10,10,0,1,0,22,12,10,10,0,0,0,12,2Zm3.71,12.29a1,1,0,0,1,0,1.42,1,1,0,0,1-1.42,0L12,13.42,9.71,15.71a1,1,0,0,1-1.42,0,1,1,0,0,1,0-1.42L10.58,12,8.29,9.71A1,1,0,0,1,9.71,8.29L12,10.58l2.29-2.29a1,1,0,0,1,1.42,1.42L13.42,12Z">
                </path>
              </g>
            </svg>
          </button>
          <button @click.prevent="openModal"
            title="Delete"
            class="w-8 h-8 bg-gray-50 text-red-600 hover:text-red-700 hover:bg-gray-200 rounded-md grid place-content-center">
            <TrashIcon class="w-5 h-5" />
          </button>
        </div>
      </div>

      <p v-if="card.children && card.children.length" class="text-sm text-blue-500 pl-3 py-2">
      <ul>
        <li class="flex items-center cursor-pointer hover:underline" v-for="child in card.children" :key="child.id"
          @click="scrollToCard(child)">
          <div class="pl-1">
            <svg v-if="child.is_completed" height="15px" width="15px" version="1.1" id="Layer_1"
              xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1792 1792"
              xml:space="preserve" fill="#00b32d" stroke="#00b32d">
              <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
              <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
              <g id="SVGRepo_iconCarrier">
                <circle cx="896" cy="896" r="782.9"></circle>
                <path class="st0"
                  d="M1322.7,592.9c-40.6-40.6-106.3-40.6-146.9,0l-455.7,456L549.4,878.6c-40.6-40.6-106.3-40.6-146.9,0 c-40.6,40.6-40.6,106.3,0,146.9l233.6,232.4c3.1,4.1,6.5,8.1,10.2,11.8c20.5,20.5,47.5,30.6,74.4,30.4c26.8,0.2,53.7-9.9,74.2-30.4 c3.8-3.8,7.3-7.9,10.4-12.1l517.4-517.8C1363.3,699.3,1363.3,633.5,1322.7,592.9z">
                </path>
              </g>
            </svg>
            <svg v-else fill="rgb(220, 38, 38)" width="15px" height="15px" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg" id="cross-circle" class="icon glyph" stroke="rgb(220, 38, 38)"
              stroke-width="0.00024000000000000003">
              <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
              <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
              <g id="SVGRepo_iconCarrier">
                <path
                  d="M12,2A10,10,0,1,0,22,12,10,10,0,0,0,12,2Zm3.71,12.29a1,1,0,0,1,0,1.42,1,1,0,0,1-1.42,0L12,13.42,9.71,15.71a1,1,0,0,1-1.42,0,1,1,0,0,1,0-1.42L10.58,12,8.29,9.71A1,1,0,0,1,9.71,8.29L12,10.58l2.29-2.29a1,1,0,0,1,1.42,1.42L13.42,12Z">
                </path>
              </g>
            </svg>
          </div>
          &nbsp;
          <b>{{ child.title }}
          </b>
        </li>
      </ul>
      </p>
      <!-- deadline in human readable format using moment js -->
      <small v-if="props.card.deadline" class="text-xs text-red-800 font-bold">
        {{ $filters.humanFormatDate(new Date(props.card.deadline)) }}
      </small>
    </div>
    <div v-if="(!card.is_completed) && card.total_quantity " class="progress-bar mt-2 relative group hover:h-7 transition-all flex justify-center items-center flex-col" :style="{
      // marginBottom: '-10px',
      // marginRight: '-6px',
      // marginLeft: '-6px',
    }">
      <div class="w-full group-hover:pt-4 group-hover:px-10">
        <div class="bg-green-500" :style="{ 
          width: progressBarWidth,
          height: '4px', 
          borderRadius: '2px',
          position: 'relative',
          zIndex: 1, 
          }">
        </div>
        <div class="bg-gray-200" :style="{ 
          width: '100%',
          height: '4px', 
          marginTop: '-4px',
          borderRadius: '2px',
          position: 'relative',
          zIndex: 0,
          }"></div>
      </div>
      <span style="font-size: 0.7rem;">
        {{ props.card.completed_quantity ?? 0 }} / {{ props.card.total_quantity }}
      </span>
      <div class="hidden absolute group-hover:flex justify-between items-center w-full">
        <button @click.prevent="router.put(route('columns.cards.update', { column: props?.card?.column, card: props?.card?.id }), { completed_quantity: Math.max((props.card.completed_quantity ?? 0) - 1, 0) })"
          class="w-8 h-8 bg-gray-50 text-gray-600 hover:text-black hover:bg-gray-200 rounded-md grid place-content-center">
          🔴
        </button>
        <button @click.prevent="router.put(route('columns.cards.update', { column: props?.card?.column, card: props?.card?.id }), { completed_quantity: Math.min((props.card.completed_quantity ?? 0) + 1, props.card.total_quantity) })"
          class="w-8 h-8 bg-gray-50 text-gray-600 hover:text-black hover:bg-gray-200 rounded-md grid place-content-center"
        >
          🟢
        </button>
      </div>
    </div>
  </div>
  <ConfirmDialog :show="isOpen" @confirm="closeModal($event)" title="Remove Card"
    message="Are you sure you want to delete this card?" />
</template>

<style>
@keyframes animate-border-width {
  0% {
    border-color: #d1d5db;
    /* background-color: #f3f4f6; */
  }

  50% {
    border-color: #ef4444;
    /* background-color: #ffd8d8; */
  }

  100% {
    border-color: #d1d5db;
    /* background-color: #f3f4f6; */
  }
}


/* golden blink animation for focus  */
@keyframes blink {

  0%,
  100% {
    border-color: #d1d5db;
    background-color: #f3f4f6;
  }

  50% {
    border-color: #f59e0b;
    background-color: #fff7d4;
  }
}

/* for div with class animated-border.border-red-500 */
.border-red-animation {
  animation: animate-border-width 2s infinite;
}

/* golden blink animation */
.golden-blink {
  animation: blink 1s infinite;
}

.st0 {
  fill: #ffffff;
}
</style>
