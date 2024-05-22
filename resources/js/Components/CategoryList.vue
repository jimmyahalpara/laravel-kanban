<script setup>
import { Head, router } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex-1 h-full overflow-x-auto ml-5">
        <div class="flex h-full items-center justify-start space-x-2 overflow-auto">
            <CategoryItem v-for="category in categories" :key="category.id" :category="category" />
            <input v-if="isInputBeingShown" v-model="title" type="text" placeholder="Enter Title"
                @keydown.enter="handleAddClick()"
                class="mt-1 block mb-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                />
            <button 
                class="px-4 py-2 text-black rounded-md mx-4" 
                @click="handleAddClick()">
                +
            </button>
        </div>
    </div>
</template>
<script>
import CategoryItem from '@/Components/CategoryItem.vue';
export default {
    props: {
        board: Object,
    },
    components: {
        CategoryItem,
    },
    data() {
        return {
            title: '',
            isInputBeingShown: false,

        };
    },
    mounted() {
        
    },
    methods: {
        handleAddClick(){
            if (this.isInputBeingShown) {
                router.post(route('boards.category.store', this.board.data.id), { name: this.title }, {
                    onSuccess: () => {
                        this.title = '';
                        this.isInputBeingShown = false;
                    }
                });
            } else {
                this.isInputBeingShown = true;
            }
        }
    },
    computed: {
        categories() {
            return this.board?.data?.card_categories;
        },
    }
};
</script>