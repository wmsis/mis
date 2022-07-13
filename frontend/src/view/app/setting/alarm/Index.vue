<template>
    <div v-loading="loading">
        <limit-update :shows="shows"
                      :limit="row"
                      :moduleList="moduleList"
                      v-on:listenChildClose="childClose">
        </limit-update>
        <tag-assign :shows="shows"
                      :moduleList="moduleList"
                      :rows="rows"
                      v-on:listenChildClose="childClose">
        </tag-assign>
        <tag-group :shows="shows"
                    :rows="rows"
                    v-on:listenChildClose="childClose">
        </tag-group>
        <base-table ref="tableRef"
                    v-if="init"
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
                <FormItem label="模块名称">
                    <Select placeholder="请选择模块" v-model="searchForm.moduleId">
                        <Option value="" key="all">所有模块</Option>
                        <Option v-for="item in moduleList" :value="item.id" :key="item.id">{{ item.name }}</Option>
                    </Select>
                </FormItem>
            </div>
            <template slot="module" slot-scope="{ row }">
                <div>{{slotModule(row)}}</div>
            </template>
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
    import tagAssign from './Assign.vue';
    import tagGroup from './Group.vue';

    export default {
        name: "AlarmSetting",
        components: {
            'base-table': BaseTable,
            'limit-update': limitUpdate,
            'tag-assign': tagAssign,
            'tag-group': tagGroup
        },
        data: function () {
            return {
                shows: {
                    search: false,
                    update: false,
                    assign: false,
                    group: false
                },
                pageUrl: this.$request.setting.limitload,
                searchForm: {
                    searchTagName: '',
                    moduleId: ''
                },
                row: {},
                rows: [],
                buttons: [{
                    name: '绑定模块',
                    icon: 'ios-hammer',
                    method: 'assign',
                    api_name: 'user-insert'
                },{
                    name: 'TAG分组',
                    icon: 'md-git-network',
                    method: 'group',
                    api_name: 'user-insert'
                }],
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
                        title: '所属模块',
                        ellipsis: true,
                        tooltip: true,
                        slot: 'module'
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
                moduleList: [],
                init: false,
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
            assign(){
                if (this.rows.length == 0) {
                    this.showMessage('请选择要绑定的Tag');
                    return false;
                }
                this.shows.assign = true;
            },
            group(){
                if (this.rows.length == 0) {
                    this.showMessage('请选择要分组的Tag');
                    return false;
                }
                this.shows.group = true;
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
            modules(){
                let vm = this;
                vm.init = false;
                vm.loading = true;
                vm.ajax({
                    method: 'get',
                    params: {
                        num: 1000,
                        page: 1
                    },
                    url: vm.$request.setting.pagemodule,
                    success: (data) => {
                        vm.moduleList = data.data;
                        vm.init = true;
                        vm.loading = false;
                    },
                    fail(){
                        vm.init = true;
                        vm.loading = false;
                    }
                });
            },
            slotModule(row){
                let name = '';
                if(row.historian_module_id) {
                    for (let i=0; i<this.moduleList.length; i++) {
                        if (this.moduleList[i].id == row.historian_module_id) {
                            name = this.moduleList[i].name;
                            break;
                        }
                    }
                }
                return name;
            }
        },
        mounted(){
            let mid = this.$route.query.mid;
            if(mid){
                this.searchForm.moduleId = mid;
            }
            this.modules();
        }
    }
</script>

<style scoped>
    .btn-my span {
        color: #2d8cf0;
        font-size: 12px;
    }
</style>
