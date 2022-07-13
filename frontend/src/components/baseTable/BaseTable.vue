<template>
    <div class="table-bg">
        <!--搜索-->
        <Drawer
            title="搜索条件"
            v-model="shows.search"
            width="420"
            :styles="styles" >
            <Form ref="searchFormRef" :model="searchForm" label-position="top">
<!--              插槽-->
                <slot name="searchFormSlot"></slot>
            </Form>
            <div class="demo-drawer-footer">
                <Button
                        icon="ios-refresh"
                        @click="clearSearch" v-if="showClearBtn">显示全部</Button>
                <Button type="primary"
                        icon="md-search"
                        @click="loadContents">搜索</Button>
            </div>
        </Drawer>
        <div class="container-table">
            <!--工具栏-->
            <div class="wrapper">
                <div class="table-wrapper">
                    <div class="button-bar">
                        <ButtonGroup :style="{'margin': '0px 0px 10px 0px'}" v-if="showButtons">
                            <Button
                                v-for="(button, idx) in buttons"
                                :key="idx"
                                type="primary"
                                :icon="button.icon"
                                v-if="isButtonExist(button.api_name)"
                                @click="btnClick(button.method)">{{button.name}}</Button>
                            <Button
                                v-if="showSearchBtn"
                                type="primary"
                                icon="md-search"
                                @click="shows.search = true">搜索</Button>
                        </ButtonGroup>
                        <slot name="otherBtns"></slot>
                    </div>
                    <!--表格-->
                    <div class="table">
                        <VTable ref="userTable"
                            :loading="loading"
                            :columns="columns"
                            :data="pager.data"
                            :max-height="height ? tableHeight : 0"
                            :highlight-row="true"
                            size="small"
                            @on-current-change="currentChange"
                            @on-selection-change="selectionChange"
                            border
                            stripe>
                        </VTable>
                    </div>
                </div>
                <div v-if="this.$slots.rightSide" class="right-side">
                    <slot name="rightSide"></slot>
                </div>
            </div>
            <!--分页-->
            <div class="pager">
                <Page size="small"
                      :total="pager.total"
                      :current="pageNumber"
                      :page-size="pageSize"
                      show-total
                      @on-change="pageChange"
                      :style="{'text-align': 'right'}"/>
            </div>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue';
    import VTable from './table';
    import {
        Form,
        Drawer,
        ButtonGroup,
        Button,
        Page,
        Table
    } from 'iview';

    Vue.component('Form', Form);
    Vue.component('Drawer', Drawer);
    Vue.component('ButtonGroup', ButtonGroup);
    Vue.component('Button', Button);
    Vue.component('Page', Page);
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
            height: {
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
            showClearBtn: {
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
                loading: false,
                pager: [
                ],
                pageNumber: 1,
                pageSize: 20,
                formData: {

                },
                tableHeight: '',
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
                    let flag = false;
                    for(let key in this.buttons){
                        let item = this.buttons[key];
                        flag = this.isButtonExist(item.api_name);
                        if(flag){
                            break;
                        }
                    }
                    return flag;
                }
                return false;
            }
        },
        components: {
            VTable
        },
        mounted: function() {
            if(this.searchForm && this.searchForm.hasOwnProperty('num') && this.searchForm.num) {
                this.pageSize = this.searchForm.num;
            }
            if (this.pageUrl) {
                this.loading = true;
                this.loadContents();
            }
            this.calculateTableHeight();
        },
        methods: {
            loadContents: function (pageNumber, pageUrl) {
                let vm = this;
                let params = vm.searchForm || {};
                params.num = params.num ? params.num : vm.pageSize;
                if (pageNumber && pageNumber > 0) {
                    params.page = pageNumber;
                } else {
                    params.page = 1;
                    vm.pageNumber=1;
                }
                vm.shows.search = false;
                vm.loading = true;
                vm.ajax({
                    url: pageUrl || vm.pageUrl,
                    params: params,
                    success: function(data) {
                        vm.pager = data;
                        vm.searchForm.totalPage = Math.ceil(vm.pager.total/20)
                        vm.loading = false;
                    },
                    fail(){
                        vm.loading = false;
                    }
                });
            },
            setSearchForm: function(searchForm) {
                for (let key in this.searchForm) {
                    this.searchForm[key] = '';
                }
                for (let key in searchForm) {
                    this.searchForm[key] = searchForm[key];
                }
            },
            clearSearch: function() {
                for (let key in this.searchForm) {
                    this.searchForm[key] = '';
                }
                this.loadContents(1);
            },
            pageChange: function(pageNumber) {
                this.searchForm.currentPage=pageNumber;
                this.pageNumber = pageNumber;
                this.loadContents(pageNumber);
            },
            btnClick: function(method) {
                this.$emit("listenBtnClick", method);
            },
            currentChange: function (currentRow) {
                this.$emit("listenCurrentChange", currentRow);
            },
            selectionChange: function(rows) {
                 this.$emit("selectionChange", rows);
            },
            calculateTableHeight: function() {
                let clientHeight = document.documentElement.clientHeight;
                if (this.showButtons) {
                    this.tableHeight = clientHeight - 195;
                } else {
                    this.tableHeight = clientHeight - 145;
                }
            }
        }
    }
</script>
<style>
    .container-table {
        display: flex;
        flex-direction: column;
        height: 100%;
        width: 100%;
        position: relative;
    }
    .container-table .button-bar {
        height: 50px;
        display: flex;
        justify-content: space-between;
    }
    .container-table .pager {
        height: 35px;
        margin-top: 10px;
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
    .ivu-table-cell {
        padding-left: 8px;
        padding-right: 8px;
    }
    .right-side{
        margin-left: 15px;
        width: 400px;
        box-sizing: border-box;
    }
    @media (min-width: 1500px){
        .right-side{
            width: 600px;
        }
    }
    @media (min-width: 1800px){
        .right-side{
            width: 800px;
        }
    }
</style>
<style scoped lang="scss">
    .table-bg{
        height: 100%;
        width: 100%;
        position: relative;
    }
    .wrapper {
        display: flex;
        width: 100%;
        height: calc( 100% - 45px );
    }
    .table-wrapper{
        width: 100%;
        height: 100%;
        & .table{
            width: 100%;
            height: calc( 100% - 50px );
            overflow: auto;
        }
    }
</style>
