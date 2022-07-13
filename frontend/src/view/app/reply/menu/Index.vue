<template>
    <div class="diy-page">
        <!--新增-->
        <wechat-menu-add :shows="shows"
            v-on:listenChildClose="childClose">
        </wechat-menu-add>
        <!--修改-->
        <wechat-menu-update :shows="shows"
            :wechat-menu="row"
            v-on:listenChildClose="childClose">
        </wechat-menu-update>

        <div class="opetate">
            <ButtonGroup :style="{'margin': '0px 0px 10px 0px'}">
                <Button type="primary" icon="md-add-circle" @click="add" v-if="isButtonExist('menu-insert')">添加菜单</Button>
                <Button type="primary" icon="md-checkmark" @click="publish" v-if="isButtonExist('menu-publish')">发布</Button>
            </ButtonGroup>
        </div>
        <div class="content">
            <div class="tb">
                <Table ref="tb"
                       :loading="loading"
                       :columns="columns"
                       :data="data"
                       :highlight-row="true"
                       border
                       stripe>
                    <template slot-scope="{ row, index }" slot="action">
                        <Button type="primary" size="small" style="margin-right: 5px;" @click="edit(row)" v-if="isButtonExist('menu-update')">编辑</Button>
                        <Button type="error" size="small" @click="remove(row)" v-if="isButtonExist('menu-delete')">删除</Button>
                    </template>
                    <template slot-scope="{ row, index }" slot="type">
                        <div>{{row.type == 'view' ? '连接' : (row.type == 'click' ? '事件' : '小程序')}}</div>
                    </template>
                    <template slot-scope="{ row, index }" slot="keyword">
                        <div class="prow" v-if="row.type == 'click'" :title="row.keyword">{{row.keyword}}</div>
                        <div class="prow" v-if="row.type == 'view'" :title="row.url">{{row.url}}</div>
                    </template>
                    <template slot-scope="{ row, index }" slot="page">
                        <div class="prow" v-if="row.type == 'miniprogram'" :title="row.pagepath">{{row.pagepath}}</div>
                    </template>
                </Table>
            </div>
            <div class="duihua">
                <Modal
                    v-model="modal"
                    title="添加菜单"
                    @on-ok="ok"
                    @on-cancel="cancel">
                    <div class="m-content">
                        <div class="select">
                            <div class="left">级别</div>
                            <div class="right">
                                <Select v-model="type" class="sel">
                                    <Option v-for="item in optList" :value="item.value" :key="item.value">{{ item.label }}</Option>
                                </Select>
                            </div>
                        </div>
                        <div class="name">
                            <div class="left">名称</div>
                            <div class="right">
                                <Input v-model="name" class="input" placeholder="请输入名称" />
                            </div>
                        </div>
                        <div class="name">
                            <div class="left">排序</div>
                            <div class="right">
                                <Input v-model="name" class="input" placeholder="请输入排序" />
                            </div>
                        </div>
                        <div class="select">
                            <div class="left">类型</div>
                            <div class="right">
                                <Select v-model="type" class="sel">
                                    <Option v-for="item in optList" :value="item.value" :key="item.value">{{ item.label }}</Option>
                                </Select>
                            </div>
                        </div>
                        <div class="name">
                            <div class="left">链接</div>
                            <div class="right">
                                <Input v-model="name" class="input" placeholder="请输入链接" />
                            </div>
                        </div>
                        <div class="name">
                            <div class="left">小程序链接</div>
                            <div class="right">
                                <Input v-model="name" class="input" placeholder="请输入小程序链接" />
                            </div>
                        </div>
                    </div>
                </Modal>
            </div>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue';
    import { mapGetters, mapState } from 'vuex'
    import {
        Select,
        Option,
        Input,
        Button,
        ButtonGroup,
        Table,
        Modal
    } from 'iview';

    import {
        Loading
    } from 'element-ui';

    Vue.component('Select', Select);
    Vue.component('Option', Option);
    Vue.component('Input', Input);
    Vue.component('Button', Button);
    Vue.component('ButtonGroup', ButtonGroup);
    Vue.component('Table', Table);
    Vue.component('Modal', Modal);
    Vue.use(Loading);

    import expandRow from './ExpandRow.vue';
    import WechatMenuAdd from './Add.vue'
    import WechatMenuUpdate from './Update.vue'

    export default {
        computed: {
            ...mapState([
                'baseURL',
                'token'
            ])
        },
        components: {
            'expandRow': expandRow,
            'wechat-menu-add': WechatMenuAdd,
            'wechat-menu-update': WechatMenuUpdate
        },
        data() {
            const vm = this;
            return {
                loading: false,
                shows: {
                    add: false,
                    update: false
                },
                columns: [
                    {
                        title: '展开',
                        key: 'index',
                        type: 'expand',
                        width: 84,
                        render: (h, params) => {
                            return h(expandRow, {
                                props: {
                                    children: vm.childrenMenuMap[params.row.id]
                                },
                                on: {
                                    listenRemove: vm.remove,
                                    listenUpdate: vm.edit
                                }
                            })
                        }
                    },
                    {
                        title: '名称',
                        key: 'name',
                    },
                    {
                        title: '关键字/链接',
                        slot: 'keyword'
                    },
                    {
                        title: '小程序链接',
                        slot: 'page'
                    },
                    {
                        title: '排序',
                        width: 84,
                        key: 'sort'
                    },
                    {
                        title: '类型',
                        width: 84,
                        slot: 'type'
                    },
                    {
                        title: '操作',
                        slot: 'action',
                        align: 'center'
                    }
                ],
                data: [
                ],
                modal: false,
                update: false,
                name: '',
                type: 'txt',
                childrenMenuMap: {

                },
                optList: [
                    {
                        value: 'txt',
                        label: '文本'
                    },
                    {
                        value: 'pictxt',
                        label: '图文'
                    },
                    {
                        value: 'pic',
                        label: '图片'
                    }
                ],
                textarea: '',
                pictext: '',
                img_url: '',
                row: {},
                uploadUrl: '',
            }
        },
        mounted: function() {
            this.uploadUrl = this.baseURL + '/goodscategory/upload?access_token=' + this.token.access_token;
            this.loadContents();
        },
        methods:{
            loadContents: function() {
                const vm = this;
                vm.loading = true;
                vm.ajax({
                    method: 'GET',
                    url: this.$request.wechatMenu.list,
                    success: function (data) {
                        vm.data = [];
                        vm.childrenMenuMap = [];
                        vm.loading = false;

                        for (let item of data) {
                            if (item.is_root) {
                                vm.data.push(item);
                            } else {
                                const path = item.path;
                                const parentId = path.substring(0, path.indexOf("/"));
                                if (vm.childrenMenuMap.hasOwnProperty(parentId)) {
                                    vm.childrenMenuMap[parentId].push(item);
                                } else {
                                    vm.childrenMenuMap[parentId] = [item];
                                }
                            }
                        }
                    },
                    fail(){
                        vm.loading = false;
                    }
                });
            },
            childClose: function() {
                this.loadContents();
            },
            add(){
                this.shows.add = true;
            },
            edit (row) {
                this.row = row;
                this.shows.update = true;
            },
            publish: function() {
                const vm = this;
                vm.ajax({
                    method: 'GET',
                    url: vm.$request.wechatMenu.publish,
                    params: {
                    },
                    success: function () {
                        vm.showMessage('操作成功', 'success');
                    }
                });
            },
            remove (row) {
                let vm = this;
                vm.showMessageBox('confirm', '删除', '确定要删除吗？', ()=>{
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.wechatMenu.delete,
                        params: {
                            id: row.id
                        },
                        success: function () {
                            vm.loadContents();
                        }
                    });
                });
            },
            ok () {

            },
            cancel () {

            },
            release(){

            }
        }
    }
</script>
<style scoped  lang="scss">
    @import '../../../../assets/scss/base/mixins';
    @import '../../../../assets/scss/base/placeholder';

    .diy-page {
        height: 100%;
        overflow: auto;
        position: relative;
        & .opetate{
            height: 50px;
        }
        & .content{
            height: calc(100% - 50px);
            & .pager{
                padding-top: 15px;
                text-align: right;
            }
            & .duihua{

            }
            & /deep/ .ivu-table-cell{
                padding-left: 8px;
                padding-right: 8px;
            }
            & /deep/ .ivu-table-expanded-cell{
                padding: 20px 0;
            }
            & .prow{
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        }
    }
    .name, .select{
        margin-bottom: 20px;
        @extend %flex-row;
        & .left{
            width: 90px;
            height: 32px;
            line-height: 32px;
            font-weight: bold;
        }
        & .right{
            flex: 1;
            & .input{
                width: 100%;
            }
            & .sel {
                width: 100%;
                & /deep/ .ivu-select-selection{
                    width: 100%;
                }
                & .ivu-select-item {
                    padding: 10px 15px;
                }
            }
        }
    }
</style>
