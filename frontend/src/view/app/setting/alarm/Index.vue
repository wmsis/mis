<template>
    <div v-loading="loading">
        <limit-update :shows="shows"
                      :limit="row"
                      v-on:listenChildClose="childClose">
        </limit-update>
        <base-table ref="tableRef"
                    v-bind:columns="columns"
                    v-bind:pageUrl="pageUrl"
                    v-bind:searchForm="searchForm"
                    v-bind:shows="shows"
                    v-bind:buttons="buttons"
                    :height="true"
                    @selectionChange="selectionChange"
                    v-bind:showSearchBtn="isButtonExist('tag-search')"
                    v-on:listenBtnClick="btnClick"
                    v-on:listenCurrentChange="currentChange">
            <div slot="searchFormSlot">
                <FormItem label="标签名称">
                    <Input v-model="searchForm.searchTagName" placeholder="标签名称"/>
                </FormItem>
            </div>
            <template slot="option" slot-scope="{ row }">
                <div>
                    <Button type="text" size="small" class="btn-my"
                            @click.stop="update(row)" v-if="isButtonExist('limit-update')">修改
                    </Button>
                </div>
            </template>
        </base-table>
    </div>

</template>

<script>
    import Vue from 'vue';
    import {
        Loading
    } from 'element-ui'
    import {
        FormItem,
        Input,
        Button,
        Select,
        Option
    } from 'iview';

    Vue.component('FormItem', FormItem);
    Vue.component('Input', Input);
    Vue.component('Button', Button);
    Vue.component('Select', Select);
    Vue.component('Option', Option);
    Vue.use(Loading);

    import BaseTable from '@/components/baseTable';
    import limitUpdate from './Update.vue';

    export default {
        name: "AlarmSetting",
        components: {
            'base-table': BaseTable,
            'limit-update': limitUpdate,
        },
        data: function () {
            return {
                shows: {
                    search: false,
                    update: false,
                    assign: false
                },
                pageUrl: this.$request.setting.limitload,
                searchForm: {
                    searchTagName: '',
                },
                row: {},
                rows: [],
                buttons: [],
                columns: [
                    {
                        type: 'selection',
                        width: 50,
                        align: 'center'
                    },
                    {
                        title: 'Tag序号',
                        key: 'id',
                        align: 'center',
                        width: 80
                    },
                    {
                        title: 'Tag名称',
                        key: 'tag_name',
                        ellipsis: true,
                        tooltip: true,
                        minWidth: 180
                    },
                    {
                        title: '别名',
                        key: 'alias',
                        ellipsis: true,
                        tooltip: true
                    },
                    {
                        title: '单位',
                        key: 'measure',
                        ellipsis: true,
                        tooltip: true,
                        width: 80
                    },
                    {
                      title: '原始上限值',
                      key: 'origin_upper_limit',
                      ellipsis: true,
                      tooltip: true
                    },
                    {
                      title: '原始下限值',
                      key: 'origin_lower_limit',
                      ellipsis: true,
                      tooltip: true
                    },
                    {
                        title: '警报上限值',
                        key: 'upper_limit',
                        ellipsis: true,
                        tooltip: true
                    },
                    {
                        title: '警报下限值',
                        key: 'lower_limit',
                        ellipsis: true,
                        tooltip: true
                    },
                    {
                        title: '操作',
                        slot: 'option',
                        align: 'center'
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
            selectionChange(rows) {
                this.rows = [...rows];
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
            },

        },
        mounted(){
            let mid = this.$route.query.mid;
            if(mid){
                this.searchForm.moduleId = mid;
            }
        }
    }
</script>

<style scoped>
    .btn-my span {
        color: #2d8cf0;
        font-size: 12px;
    }
</style>
