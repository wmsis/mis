<template>
    <div>
        <!--搜索-->
        <Drawer
            title="搜索条件"
            v-model="shows.search"
            width="420"
            :styles="styles">
            <Form ref="searchFormRef" :model="searchForm">
                <slot name="searchFormSlot"></slot>
            </Form>
            <div class="demo-drawer-footer">
                <Button
                    icon="ios-refresh"
                    @click="clearSearch">清空</Button>
                <Button type="primary"
                        icon="md-search"
                        @click="loadContents">搜索</Button>
            </div>
        </Drawer>
        <div class="container-base-list">
            <!--工具栏-->
            <div class="button-bar" v-if="showButtons">
                <ButtonGroup
                             :style="{'margin': '0px 0px 10px 0px'}">
                    <Button
                        v-for="(button, idx) in buttons"
                        :key="idx"
                        type="primary"
                        :icon="button.icon"
                        @click="btnClick(button.method)">{{button.name}}</Button>
                    <Button
                        v-if="showSearchBtn"
                        type="primary"
                        icon="md-search"
                        @click="shows.search = true">搜索</Button>
                </ButtonGroup>
            </div>
            <!--表格-->
            <div class="table">
                <Table ref="userTable"
                       :loading="loading"
                       :columns="columns"
                       :data="pager"
                       :highlight-row="true"
                       size="small"
                       @on-current-change="currentChange"
                       border
                       stripe>
                </Table>
            </div>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue';
    import {
        Drawer,
        Form,
        Input,
        Button,
        ButtonGroup,
        Table
    } from 'iview';

    Vue.component('Form', Form);
    Vue.component('Input', Input);
    Vue.component('Drawer', Drawer);
    Vue.component('Button', Button);
    Vue.component('ButtonGroup', ButtonGroup);
    Vue.component('Table', Table);

    export default {
        props: {
            columns: {
                required: true
            },
            pageUrl: {
                required: true
            },
            lazyLoad: {
                required: false,
                default: false
            },
            shows: {
                required: true
            },
            showSearchBtn: {
                required: false,
                default: true
            },
            buttons: {
                required: false,
                default: []
            },
            searchForm: {
            }
        },
        data: function() {
            return {
                pager: [
                ],
                pageNumber: 1,
                formData: {

                },
                tableHeight: '',
                loading: false,
                styles: {
                    height: 'calc(100% - 55px)',
                    overflow: 'auto',
                    paddingBottom: '53px',
                    position: 'static'
                }
            }
        },
        computed: {
            showButtons: function () {
                if (this.showSearchBtn) {
                    return true;
                }
                if (this.buttons.length > 0) {
                    return true;
                }
                return false;
            }
        },
        mounted: function() {
            this.loadButtons();
            if (!this.lazyLoad) {
                this.loadContents();
            }
            this.calculateTableHeight();
        },
        methods: {
            calculateTableHeight: function() {
                let clientHeight = document.documentElement.clientHeight;
                if (this.showButtons) {
                    this.tableHeight = clientHeight - 150;
                } else {
                    this.tableHeight = clientHeight - 109
                }
            },
            loadButtons: function() {
            },
            loadContents: function (pageNumber, pageUrl) {
                let vm = this;
                let params = vm.searchForm || {};
                params.num = 20;
                if (pageNumber && pageNumber > 0) {
                    params.page = pageNumber;
                } else {
                    params.page = 1;
                }
                this.loading = true;
                vm.ajax({
                    url: pageUrl || vm.pageUrl,
                    params: params,
                    success: function(data) {
                        vm.pager = data;
                        vm.shows.search = false;
                        vm.loading = false;
                    },
                    fail(){
                        vm.loading = false;
                    }
                });
            },
            clearSearch: function() {
                for (let key in this.searchForm) {
                    this.searchForm[key] = '';
                }
                this.loadContents(1);
            },
            pageChange: function(pageNumber) {
                this.pageNumber = pageNumber;
                this.loadContents(pageNumber);
            },
            btnClick: function(method) {
                this.$emit("listenBtnClick", method);
            },
            currentChange: function (currentRow) {
                this.$emit("listenCurrentChange", currentRow);
            }
        }
    }
</script>
<style lang="scss">
    .container-base-list {
        display: flex;
        flex-direction: column;
    }
    .container-base-list .button-bar {
        height: 40px;
    }
    .container-base-list .table {
        height: calc(100vh - 150px);
    }
    .container-base-list .pager {
        height: 35px;
    }
    .demo-drawer-footer{
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
