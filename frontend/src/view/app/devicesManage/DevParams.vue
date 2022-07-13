<template>
    <div>
        <!--新增-->
        <param-add :shows="shows" :device-id="devid"
                   v-on:listenChildClose="childClose">
        </param-add>
        <!--        修改-->
        <param-update :shows="shows"
                      :dev="row"
                      v-on:listenChildClose="childClose">
        </param-update>

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
                <FormItem label="参数名称" label-position="top">
                    <Input v-model="searchForm.name" placeholder="参数名称"/>
                </FormItem>
            </div>
            <template slot="option" slot-scope="{ row }">
                <div>
                    <Button type="text" size="small" class="btn-my" @click.stop="update(row)"
                            v-if="isButtonExist('par-update')">查看/修改
                    </Button>
                    <Button type="text" size="small" class="btn-my" @click.stop="remove(row)"
                            v-if="isButtonExist('par-delete')">删除
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
    import ParamsAdd from './Params-Add.vue'
    import ParamsUpdate from './Params-Update.vue'


    export default {
        components: {
            'base-table': BaseTable,
            'param-add': ParamsAdd,
            'param-update': ParamsUpdate
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
                        title: '参数名称',
                        key: 'name',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 120
                    },
                    {
                        title: '参数值',
                        key: 'value',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 120
                    },
                    {
                        title: '设备名称',
                        key: 'equipment_name',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 120
                    },
                    {
                        title: '设备型号',
                        key: 'equipment_model',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 120
                    },
                    {
                        title: '创建日期',
                        key: 'created_at',
                        minWidth: 160
                    },
                    {
                        title: '更新日期',
                        key: 'updated_at',
                        minWidth: 160
                    },
                    {
                        title: '操作',
                        slot: 'option',
                        align: 'center',
                        minWidth: 150
                    }
                ],
                buttons: [{
                    name: '新增',
                    icon: 'md-add-circle',
                    method: 'add',
                    api_name: 'par-insert'
                }],
                searchForm: {
                    equipment_id: '',
                    name: ''
                },
                row: {},
                pageUrl: '',
                devid: '',
                dev_name:'',
                dev_model:'',
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
                        url: that.$request.equipments.equipmentParamDelete.replace('{equipment_id}', that.devid).replace('{id}', that.row.id),
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
                this.row.dev_name = this.dev_name
                this.row.dev_model = this.dev_model
            }
        },
        mounted() {
            console.log('dev params view ....')
            let devid = this.$route.params.devid;
            this.devid = devid;
            this.pageUrl = this.$request.equipments.equipmentParamList.replace('{equipment_id}', this.devid);
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
