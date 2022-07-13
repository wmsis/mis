<template>
    <div class="table-bg">
        <!--搜索-->
        <Drawer
                title="筛选条件"
                v-model="shows.search"
                width="560"
                :styles="styles">
            <Form ref="searchFormRef" :model="searchForm" label-position="top">
                <slot name="datetimerangeSearchFormSlot"></slot>
                <slot name="tagSearchFormSlot"></slot>
            </Form>
            <div class="drawer-footer">
                <Button
                        icon="ios-refresh"
                        @click="clearSearch" v-if="showClearBtn">显示全部</Button>
                <Button type="primary"
                        icon="md-search"
                        @click="loadContents">筛选</Button>
            </div>
        </Drawer>
    </div>
</template>
<script>
    import Vue from 'vue';
    import {
        Form,
        Drawer,
        ButtonGroup,
        Button
    } from 'iview';

    Vue.component('Form', Form);
    Vue.component('Drawer', Drawer);
    Vue.component('ButtonGroup', ButtonGroup);
    Vue.component('Button', Button);

    export default {
        props: {
            shows: {
                required: true
            },
            showSearchBtn: {
                required: false,
                default: true
            },
            showClearBtn: {
                required: false,
                default: true
            },
            searchForm: {
            }
        },
        data() {
            return {
                loading: false,
                formData: {

                },
                styles: {
                    height: 'calc(100% - 55px)',
                    overflow: 'auto',
                    paddingBottom: '53px',
                    position: 'static'
                }
            }
        },
        methods: {
            loadContents () {
                this.$emit("listenChildClose");
            },
            clearSearch() {
                for (let key in this.searchForm) {
                    this.searchForm[key] = '';
                }
                this.loadContents();
            }
        }
    }
</script>
<style scoped>
    .table-bg{
        height: 100%;
        width: 100%;
        position: relative;
    }
    .drawer-footer{
        width: 100%;
        position: absolute;
        bottom: 0;
        left: 0;
        border-top: 1px solid #e8e8e8;
        padding: 10px 16px;
        text-align: right;
        background: #fff;
    }
</style>
