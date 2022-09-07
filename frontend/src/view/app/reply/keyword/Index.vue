<!--role table-->
<template>
    <div>
        <!--新增-->
        <keyword-reply-add :shows="shows" v-on:listenChildClose="childClose"></keyword-reply-add>

        <!--修改-->
        <keyword-reply-update :shows="shows" :keyword-reply="row" v-on:listenChildClose="childClose"></keyword-reply-update>

        <base-table ref="tableRef"
                    v-bind:columns="columns"
                    v-bind:pageUrl="pageUrl"
                    v-bind:searchForm="searchForm"
                    v-bind:shows="shows"
                    v-bind:showSearchBtn="isButtonExist('keyword-search')"
                    v-bind:buttons="buttons"
                    :height="true"
                    v-on:listenBtnClick="btnClick"
                    v-on:listenCurrentChange="currentChange">
            <div slot="searchFormSlot">
                <FormItem label="关键字" label-position="top">
                    <Input v-model="searchForm.search" placeholder="请输入关键字"/>
                </FormItem>
            </div>
            <template slot="option" slot-scope="{ row }">
                <div>
                    <Button type="primary" size="small" style="margin-right: 5px;" @click.stop="update(row)" v-if="isButtonExist('keyword-update')">修改</Button>
                    <Button type="error" size="small" @click.stop="remove(row)" v-if="isButtonExist('keyword-delete')">删除</Button>
                </div>
            </template>
        </base-table>
    </div>
</template>
<script>
    import Vue from 'vue';
    import {
        FormItem,
        Input,
        Button
    } from 'iview';

    Vue.component('FormItem', FormItem);
    Vue.component('Input', Input);
    Vue.component('Button', Button);

    import BaseTable from '@/components/baseTable'
    import KeywordReplyAdd from './Add.vue'
    import KeywordReplyUpdate from './Update.vue'

    export default {
        components: {
            'base-table': BaseTable,
            'keyword-reply-add': KeywordReplyAdd,
            'keyword-reply-update': KeywordReplyUpdate
        },
        data: function() {
            return {
                shows: {
                    search: false,
                    add: false,
                    update: false
                },
                columns: [
                    {
                        title: '关键字',
                        key: 'keyword',
                        minWidth: 120
                    },
                    {
                        title: '回复类型',
                        key: 'type',
                        minWidth: 120,
                        render: (h, {row, column, index}) => {
                            if (row.type == 'img') {
                                return h('div', {style:{}}, '图片');
                            } else if (row.type == 'pic_txt') {
                                return h('div', {style:{}}, '图文');
                            } else if (row.type == 'text') {
                                return h('div', {style:{}}, '文本');
                            }
                        }
                    },
                    {
                        title: '操作',
                        minWidth: 120,
                        align: 'center',
                        slot: 'option'
                    }
                ],
                buttons: [{
                    name: '添加关键字',
                    icon: 'md-add-circle',
                    method: 'add',
                    api_name: 'keyword-save'
                }],
                searchForm: {
                    search: ''
                },
                row: {},
                pageUrl: this.$request.keyword.page
            }
        },
        methods: {
            showSearchDrawer: function () {
                this.shows.search = true;
            },
            btnClick: function(method) {
                if (this[method]) {
                    this[method]();
                }
            },
            add: function () {
                this.shows.add = true;
            },
            update: function(row) {
                this.row = row;
                this.shows.update = true;
            },
            remove: function(row) {
                let vm = this;
                vm.showMessageBox('confirm', '确定删除', '确定要删除吗？', ()=>{
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.keyword.delete,
                        params: {
                            id: row.id
                        },
                        success: function () {
                            vm.$refs.tableRef.loadContents();
                        }
                    });
                });
            },
            toMaterialTable: function(row) {
                this.$router.push({
                    path: '/material',
                    query: {
                        picTxtId: row.id
                    }
                });
            },
            download: function () {
            },
            childClose: function() {
                this.$refs.tableRef.loadContents();
            },
            currentChange: function (currentRow, oldCurrentRow) {
                this.row = currentRow;
            }
        }
    }
</script>
<style>
</style>
