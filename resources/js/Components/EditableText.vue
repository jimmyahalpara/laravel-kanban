<template>
    <span 
        ref="editableText"
        contenteditable="true" 
        @input="inputHandler"
    >
        {{ text }}
    </span>
</template>

<script>
export default {
    props: {
        value: String,
    },
    
    data() {
        return {
            text: this.value,
            timeoutHandler: null,
        };
    },
    
    methods: {
        inputHandler(event) {
            // wait for 2000 ms before updating the text
            clearTimeout(this.timeoutHandler);
            this.timeoutHandler = setTimeout(() => {
                this.text = event.target.innerText;
                this.$emit('textinput', this.text);
                // reve focus 
                if (this.text){
                    this.$refs.editableText.blur();
                }
            }, 2000);
        },
    },
};
</script>
