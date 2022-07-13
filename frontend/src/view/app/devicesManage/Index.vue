<template>
    <div>
        <!--新增-->
        <dev-add :shows="shows"
                 v-on:listenChildClose="childClose">
        </dev-add>
        <!--        修改-->
        <dev-update :shows="shows"
                    :dev="row"
                    v-on:listenChildClose="childClose">
        </dev-update>

        <base-table ref="tableRef"
                    v-bind:columns="columns"
                    v-bind:pageUrl="pageUrl"
                    v-bind:searchForm="searchForm"
                    v-bind:shows="shows"
                    v-bind:buttons="buttons"
                    :height="true"
                    v-bind:showSearchBtn="isButtonExist('dev-search')"
                    v-on:listenBtnClick="btnClick"
                    v-on:listenCurrentChange="currentChange">
            <div slot="searchFormSlot">
                <FormItem label="设备名称" label-position="top">
                    <Input v-model="searchForm.name" placeholder="设备名称"/>
                </FormItem>
            </div>
            <template slot="status" slot-scope="{ row }">
                <div>{{row.status == 'active' ? '启用' : row.status == 'storage' ? '封存' : row.status == 'waste' ? '报废' :
                    ''}}
                </div>
            </template>
            <template slot="option" slot-scope="{ row }">
                <div>
                    <Button type="text" size="small" class="btn-my" @click.stop="devParams(row)"
                            v-if="isButtonExist('dev-params')">设备参数
                    </Button>
                    <Button type="text" size="small" class="btn-my" @click.stop="detail(row)"
                            v-if="isButtonExist('dev-history')">检修记录
                    </Button>
                    <Button type="text" size="small" class="btn-my" @click.stop="update(row)"
                            v-if="isButtonExist('dev-update')">修改
                    </Button>
                    <Button type="text" size="small" class="btn-my" @click.stop="remove(row)"
                            v-if="isButtonExist('dev-delete')">删除
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
    import DevAdd from './Add.vue'
    import DevUpdate from './Update.vue'


    export default {
        components: {
            'base-table': BaseTable,
            'dev-add': DevAdd,
            'dev-update': DevUpdate
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
                        title: '设备名称',
                        key: 'name',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 100
                    },
                    {
                        title: '设备型号',
                        key: 'model',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 80
                    },
                    {
                        title: '设备厂家',
                        key: 'manufacturer',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 80
                    },
                    {
                        title: '设备序号',
                        key: 'serial_number',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 100
                    },
                    {
                        title: '投产日期',
                        key: 'production_date',
                        minWidth: 100
                    },
                    {
                        title: '设备状态',
                        slot: 'status',
                        minWidth: 60
                    },
                    {
                        title: '责任人姓名',
                        key: 'charge_person_name',
                        minWidth: 80
                    },
                    {
                        title: '责任人电话',
                        key: 'charge_person_phone',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 100
                    },
                    {
                        title: '服役位置',
                        key: 'work_location',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 90
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
                    api_name: 'dev-add'
                }],
                searchForm: {
                    name: ''
                },
                row: {},
                pageUrl: this.$request.equipments.equipmentList
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
            devParams: function (row) {
                this.row = row;
                this.$router.push({
                    name: 'dev-params',
                    params: {
                        devid: this.row.id
                    }
                });
            },
            add: function () {
                this.shows.add = true;
            },
            detail: function (row) {
                this.row = row;
                this.$router.push({
                    name: 'dev-records',
                    params: {
                        devid: this.row.id
                    }
                });
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
                        url: that.$request.equipments.equipmentDelete.replace('{id}', that.row.id),
                        params: {
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
          console.log('dev index ...')
        }
    }
</script>
<style>
    .btn-my span {
        color: #2d8cf0;
        font-size: 12px;
    }
</style>
