<template>
    <div>
        <!--新增-->
        <user-add :shows="shows"
                  v-on:listenChildClose="childClose">
        </user-add>

        <!--修改-->
        <user-update :shows="shows"
                     :user="row"
                     v-on:listenChildClose="childClose">
        </user-update>

        <!--分配角色-->
        <assign-role :shows="shows"
                     :userId="row.id"
                     v-on:listenChildClose="childClose">
        </assign-role>

        <!--分配组织-->
        <assign-orgnization :shows="shows"
                     :userId="row.id"
                     v-on:listenChildClose="childClose">
        </assign-orgnization>

        <base-table ref="tableRef"
                    v-bind:columns="columns"
                    v-bind:pageUrl="pageUrl"
                    v-bind:searchForm="searchForm"
                    v-bind:shows="shows"
                    v-bind:buttons="buttons"
                    :height="true"
                    v-bind:showSearchBtn="isButtonExist('user-search')"
                    v-on:listenBtnClick="btnClick"
                    v-on:listenCurrentChange="currentChange">
            <div slot="searchFormSlot">
                <FormItem label="用户姓名" label-position="top">
                    <Input v-model="searchForm.search" placeholder="用户姓名"/>
                </FormItem>
            </div>
            <template slot="type" slot-scope="{ row }">
                <div v-if="row.type=='admin'">超级管理员</div>
                <div v-if="row.type=='group'">集团用户</div>
                <div v-if="row.type=='webmaster'">分站站长</div>
            </template>
            <template slot="option" slot-scope="{ row }">
                <div>
                    <Button  type="text" :disabled="row.type=='admin' ? true : false" size="small" class="btn-my" @click.stop="update(row)" v-if="isButtonExist('user-update')">修改</Button>
                    <Button  type="text" :disabled="row.type=='admin' ? true : false" size="small" class="btn-my" @click.stop="remove(row)" v-if="isButtonExist('user-delete')">删除</Button>
                    <Button  type="text" :disabled="row.type=='admin' ? true : false" size="small" class="btn-my" @click.stop="resetPassword(row)" v-if="isButtonExist('user-reset-passpord')">重置密码</Button>
                    <Button  type="text" :disabled="row.type=='admin' ? true : false" size="small" class="btn-my" @click.stop="assignRole(row)" v-if="isButtonExist('user-assign-role')">分配角色</Button>
                    <Button  type="text" :disabled="row.type=='admin' ? true : false" size="small" class="btn-my" @click.stop="assignOrgnization(row)" v-if="isButtonExist('user-assign-orgnization')">分配组织</Button>
                </div>
            </template>
        </base-table>

        <Spin v-if="childLoading" size="large" class="spin"></Spin>
    </div>
</template>
<script>
    import Vue from 'vue';
    import {
        FormItem,
        Input,
        Button,
        Spin
    } from 'iview';

    Vue.component('FormItem', FormItem);
    Vue.component('Input', Input);
    Vue.component('Button', Button);
    Vue.component('Spin', Spin);

    import BaseTable from '@/components/baseTable'
    import UserAdd from './Add.vue'
    import UserUpdate from './Update.vue'
    import AssignRole from './AssignRole.vue'
    import AssignOrgnization from './AssignOrgnization.vue'

    export default {
        components: {
            'base-table': BaseTable,
            'user-add': UserAdd,
            'user-update': UserUpdate,
            'assign-role': AssignRole,
            'assign-orgnization': AssignOrgnization
        },
        data: function() {
            return {
                childLoading: false,
                shows: {
                    search: false,
                    add: false,
                    update: false,
                    assign: false,
                    orgnization: false,
                },
                columns: [
                    {
                        type: 'index',
                        title: '序号',
                        align: 'center',
                        width: 50
                    },
                    {
                        title: '用户姓名',
                        key: 'name',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 120
                    },
                    {
                        title: '用户组织',
                        key: 'orgnization_name',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 100
                    },
                    {
                        title: '用户类型',
                        slot: 'type',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 100
                    },
                    {
                        title: '用户角色',
                        key: 'role_name',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 100
                    },
                    {
                        title: '手机号码',
                        key: 'mobile',
                        minWidth: 110
                    },
                    {
                        title: '是否启用',
                        key: 'isopen',
                        align: 'center',
                        minWidth: 80,
                        render: function (h, {row, column, index}) {
                            if (row.isopen) {
                                return h('div', {style:{'color':'green'}}, '是');
                            } else {
                                return h('div', {style:{'color':'red'}}, '否');
                            }
                        }
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
                        minWidth: 350
                    }
                ],
                buttons: [{
                    name: '新增',
                    icon: 'md-add-circle',
                    method: 'add',
                    api_name: 'user-insert'
                }],
                searchForm: {
                    search: '',
                    roleid: 'all'
                },
                row: {},
                pageUrl: this.$request.users.page
            }
        },
        methods: {
            showSearchDrawer: function () {
                this.shows.search = true;
            },
            childReady(data){
                let status = data.status;
                this.childLoading = false;
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
                    that.ajax({
                        method: 'POST',
                        url: that.$request.users.delete,
                        params: {
                            id: that.row.id
                        },
                        success: function (data) {
                            that.$refs.tableRef.loadContents();
                        }
                    });
                });
            },
            resetPassword: function(row) {
                let that = this;
                that.row = row;

                that.showMessageBox('confirm', '重置密码', '确定重置密码吗？', ()=>{
                    that.ajax({
                        method: 'POST',
                        url: that.$request.users.resetPassword,
                        params: {
                            idstring: that.row.id
                        },
                        success: function (data) {
                            that.showMessage('操作成功', 'success');
                        }
                    });
                });
            },
            assignRole: function(row) {
                this.row = row;
                this.shows.assign = true;
            },
            assignOrgnization: function(row) {
                this.row = row;
                this.shows.orgnization = true;
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
    .spin {
        position: absolute;
        left: 50%;
        top: 50%;
    }
</style>
