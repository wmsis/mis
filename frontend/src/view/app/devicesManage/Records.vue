<template>
    <div>
        <!--新增-->
        <rec-add :shows="shows" :device-id="devid"
                 v-on:listenChildClose="childClose">
        </rec-add>
        <!--        修改-->
        <rec-update :shows="shows"
                    :dev="row"
                    v-on:listenChildClose="childClose">
        </rec-update>

        <base-table ref="tableRef"
                    v-bind:columns="columns"
                    v-bind:pageUrl="pageUrl"
                    v-bind:searchForm="searchForm"
                    v-bind:shows="shows"
                    v-bind:buttons="buttons"
                    :height="true"
                    v-bind:showSearchBtn="isButtonExist('user-search')"
                    v-on:listenBtnClick="btnClick"
                    v-on:listenCurrentChange="currentChange"
                    v-if="loadStatus">
            <div slot="searchFormSlot">
                <FormItem label="设备名称" label-position="top">
                    <Input v-model="searchForm.name" placeholder="设备名称"/>
                </FormItem>
            </div>
            <template slot="kind" slot-scope="{ row }">
                <div>{{row.kind == 'general' ? '一类缺陷' : row.kind == 'worse' ? '二类缺陷' : row.kind == 'serious' ? '三类缺陷' :
                    ''}}
                </div>
            </template>
            <template slot="option" slot-scope="{ row }">
                <div>
                    <Button type="text" size="small" class="btn-my" @click.stop="update(row)"
                            v-if="isButtonExist('rec-update')">查看/修改
                    </Button>
                    <Button type="text" size="small" class="btn-my" @click.stop="remove(row)"
                            v-if="isButtonExist('rec-delete')">删除
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
        Button
    } from 'iview';

    Vue.component('FormItem', FormItem);
    Vue.component('Input', Input);
    Vue.component('Button', Button);

    import BaseTable from '@/components/baseTable'
    import RecordAdd from './Record-Add.vue'
    import RecordUpdate from './Record-Update.vue'


    export default {
        components: {
            'base-table': BaseTable,
            'rec-add': RecordAdd,
            'rec-update': RecordUpdate
        },
        data: function () {
            return {
                shows: {
                    search: false,
                    add: false,
                    update: false
                },
                columns: [
                    {
                        type: 'index',
                        title: '序号',
                        align: 'center',
                        minWidth: 50
                    },
                    {
                        title: '检修日期',
                        key: 'date',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 120
                    },
                    {
                        title: '检修性质',
                        slot: 'kind',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 120
                    },
                    {
                        title: '负责人',
                        key: 'supervisor',
                        minWidth: 120
                    },
                    {
                        title: '记录人',
                        key: 'recorder',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 120
                    },
                    {
                        title: '记录时间',
                        key: 'record_time',
                        minWidth: 150
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
                    api_name: 'rec-insert'
                }],
                searchForm: {
                    equipment_id: ''
                },
                row: {},
                pageUrl: '',
                devid: '',
                loadStatus: false
            }
        },
        methods: {
            showSearchDrawer: function () {
                this.shows.search = true;
            },
            btnClick: function (method) {
                if (this[method]) {
                    this[method]();
                }
            },
            add: function () {
                this.shows.add = true;
            },
            update: function (row) {
                this.row = row;
                this.shows.update = true;
            },
            remove: function (row) {
                let that = this;
                that.row = row;

                that.showMessageBox('confirm', '删除数据', '确定要删除吗？', () => {
                    that.ajax({
                        method: 'DELETE',
                        url: that.$request.equipments.equipmentRecordDelete.replace('{equipment_id}', that.devid).replace('{id}', that.row.id),
                        params: {
                            equipment_id: that.devid,
                            id: that.row.id
                        },
                        success: function (data) {
                            that.$refs.tableRef.loadContents();
                        }
                    });
                });
            },
            childClose: function () {
                this.$refs.tableRef.loadContents();
            },
            currentChange: function (currentRow, oldCurrentRow) {
                this.row = currentRow;
            }
        },
        mounted() {
            let devid = this.$route.params.devid;
            this.devid = devid;
            this.pageUrl = this.$request.equipments.equipmentRecordList.replace('{equipment_id}', this.devid);
            this.searchForm.equipment_id = this.devid;
            this.loadStatus = true;
        }
    }
</script>
<style>
    .btn-my span {
        color: #2d8cf0;
        font-size: 12px;
    }
</style>
