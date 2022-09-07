<!--role table-->
<template>
    <div>
        <!--新增-->
        <role-add :shows="shows"
                  v-on:listenChildClose="childClose">
        </role-add>

        <!--修改-->
        <role-update :shows="shows"
                  :role="row"
                  v-on:listenChildClose="childClose">
        </role-update>

        <assign-permission :shows="shows"
                           :role="row"
                           v-on:listenChildClose="childClose">
        </assign-permission>

        <assign-api :shows="shows"
                           :role="row"
                           v-on:listenChildClose="childClose">
        </assign-api>

        <base-table ref="tableRef"
                    v-bind:columns="columns"
                    v-bind:pageUrl="pageUrl"
                    v-bind:searchForm="searchForm"
                    v-bind:shows="shows"
                    v-bind:buttons="buttons"
                    :height="true"
                    v-bind:showSearchBtn="isButtonExist('role-search')"
                    v-on:listenBtnClick="btnClick"
                    v-on:listenCurrentChange="currentChange">
            <div slot="searchFormSlot">
                <FormItem label="角色姓名" label-position="top">
                    <Input v-model="searchForm.search" placeholder="请输入角色姓名"/>
                </FormItem>
            </div>
            <template slot="option" slot-scope="{ row }">
                <div>
                    <Button  type="text" :disabled="row.type=='admin' ? true : false" size="small" class="btn-my" @click.stop="update(row)" v-if="isButtonExist('role-update')">修改</Button>
                    <Button  type="text" :disabled="row.type=='admin' ? true : false" size="small" class="btn-my" @click.stop="remove(row)" v-if="isButtonExist('role-delete')">删除</Button>
                    <Button  type="text" :disabled="row.type=='admin' ? true : false" size="small" class="btn-my" @click.stop="assign(row)" v-if="isButtonExist('role-assign-permission')">菜单权限</Button>
                    <Button  type="text" :disabled="row.type=='admin' ? true : false" size="small" class="btn-my" @click.stop="api(row)" v-if="isButtonExist('role-assign-api')">接口权限</Button>
                </div>
            </template>
        </base-table>
    </div>
</template>
<script>
    import Vue from 'vue'
    import {
        FormItem,
        Input,
        Button
    } from 'iview';

    Vue.component('FormItem', FormItem);
    Vue.component('Input', Input);
    Vue.component('Button', Button);

    import BaseTable from '@/components/baseTable'
    import RoleAdd from './Add.vue'
    import RoleUpdate from './Update.vue'
    import AssignPermission from './AssignPermission.vue'
    import AssignApi from './AssignApi.vue'

    export default {
        components: {
            'base-table': BaseTable,
            'role-add': RoleAdd,
            'role-update': RoleUpdate,
            'assign-permission': AssignPermission,
            'assign-api': AssignApi
        },
        data: function() {
            return {
                shows: {
                    search: false,
                    add: false,
                    update: false,
                    permission: false,
                    api: false
                },
                columns: [
                    {
                        type: 'index',
                        title: '序号',
                        align: 'center',
                        width: 50
                    },
                    {
                        title: '角色名称',
                        key: 'name',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 120
                    },
                    {
                        title: '描述',
                        key: 'desc',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 180
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
                        minWidth: 240
                    }
                ],
                buttons: [{
                    name: '新增',
                    icon: 'md-add-circle',
                    method: 'add',
                    api_name: 'role-insert'
                }],
                searchForm: {
                    search: ''
                },
                row: {},
                pageUrl: this.$request.roles.page
            }
        },
        mounted: function() {
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
                this.row = row;

                let vm = this;
                vm.showMessageBox('confirm', '删除', '确定要删除吗？', ()=>{
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.roles.delete,
                        params: {
                            id: vm.row.id
                        },
                        successCallback: function () {
                            vm.$refs.tableRef.loadContents();
                        }
                    });
                });
            },
            assign(row){
                this.row = row;
                this.shows.permission = true;
            },
            api(row){
                this.row = row;
                this.shows.api = true;
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
    .btn-my span{
        color: #2d8cf0;
        font-size: 12px;
    }
    .btn-my[disabled] span{
        color: #c5c8ce;
        background-color: #fff;
        border-color: transparent;
    }
</style>
