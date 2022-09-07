<template>
    <div>
        <BaseTable
                ref="tableRef"
                :columns="columns"
                :pageUrl="pageUrl"
                :shows="shows"
                :searchForm="searchForm"
                :height="true"
                v-bind:showSearchBtn="isButtonExist('user-message-search')"
                @selectionChange="selectionChange"
                @listenBtnClick="btnClick"
                :buttons="buttons">
            <template slot="info" slot-scope="{ row }">
                <div class="wx-info">
                    <img :src="row.member && row.member.wechats.length > 0 ? row.member.wechats[0].headimgurl : defalutImg"  width="50" height="50"/>
                    <span>{{row.member && row.member.username ? row.member.username : row.member && row.member.wechats.length > 0 ? row.member.wechats[0].nickname : '无名'}}</span>
                </div>
            </template>
            <template slot="option" slot-scope="{row}">
                <div>
                    <Button size="small" type="text" style="color: #2d8cf0" @click.stop="onClickQueue(row)" v-if="isButtonExist('user-message-delete')">删除</Button>
                </div>
            </template>
            <template slot="status" slot-scope="{ row }">
                <div>
                    <Tag color="green" v-if="row.status == 'success'" size="medium">执行成功</Tag>
                    <Tag color="red" v-else size="medium">执行失败</Tag>
                </div>
            </template>
            <template slot="name" slot-scope="{ row }">
                <div>{{row.type=='text' ? row.text : row.name}}</div>
            </template>
            <div slot="searchFormSlot">
                <div slot="searchFormSlot">
                    <FormItem label="消息标题" label-position="top">
                        <Input v-model="searchForm.name"  placeholder="请输入消息标题" clearable/>
                    </FormItem>
                </div>
            </div>
        </BaseTable>
    </div>
</template>
<script>
    import Vue from 'vue';
    import {
        Input,
        FormItem,
        Button,
        Tag
    } from 'iview';

    Vue.component('Input', Input);
    Vue.component('FormItem', FormItem);
    Vue.component('Button', Button);
    Vue.component('Tag', Tag);

    import BaseTable from '@/components/baseTable';

    export default {
        components: { BaseTable },
        data() {
            return {
                defalutImg: require('@/assets/img/default@2x.jpg'),
                searchForm: {
                    name: '',
                },
                pageUrl: this.$request.picTxt.queue,
                shows: {
                    search: false,
                    add: false,
                    update: false,
                    bind: false
                },
                rows: [],
                buttons: [
                    {
                        name: '批量删除',
                        icon: 'md-trash',
                        method: 'deleteBatch',
                        api_name: 'user-message-batchdelete'
                    }
                ],
                columns: [
                    {
                        type: 'selection',
                        width: 36
                    },
                    {
                        title: '用户信息',
                        slot: 'info',
                        minWidth: 150
                    },
                    {
                        title: '消息标题',
                        slot: 'name',
                        minWidth: 150,
                        ellipsis: true
                    },
                    {
                        title: '执行情况',
                        slot: 'status',
                        minWidth: 98
                    },
                    {
                        title: '状态',
                        key: 'result',
                        minWidth: 200
                    },
                    {
                        title: '加入队列时间',
                        key: 'created_at',
                        minWidth: 160
                    },
                    {
                        title: '处理队列时间',
                        key: 'updated_at',
                        minWidth: 160
                    },
                    {
                        title: '操作',
                        slot: 'option',
                        minWidth: 60,
                    }
                ]
            }
        },
        methods: {
            btnClick(method) {
                if (this[method]) {
                    this[method]();
                }
            },
            deleteBatch() {
                if (this.rows.length == 0) {
                    return this.showMessage('请选择要删除的消息');
                }
                var ids = this.rows.map((row) => row.id).join(',');
                this.ajax({
                    method: 'post',
                    data: { idstring: ids },
                    url: this.$request.picTxt.batchDelQueue,
                    success: (data) => {
                        this.$refs.tableRef.loadContents();
                    }
                });
            },
            selectionChange(rows) {
                this.rows = [...rows];
            },
            onClickQueue(row) {
                this.ajax({
                    method: 'post',
                    data: { id: row.id },
                    url: this.$request.picTxt.delQueue,
                    success: (data) => {
                        this.$refs.tableRef.loadContents();
                    }
                });
            }
        }
    }
</script>
<style scoped  lang="scss">
    .wx-info {
        padding: 8px 0;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        & img{
            display: inline-block;
            height: 30px;
            width: 30px;
            border-radius: 3px;
        }
        span {
            margin-left: 5px;
        }
    }
</style>
