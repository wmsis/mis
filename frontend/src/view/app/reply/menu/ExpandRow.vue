<template>
    <div>
        <Row class="expand-row" v-for="(menu, index) in children" :key="index">
            <div class="column short-column">
                <div class="idx">{{index}}</div>
            </div>
            <div class="column">
                <span class="expand-value">{{menu.name}}&nbsp;</span>
            </div>
            <div class="column">
                <span class="expand-value" v-if="menu.type == 'view'" :title="menu.url">{{ menu.url }}&nbsp;</span>
                <span class="expand-value" v-if="menu.type == 'click'" :title="menu.keyword">{{ menu.keyword }}&nbsp;</span>
            </div>
            <div class="column">
                <span class="expand-value" v-if="menu.type == 'miniprogram'" :title="menu.pagepath">{{ menu.pagepath }}&nbsp;</span>
            </div>
            <div class="column short-column">
                <span class="expand-value">{{ menu.sort }}&nbsp;</span>
            </div>
            <div class="column short-column">
                <span class="expand-value">{{menu.type == 'view' ? '连接' : (menu.type == 'click' ? '事件' : '小程序')}}&nbsp;</span>
            </div>
            <div class="column cz">
                <Button type="primary" size="small" style="margin-right: 5px;" @click="edit(menu)">编辑</Button>
                <Button type="error" size="small" @click="remove(menu)">删除</Button>
            </div>
        </Row>
    </div>
</template>
<script>
    import Vue from 'vue';
    import {
        Row,
        Col,
        Input,
        Button
    } from 'iview';

    Vue.component('Row', Row);
    Vue.component('Col', Col);
    Vue.component('Input', Input);
    Vue.component('Button', Button);

    export default {
        props: ['children'],
        data(){
            return {
            }
        },
        methods: {
            edit: function (row) {
                this.$emit("listenUpdate", row);
            },
            remove (row) {
                this.$emit("listenRemove", row);
            }
        }
    };
</script>
<style scoped>
    .expand-row{
        margin-bottom: 16px;
        display: flex;
        flex-direction: row;
        width: 100%;
    }
    .expand-row:last-child{
        margin-bottom: 0;
    }
    .column{
        box-sizing: border-box;
        padding-left: 8px;
        padding-right: 8px;
        width: calc( (100% - 252px)/4 );
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .short-column{
        width: 84px;
    }
    .idx{
        opacity: 0;
    }
    .cz{
        text-align: center;
    }
</style>
