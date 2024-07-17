<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>

  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <div class="w-full h-full overflow-y-auto no-scroll">
      <table class="table-auto text-sm w-full mt-3 border-collapse">
        <thead>
          <tr class="text-left border bg-gray-400 text-white">
            <th class="px-4"></th>
            <th class="" width="1%">
            </th>
            <th class="px-4" width="30%">Title</th>
            <th class="px-4 text-center">Board</th>
            <th class="px-4 text-center">Column</th>
            <th class="px-4 text-center">Category</th>
            <th class="px-4 text-center">Due</th>
            <th class="px-4">Updated At</th>
          </tr>
        </thead>
        <Draggable v-model="cardData" group="cards" item-key="id" tag="tbody" drag-class="drag" ghost-class="ghost"
          class="space-y-3" @change="onReorderCards" @end="onReorderEnds">
          <template #item="{ element }">
            <tr class="border border-gray-500 border-1" :class="[getCardBgColor(element)]">
              <td class="px-4 cursor-move">
                <svg fill="#000000" width="15px" height="15px" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg"
                  stroke="#000000" stroke-width="0.019200000000000002">
                  <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                  <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                  <g id="SVGRepo_iconCarrier">
                    <path
                      d="M600 1440c132.36 0 240 107.64 240 240s-107.64 240-240 240-240-107.64-240-240 107.64-240 240-240Zm720 0c132.36 0 240 107.64 240 240s-107.64 240-240 240-240-107.64-240-240 107.64-240 240-240ZM600 720c132.36 0 240 107.64 240 240s-107.64 240-240 240-240-107.64-240-240 107.64-240 240-240Zm720 0c132.36 0 240 107.64 240 240s-107.64 240-240 240-240-107.64-240-240 107.64-240 240-240ZM600 0c132.36 0 240 107.64 240 240S732.36 480 600 480 360 372.36 360 240 467.64 0 600 0Zm720 0c132.36 0 240 107.64 240 240s-107.64 240-240 240-240-107.64-240-240S1187.64 0 1320 0Z"
                      fill-rule="evenodd"></path>
                  </g>
                </svg>
              </td>
              <td class="border border-gray-500 border-1 cursor-pointer" @click="element.is_completed = !element.is_completed">
                <div class="flex justify-center items-center w-full h-full">
                  {{ element.is_completed ? '🟢' : '🔴'  }}
                </div>
              </td>
              <td class="px-4 border border-gray-500 border-1">
                <EditableText :value="element.title" @textinput="element.title = $event"/>
              </td>
              <td class="px-0 py-0 border border-gray-500 border-1">
                <select class="m-0 text-center w-full rounded-sm p-0 pe-5 border-none" disabled v-model="element.board">
                  <option v-for="board in boards" :key="board.id" :value="board.id">{{ board.title }}</option>
                </select>
              </td>
              <td class="px-0 py-0 border border-gray-500 border-1">
                <select class="m-0 text-center w-full rounded-sm p-0 pe-3 border-none" v-model="element.column">
                  <option v-for="column in board_columns[element.board]" :key="column.id" :value="column.id">{{
                    column.title }}</option>
                </select>
              </td>
              <td class="px-0 py-0 border border-gray-500 border-1">
                <select class="m-0 text-center w-full rounded-sm p-0 pe-3 border-none" v-model="element.card_category_id"
                  :style="{
                    backgroundColor: getCategory(element.board, element.card_category_id)?.background_color,
                    color: getCategory(element.board, element.card_category_id)?.color
                  }">
                  <option v-for="category in (board_categories[element.board] ?? [])" :key="category.id"
                    :value="category.id" :style="{
                      backgroundColor: category?.background_color,
                      color: category?.color
                    }">{{ category.name }}</option>
                </select>
              </td>
              <td class="px-0 py-0 border border-gray-500 border-1">
                <input type="datetime-local" class="m-0 text-center w-full rounded-sm p-0 border-none" v-model="element.deadline">
              </td>
              <td class="px-4 border border-gray-500 border-1">{{ element.updated_at }}</td>
            </tr>
          </template>
        </Draggable>
      </table>
    </div>
  </AuthenticatedLayout>
</template>
<script>
import Draggable from 'vuedraggable';
import EditableText from '@/Components/EditableText.vue';
export default {
  components: {
    Draggable,
    EditableText,
  },
  props: {
    cards: Object,
    boards: Array,
    board_columns: Object,
    board_categories: Object,
  },
  data() {
    return {
      cardData: this.cards?.data,
      updateCardTimeout: null,
    };
  },
  methods: {
    onReorderCards() {
    },
    onReorderEnds() {
    },
    getCategory(boardId, categoryId) {
      return this.board_categories[boardId]?.find((category) => category.id === categoryId);
    },
    getCardBgColor(element){
      var greyColor = !element.is_completed && new Date(element.deadline) > new Date();
      var greenborder = element.is_completed;
      var redborder = element.deadline && !element.is_completed && (new Date(element.deadline) <= new Date() || new Date(element.deadline).toDateString() === new Date().toDateString());

      if (greyColor){
        return 'bg-gray-200';
      } else if (greenborder){
        return 'bg-green-500';
      } else if (redborder){
        return 'bg-red-500';
      }
    },
    updateCards(){
      // make post request to update cards
      this.$inertia.post(route('dashboard.update-cards'), {
        cards: this.cardData,
      });
    }
  },
  watch: {
    cardData: {
      handler() {
        console.log('cardData changed');
        clearTimeout(this.updateCardTimeout);
        this.updateCardTimeout = setTimeout(() => {
          this.updateCards();
        }, 1000);
      },
      deep: true,
    },
  },

}
</script>

<style scoped>
  .no-scroll::-webkit-scrollbar {
    display: none;
  }

  .no-scroll {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }
</style>
