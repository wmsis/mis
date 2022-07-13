<template>
    <div>
        <front-update :shows="shows"
                      :data="row"
                      v-on:listenChildClose="childClose">
        </front-update>

        <front-insert :shows="shows" v-on:listenChildClose="childClose">
        </front-insert>

        <base-table ref="tableRef"
                    v-bind:columns="columns"
                    v-bind:pageUrl="pageUrl"
                    v-bind:searchForm="searchForm"
                    v-bind:shows="shows"
                    v-bind:buttons="buttons"
                    :height="true"
                    v-bind:showSearchBtn="isButtonExist('tagbind-search')"
                    v-on:listenBtnClick="btnClick"
                    v-on:listenCurrentChange="currentChange">
            <div slot="searchFormSlot">
                <FormItem label="标签名称">
                    <Input v-model="searchForm.name" placeholder="标签名称"/>
                </FormItem>
            </div>
            <template slot="option" slot-scope="{ row }">
                <div>
                    <Button type="text" size="small" class="btn-my"
                            @click.stop="update(row)" v-if="isButtonExist('tagbind-update')">修改
                    </Button>
                    <Button type="text" size="small" class="btn-my"
                            @click.stop="del(row)" v-if="isButtonExist('tagbind-delete')">删除
                    </Button>
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
        Button,
        Select,
        Option
    } from 'iview';
    import {
        Loading
    } from 'element-ui'

    Vue.component('FormItem', FormItem);
    Vue.component('Input', Input);
    Vue.component('Button', Button);
    Vue.component('Select', Select);
    Vue.component('Option', Option);
    Vue.use(Loading);

    import BaseTable from '@/components/baseTable'
    import FrontUpdate from "./FrontUpdate";
    import FrontAdd from "./FrontAdd";

    export default {
        name: "TagBind",
        components: {
            'base-table': BaseTable,
            'front-update': FrontUpdate,
            'front-insert': FrontAdd
        },
        data: function () {
            return {
                shows: {
                    search: false,
                    update: false,
                    add: false
                },
                pageUrl: this.$request.Boiler.FrontsIndex,
                searchForm: {
                    name: ''
                },
                row: {},
                buttons: [{
                    name: '新增',
                    icon: 'md-add-circle',
                    method: 'add',
                    api_name: 'tagbind-add'
                }],
                columns: [
                    {
                        title: 'FrontId',
                        key: 'front_tag_id',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 100
                    },
                    {
                        title: '名称',
                        key: 'name',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 100
                    },
                    {
                        title: '描述',
                        key: 'description',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 140
                    },
                    {
                        title: 'TagId',
                        key: 'historian_tag_id',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 60
                    },
                    {
                        title: '计算函数',
                        key: 'func',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 100
                    },
                    {
                        title: '创建时间',
                        key: 'created_at',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 140
                    },
                    {
                        title: '更新时间',
                        key: 'updated_at',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 140
                    },
                    {
                        title: '操作',
                        slot: 'option',
                        align: 'center',
                        minWidth: 110
                    }
                ],
                loading: false
            }
        },
        methods: {
            showSearchDrawer: function () {
                this.shows.search = true;
            },
            update: function (row) {
                this.row = row;
                this.shows.update = true;
            },
            add: function () {
                this.shows.add = true;
            },
            del: function (row) {
                this.row = row;
                let vm = this;
                vm.showMessageBox('confirm', '删除', '确定要删除吗？', () => {
                    vm.ajax({
                        method: 'DELETE',
                        url: vm.$request.Boiler.FrontDelete.replace('{id}', this.row.id),
                        params: {
                            id: vm.row.id
                        },
                        success: function () {
                            vm.$refs.tableRef.loadContents();
                        }
                    });
                });
            },
            childClose: function () {
                this.$refs.tableRef.loadContents();
            },
            currentChange: function (currentRow, oldCurrentRow) {
                this.row = currentRow;
            },
            btnClick: function (method) {
                if (this[method]) {
                    this[method]();
                }
            }
        }
    }

</script>

<style scoped>
    .btn-my {
        color: #2d8cf0;
        font-size: 12px;
    }
</style>
