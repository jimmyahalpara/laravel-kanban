<script setup>
import { Head, router } from '@inertiajs/vue3';
import EditableText from '@/Components/EditableText.vue';
</script>
<template>
    <div 
        ref="categoryItem"
        @mouseover="mouseOver = true" 
        @mouseleave="mouseOver = false" 
        class="border border-1 px-2 py-0 transition-all duration-300 flex justify-between items-center"
        :style="{
            borderRadius: mouseOver ? '0' : '2rem',
            borderColor: mouseOver ? 'black' : 'transparent',
            backgroundColor: mouseOver ? 'transparent' : this.categoryObject.background_color,
            color: mouseOver ? 'black'  :this.categoryObject.color,
            overflow: 'hidden',
            height: '1.5rem',
        }"
    >
        <div class="text-sm" :style="{
            minWidth: this.originalWidth - 10 + 'px',
        }">
            <EditableText 
            :value="category.name"
            @textinput="categoryObject.name = $event"/>
        </div>
        <div :style="{
                    width: mouseOver ? 'auto' : '0px',
                    overflow: 'hidden',
                    display: 'flex',
                }">
                <div>
                    <color-picker v-model:pureColor="categoryObject.background_color" />
                </div>
                <div>
                    <color-picker v-model:pureColor="categoryObject.color" />
                </div>
                <button @click="deleteHandler()" v-html='confirmDelete ? `<svg height="20px" width="20px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1792 1792" xml:space="preserve" fill="#ff0000" stroke="#ff0000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css"> .st0{fill:#FFFFFF;} </style> <circle cx="896" cy="896" r="782.9"></circle> <path class="st0" d="M1322.7,592.9c-40.6-40.6-106.3-40.6-146.9,0l-455.7,456L549.4,878.6c-40.6-40.6-106.3-40.6-146.9,0 c-40.6,40.6-40.6,106.3,0,146.9l233.6,232.4c3.1,4.1,6.5,8.1,10.2,11.8c20.5,20.5,47.5,30.6,74.4,30.4c26.8,0.2,53.7-9.9,74.2-30.4 c3.8-3.8,7.3-7.9,10.4-12.1l517.4-517.8C1363.3,699.3,1363.3,633.5,1322.7,592.9z"></path> </g></svg>` :`<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 6.38597C3 5.90152 3.34538 5.50879 3.77143 5.50879L6.43567 5.50832C6.96502 5.49306 7.43202 5.11033 7.61214 4.54412C7.61688 4.52923 7.62232 4.51087 7.64185 4.44424L7.75665 4.05256C7.8269 3.81241 7.8881 3.60318 7.97375 3.41617C8.31209 2.67736 8.93808 2.16432 9.66147 2.03297C9.84457 1.99972 10.0385 1.99986 10.2611 2.00002H13.7391C13.9617 1.99986 14.1556 1.99972 14.3387 2.03297C15.0621 2.16432 15.6881 2.67736 16.0264 3.41617C16.1121 3.60318 16.1733 3.81241 16.2435 4.05256L16.3583 4.44424C16.3778 4.51087 16.3833 4.52923 16.388 4.54412C16.5682 5.11033 17.1278 5.49353 17.6571 5.50879H20.2286C20.6546 5.50879 21 5.90152 21 6.38597C21 6.87043 20.6546 7.26316 20.2286 7.26316H3.77143C3.34538 7.26316 3 6.87043 3 6.38597Z" fill="#ff0000"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M11.5956 22.0001H12.4044C15.1871 22.0001 16.5785 22.0001 17.4831 21.1142C18.3878 20.2283 18.4803 18.7751 18.6654 15.8686L18.9321 11.6807C19.0326 10.1037 19.0828 9.31524 18.6289 8.81558C18.1751 8.31592 17.4087 8.31592 15.876 8.31592H8.12404C6.59127 8.31592 5.82488 8.31592 5.37105 8.81558C4.91722 9.31524 4.96744 10.1037 5.06788 11.6807L5.33459 15.8686C5.5197 18.7751 5.61225 20.2283 6.51689 21.1142C7.42153 22.0001 8.81289 22.0001 11.5956 22.0001ZM10.2463 12.1886C10.2051 11.7548 9.83753 11.4382 9.42537 11.4816C9.01321 11.525 8.71251 11.9119 8.75372 12.3457L9.25372 17.6089C9.29494 18.0427 9.66247 18.3593 10.0746 18.3159C10.4868 18.2725 10.7875 17.8856 10.7463 17.4518L10.2463 12.1886ZM14.5746 11.4816C14.9868 11.525 15.2875 11.9119 15.2463 12.3457L14.7463 17.6089C14.7051 18.0427 14.3375 18.3593 13.9254 18.3159C13.5132 18.2725 13.2125 17.8856 13.2537 17.4518L13.7537 12.1886C13.7949 11.7548 14.1625 11.4382 14.5746 11.4816Z" fill="#ff0000"></path> </g></svg>`'>
                </button>
        </div>
    </div>
</template>
<script>
import { ColorPicker } from "vue3-colorpicker";
import "vue3-colorpicker/style.css";
export default {
    props: {
        category: Object,
    },
    components: {
        ColorPicker,
    },
    data() {
        return {
            mouseOver: false,
            extraWidth: 170,
            // compute the original width of the category item
            originalWidth: 0,
            categoryObject: {
                name: this.category.name,
                background_color: this.category.background_color,
                color: this.category.color,
            },
            timeOutHandler: null,
            confirmDelete: false,
        };
    },
    mounted() {
        this.originalWidth = window.getComputedStyle(this.$refs.categoryItem).width;
        // strip the px from the width
        this.originalWidth = parseFloat(this.originalWidth.replace('px', ''));
    },
    methods: {
        deleteHandler() {
            if (!this.confirmDelete) {
                this.confirmDelete = true;
            } else {
                router.delete(route('boards.category.destroy', {
                    board: this.category.board_id,
                    cardCategory: this.category.id,
                }));
            }
        },
    },
    computed: {
        
    },
    watch : {
        // watch mouse over, if it is true, then add extra width
        mouseOver(value) {
            const refItem = this.$refs.categoryItem;
            if (value) {
                // add extra width
                refItem.style.width = this.originalWidth + this.extraWidth - 20 + 'px';
            } else {
                this.confirmDelete = false;
                // remove extra width
                refItem.style.width = this.originalWidth + 'px';
            }
        },
        categoryObject : {
            handler(value) {
                // debounce the update
                if (this.timeOutHandler) {
                    clearTimeout(this.timeOutHandler);
                }
                this.timeOutHandler = setTimeout(() => {
                    router.put(route('boards.category.update', {
                        board: this.category.board_id,
                        cardCategory: this.category.id,
                    }), value, {
                        onSuccess: () => {
                            // recalculate the original width
                            this.originalWidth = window.getComputedStyle(this.$refs.categoryItem).width;
                            // strip the px from the width
                            this.originalWidth = parseFloat(this.originalWidth.replace('px', ''));
                        }
                    });
                }, 500);
            },
            deep: true,
        }
    },

};
</script>