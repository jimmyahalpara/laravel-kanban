<template>
        <div class="flex justify-center items-center">
            <input 
                type="text" 
                v-model="title" 
                v-if="showInput" 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                placeholder="Enter board title"
                @keyup.enter="createBoard"
            >
            <button @click="createBoard" v-if="showInput" class="px-4 py-2">+</button>
            <button @click="showInput = true" v-if="!showInput" class="px-4 py-2">+</button>
        </div>
</template>

<script>
export default {
    data() {
        return {
            title: '',
            showInput: false,
        }
    },
    methods: {
        createBoard() {
            console.log(1231231231231);
            if (!this.title) {
                return;
            }

            // submit post request 
            this.$inertia.post(route('boards.store'), {
                title: this.title
            });

            this.title = '';
            this.showInput = false;
        }
    }
}
</script>