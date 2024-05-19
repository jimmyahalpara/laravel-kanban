<script setup>
import { computed, nextTick, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import cloneDeep from 'lodash.clonedeep';
import Draggable from 'vuedraggable';
import Card from '@/Components/Kanban/Card.vue';
import CardCreate from '@/Components/Kanban/CardCreate.vue';
import ConfirmDialog from '@/Components/Kanban/ConfirmDialog.vue';
import MenuDropDown from '@/Components/Kanban/MenuDropDown.vue';
import EditableText from '@/Components/EditableText.vue';

const props = defineProps({
  board: Number,
  column: Object,
});

const emit = defineEmits(['reorder-commit', 'reorder-change']);

const columnTitle = computed(() => props.column?.title);
const cards = ref(props?.column?.cards);
const latestCards = computed(() => props?.column?.cards);
const cardsRef = ref();

// Keep the cards up-to-date
watch(
  () => props?.column,
  () => (cards.value = props?.column?.cards)
);

// TODO: Move to composable useModal
const isOpen = ref(false);
const parent = ref(null);
// random number to trigger
const randomOpenTrigger = ref(0);
const closeModal = confirm => {
  isOpen.value = false;
  if (confirm) {
    router.delete(route('columns.destroy', { column: props?.column?.id }));
  }
};
const openModal = () => (isOpen.value = true);

const moveLeft = () => {
  router.post(route('columns.move', { column: props?.column?.id, direction: 'left' }));
}
const moveRight = () => {
  router.post(route('columns.move', { column: props?.column?.id, direction: 'right' }));
}

const addChildHandler = card => {
  parent.value = card;
  randomOpenTrigger.value = Math.random();
};

const clearParentId = () => {
  parent.value = null;
};

const onCardCreated = async () => {
  // scroll to the bottom of the list
  await nextTick();
  cardsRef.value.scrollTop = cardsRef.value.scrollHeight;
  parent.value = null;
};

const menuItems = [
  {
    name: 'Delete column',
    action: () => openModal(),
  },
  {
    name: 'Move Left',
    action: () => moveLeft(),
    icon: '<svg width="20px" height="20px" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="1.032" transform="rotate(180)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.2929 4.29289C12.6834 3.90237 13.3166 3.90237 13.7071 4.29289L20.7071 11.2929C21.0976 11.6834 21.0976 12.3166 20.7071 12.7071L13.7071 19.7071C13.3166 20.0976 12.6834 20.0976 12.2929 19.7071C11.9024 19.3166 11.9024 18.6834 12.2929 18.2929L17.5858 13H4C3.44772 13 3 12.5523 3 12C3 11.4477 3.44772 11 4 11H17.5858L12.2929 5.70711C11.9024 5.31658 11.9024 4.68342 12.2929 4.29289Z" fill="#000000"></path> </g></svg>'
  },
  {
    name: 'Move Right',
    action: () => moveRight(),
    icon: '<svg width="20px" height="20px" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="1.032"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.2929 4.29289C12.6834 3.90237 13.3166 3.90237 13.7071 4.29289L20.7071 11.2929C21.0976 11.6834 21.0976 12.3166 20.7071 12.7071L13.7071 19.7071C13.3166 20.0976 12.6834 20.0976 12.2929 19.7071C11.9024 19.3166 11.9024 18.6834 12.2929 18.2929L17.5858 13H4C3.44772 13 3 12.5523 3 12C3 11.4477 3.44772 11 4 11H17.5858L12.2929 5.70711C11.9024 5.31658 11.9024 4.68342 12.2929 4.29289Z" fill="#000000"></path> </g></svg>'
  }
];

const onReorderCards = () => {
  const cloned = cloneDeep(cards?.value);
  
  const cardsWithOrder = [
    ...cloned?.map((card, index) => ({
      id: card.id,
      position: index * 1000 + 1000,
    })),
  ];

  emit('reorder-change', {
    id: props?.column?.id,
    cards: cardsWithOrder,
  });
};

const moveToTop = card => {
  const cloned = cloneDeep(cards?.value);
  const index = cloned.findIndex(c => c.id === card.id);
  cloned.splice(index, 1);
  cloned.unshift(card);
  cards.value = cloned;
  emit('reorder-change', {
    id: props?.column?.id,
    cards: cloned.map((card, index) => ({
      id: card.id,
      position: index * 1000 + 1000,
    })),
  });
  emit('reorder-commit');
};

const onReorderEnds = () => {
  emit('reorder-commit');
};
</script>

<template>
  <div class="w-72 max-h-full bg-gray-200 flex flex-col rounded-md">
    <div class="flex items-center justify-between px-3 py-2">
      <h3 class="font-semibold text-sm text-gray-700">
        <EditableText :value="columnTitle" @textinput="router.post(route('columns.update.title', { column: props.column.id }), { title: $event })"/>
      </h3>
      <MenuDropDown :menuItems="menuItems" />
    </div>
    <div class="pb-3 flex-1 flex flex-col overflow-hidden">
      <div class="px-3 overflow-y-auto" ref="cardsRef">
        <Draggable
          v-model="cards"
          group="cards"
          item-key="id"
          tag="ul"
          drag-class="drag"
          ghost-class="ghost"
          class="space-y-3"
          @change="onReorderCards"
          @end="onReorderEnds"
        >
          <template #item="{ element }">
            <li>
              <Card 
                :card="element" 
                @moveToTop="moveToTop(element)"
                @addChild="addChildHandler(element)"
              />
            </li>
          </template>
        </Draggable>
        <div class="px-3 mt-3">
          <CardCreate 
            :parent="parent"
            :column="column" 
            :openTrigger="randomOpenTrigger"
            @created="onCardCreated" 
            @clearParentId="clearParentId"
            />
        </div>
      </div>
    </div>
  </div>
  <ConfirmDialog
    :show="isOpen"
    @confirm="closeModal($event)"
    title="Remove Column"
    message="Are you sure you want to delete this column and all its cards?"
  />
</template>
