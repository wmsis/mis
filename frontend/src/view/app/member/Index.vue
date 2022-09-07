<!--member table-->
<template>
    <div>
        <base-table ref="tableRef"
                    :columns="columns"
                    :pageUrl="pageUrl"
                    :searchForm="searchForm"
                    :shows="shows"
                    :buttons="buttons"
                    :height="true"
                    :showSearchBtn="isButtonExist('member-search')"
                    v-on:listenBtnClick="btnClick"
                    v-on:listenCurrentChange="currentChange">
            <div slot="searchFormSlot">
                <FormItem label="会员名称" label-position="top">
                    <Input v-model="searchForm.name" placeholder="请输入会员姓名"/>
                </FormItem>
            </div>
            <template slot-scope="{ row, index }" slot="info">
                <div class="info">
                    <div class="img">
                        <img :src="row.wechats[0].headimgurl" v-if="row.wechats && row.wechats.length > 0"/>
                    </div>
                    <div class="nickname">{{row.username ? row.username : row.wechats && row.wechats.length > 0 ? row.wechats[0].nickname: ''}}</div>
                </div>
            </template>
            <template slot-scope="{ row, index }" slot="subscribe">
                <div class="subscribe" :class="row.wechats && row.wechats.length > 0 && row.wechats[0].subscribe ? 'green' : 'red'">{{row.wechats && row.wechats.length > 0 && row.wechats[0].subscribe ? '已关注' : '未关注'}}</div>
            </template>
            <template slot-scope="{ row, index }" slot="gender">
                <div class="gender">{{row.wechats && row.wechats.length > 0 ? ( row.wechats[0].gender == 'man' ? '男士' : '女士') : ''}}</div>
            </template>
        </base-table>
    </div>
</template>
<script>
    import Vue from 'vue';
    import { mapGetters, mapState } from 'vuex'
    import {
        FormItem,
        Input
    } from 'iview';
    Vue.component('FormItem', FormItem);
    Vue.component('Input', Input);

    import BaseTable from '@/components/baseTable'
    export default {
        components: {
            'base-table': BaseTable,
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
                        title: '用户信息',
                        key: 'username',
                        align: 'center',
                        slot: 'info',
                        minWidth: 120,
                        //fixed: 'left'
                    },
                    // {
                    //     title: '性别',
                    //     slot: 'gender',
                    //     align: 'center'
                    // },
                    {
                        title: '手机号码',
                        key: 'mobile',
                        minWidth: 96,
                        align: 'center'
                    },
                    {
                        title: '加入时间',
                        key: 'created_at',
                        minWidth: 180,
                        align: 'center'
                    },
                    {
                        title: '更新时间',
                        key: 'name',
                        align: 'center',
                        minWidth: 115,
                        render: function (h, {row, column, index}) {
                            if (row.updated_at) {
                                return h('div', {style:{}}, row.updated_at.substring(0, 16));
                            } else {
                                return h('div', {style:{}}, '');
                            }
                        }
                    },

                    {
                        title: '是否关注',
                        slot: 'subscribe',
                        align: 'center',
                        minWidth: 65
                    }
                ],
                buttons: [],
                searchForm: {
                    search: '',
                    num: 15
                },
                row: {},
                pageUrl: this.$request.member.page
            }
        },
        mounted() {

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
            detail: function() {

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
<style lang="scss">
    .info{
        display: flex;;
        flex-direction: row;
    }
    .info .img{
        height: 30px;
        width: 30px;
        margin-right: 5px;
    }
    .info img{
        height: 30px;
        width: 30px;
        display: block;
        border-radius: 2px;
    }
    .info .nickname{
        flex: 1;
        height: 30px;
        line-height: 30px;
        display: inline-block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space:nowrap;
        text-align: left;
    }
    .ivu-table-cell {
        padding-left: 8px;
        padding-right: 8px;
    }
    .subscribe.red{
        background-color: rgba(245,108,108,.1);
        border: 1px solid rgba(245,108,108,.2);
        color: #f56c6c;
        height: 20px;
        padding: 0 4px;
        line-height: 20px;
        border-radius: 4px;
        font-size: 12px;
        display: inline-block;
    }
    .subscribe.green{
        background-color: rgba(103,194,58,.1);
        border: 1px solid rgba(103,194,58,.2);
        color: #67c23a;
        height: 20px;
        padding: 0 4px;
        line-height: 20px;
        border-radius: 4px;
        font-size: 12px;
        display: inline-block;
    }
  .btn-my span {
        color: #2d8cf0;
        font-size: 12px;
    }
</style>
