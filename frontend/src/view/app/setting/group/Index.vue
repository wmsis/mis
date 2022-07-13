<template>
    <div>
        <!--新增-->
        <user-add :shows="shows"
                  v-on:listenChildClose="childClose">
        </user-add>

        <!--修改-->
        <user-update :shows="shows"
                     :row="row"
                     v-on:listenChildClose="childClose">
        </user-update>

        <base-table ref="tableRef"
                    v-bind:columns="columns"
                    v-bind:pageUrl="pageUrl"
                    v-bind:searchForm="searchForm"
                    v-bind:shows="shows"
                    v-bind:buttons="buttons"
                    :height="true"
                    v-bind:showSearchBtn="isButtonExist('group-search')"
                    v-on:listenBtnClick="btnClick"
                    v-on:listenCurrentChange="currentChange">
            <div slot="searchFormSlot">
                <FormItem label="分组名称" label-position="top">
                    <Input v-model="searchForm.name" placeholder="分组名称"/>
                </FormItem>
            </div>
            <template slot="option" slot-scope="{ row }">
                <div>
                    <Button  type="text" size="small" class="btn-my" @click.stop="update(row)" v-if="isButtonExist('group-update')">修改</Button>
                    <Button  type="text" size="small" class="btn-my" @click.stop="remove(row)" v-if="isButtonExist('group-delete')">删除</Button>
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
    import UserAdd from './Add.vue'
    import UserUpdate from './Update.vue'

    export default {
        components: {
            'base-table': BaseTable,
            'user-add': UserAdd,
            'user-update': UserUpdate
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
                        type: 'index',
                        width: 60,
                        align: 'center'
                    },
                    {
                        title: '模块名称',
                        key: 'name',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 120
                    },
                    {
                        title: '模块描述',
                        key: 'description',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 200
                    },
                    {
                        title: '创建时间',
                        key: 'created_at',
                        minWidth: 160
                    },
                    {
                        title: '操作',
                        slot: 'option',
                        align: 'center',
                        minWidth: 160
                    }
                ],
                buttons: [{
                    name: '新增',
                    icon: 'md-add-circle',
                    method: 'add',
                    api_name: 'group-add'
                }],
                searchForm: {
                    name: ''
                },
                row: {},
                pageUrl: this.$request.group.index
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
                let that = this;
                that.row = row;

                that.showMessageBox('confirm', '删除数据', '确定要删除吗？', ()=>{
                    let url = that.$request.group.destroy.replace("{id}", that.row.id);
                    that.ajax({
                        method: 'DELETE',
                        url: url,
                        params: {
                            id: that.row.id
                        },
                        success: function (data) {
                            that.$refs.tableRef.loadContents();
                        }
                    });
                });
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
    .btn-my span{
        color: #2d8cf0;
        font-size: 12px;
    }
</style>
