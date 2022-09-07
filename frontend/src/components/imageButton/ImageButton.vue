<template>
    <div :style="{marginTop: '5px', float: 'left', marginRight: '10px'}">
        <a @click="imageClick">
            <img :src="imageUrl" :style="{width: width, height: height}"/>
        </a>
    </div>
</template>
<script>
    export default {
        props: {
            imageUrl: {
                required: true,
            },
            width: {
                required: false,
                default: '50px'
            },
            height: {
                required: false,
                default: '50px'
            },
        },
        data() {
            return {
            }
        },
        methods: {
            imageClick: function () {
                this.$emit('onImageClick');
            }
        }
    }
</script>
