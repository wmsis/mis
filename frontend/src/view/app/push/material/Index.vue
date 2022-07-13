<template>
    <div>
        <!--商品大图-->
        <Modal title="大图"
               v-model="shows.bigImage"
               :footer-hide="true">
            <img :src="bigImageUrl" style="width: 100%">
        </Modal>

        <!--新增-->
        <material-add :shows="shows"
              :picTxtId="picTxtId"
              v-on:listenChildClose="childClose">
        </material-add>

        <!--修改-->
        <material-update :shows="shows"
             :material="row"
             v-on:listenChildClose="childClose">
        </material-update>
        <base-list ref="tableRef"
                   v-bind:columns="columns"
                   v-bind:pageUrl="pageUrl"
                   v-bind:lazyLoad="true"
                   v-bind:searchForm="searchForm"
                   v-bind:showSearchBtn="false"
                   v-bind:shows="shows"
                   v-bind:buttons="buttons"
                   v-on:listenBtnClick="btnClick"
                   v-on:listenCurrentChange="currentChange">
            <div slot="searchFormSlot">
                <FormItem label="广告标题" label-position="top">
                    <Input v-model="searchForm.search" placeholder="广告标题"/>
                </FormItem>
            </div>
        </base-list>
    </div>
</template>
<script>
    import Vue from 'vue';
    import { mapGetters, mapState } from 'vuex';
    import {
        Modal,
        FormItem,
        Input
    } from 'iview';

    Vue.component('Modal', Modal);
    Vue.component('FormItem', FormItem);
    Vue.component('Input', Input);

    import BaseList from '@/components/baseList'
    import ImageButton from '@/components/imageButton'
    import MaterialAdd from './Add.vue'
    import MaterialUpdate from './Update.vue'

    export default {
        computed: {
            ...mapState([
                'baseURL',
                'host'
            ])
        },
        components: {
            'base-list': BaseList,
            'image-button': ImageButton,
            'material-add': MaterialAdd,
            'material-update': MaterialUpdate
        },
        data: function() {
            let vm = this;
            return {
                shows: {
                    search: false,
                    add: false,
                    update: false,
                    bigImage: false
                },
                picTxtId: '',
                bigImageUrl: '',
                columns: [
                    {
                        title: '图片',
                        align: 'center',
                        width: 115,
                        render: function (h, {row, column, index}) {
                            return h(ImageButton, {
                                props: {
                                    imageUrl: vm.host + row.img,
                                    width: '80px',
                                    height: '80px'
                                },
                                on: {
                                    onImageClick: function () {
                                        vm.bigImageUrl = vm.host + row.img;
                                        vm.shows.bigImage = true;
                                    }
                                },
                            });
                        }
                    },
                    {
                        title: '链接(点击复制)',
                        align: 'center',
                        key: 'url',
                        minWidth: 150
                    },
                    {
                        title: '标题',
                        key: 'title',
                        minWidth: 150
                    },
                    {
                        title: '创建时间',
                        key: 'created_at',
                        align: 'center',
                        minWidth: 175
                    },
                    {
                        title: '类型',
                        key: 'type',
                        minWidth: 70,
                        align: 'center',
                        render: function (h, {row, column, index}) {
                            if (row.type == 'pictxt') {
                                return h('div', {style:{}}, '图文');
                            } else {
                                return h('div', {style:{}}, '');
                            }
                        }
                    },
                    // {
                    //     title: '推送',
                    //     key: 'send_num',
                    //     align: 'center',
                    //     minWidth: 70
                    // },
                    // {
                    //     title: '已读',
                    //     key: 'read_num',
                    //     align: 'center',
                    //     minWidth: 70
                    // },
                    // {
                    //     title: '已购',
                    //     key: 'buy_num',
                    //     align: 'center',
                    //     minWidth: 70
                    // },
                    {
                        title: '操作',
                        minWidth: 150,
                        align: 'center',
                        render: (h, {row, column, index}) => {
                            return h('div', [
                                h('Button', {
                                    props: {
                                        type: 'primary',
                                        size: 'small'
                                    },
                                    style: {
                                        marginRight: '5px'
                                    },
                                    on: {
                                        click: () => {
                                            this.update(row)
                                        }
                                    }
                                }, '修改'),
                                h('Button', {
                                    props: {
                                        type: 'error',
                                        size: 'small'
                                    },
                                    on: {
                                        click: () => {
                                            this.delete(row)
                                        }
                                    }
                                }, '删除')
                            ]);
                        }
                    }
                ],
                buttons: [{
                    name: '新增素材',
                    icon: 'md-add-circle',
                    method: 'add'
                }],
                searchForm: {
                    search: ''
                },
                row: {},
                pageUrl: this.$request.material.list
            }
        },
        mounted: function() {
            this.picTxtId = this.$route.query.picTxtId;
            this.pageUrl = this.pageUrl.replace("{id}", this.picTxtId);
            this.$refs.tableRef.loadContents(1, this.pageUrl);
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
            delete: function(row) {
                let vm = this;
                vm.confirm({
                    content: '确定删除?',
                    onOk: function () {
                        vm.ajax({
                            method: 'POST',
                            url: vm.$request.material.delete,
                            params: {
                                id: row.id
                            },
                            successCallback: function () {
                                vm.$refs.tableRef.loadContents();
                            }
                        });
                    }
                });
            },
            resetPassword: function() {
                if (!this.row.id) {
                    this.error("请选择行.");
                    return;
                }

                let vm = this;
                vm.confirm({
                    content: '确定重置密码?',
                    onOk: function () {
                        vm.ajax({
                            method: 'POST',
                            url: vm.$request.admins.resetPassword,
                            params: {
                                idstring: vm.row.id
                            },
                            successCallback: function () {
                            }
                        });
                    }
                });
            },
            assignRole: function() {
                if (!this.row.id) {
                    this.error("请选择行.");
                    return;
                }
                this.shows.assign = true;
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
<style lang="scss">
</style>
