<!--role table-->
<template>
    <div>
        <!--新增-->
        <add :shows="shows"
             v-on:listenChildClose="childClose">
        </add>

        <!--修改-->
        <update :shows="shows"
             :picTxt="row"
             v-on:listenChildClose="childClose">
        </update>

        <!--素材-->
        <material :showsParent="shows"
             :picTxtId="row.id"
             v-on:listenChildClose="childClose">
        </material>

        <div class="tips">图文消息个数；当用户发送文本、图片、语音、视频、图文、地理位置这六种消息时，只能回复1条图文消息；其余场景最多可回复8条图文消息</div>

        <base-table ref="tableRef"
                    v-bind:columns="columns"
                    v-bind:pageUrl="pageUrl"
                    v-bind:searchForm="searchForm"
                    v-bind:shows="shows"
                    v-bind:buttons="buttons"
                    :height="true"
                    v-bind:showSearchBtn="isButtonExist('pictxt-search')"
                    v-on:listenBtnClick="btnClick"
                    v-on:listenCurrentChange="currentChange">
            <div slot="searchFormSlot">
                <FormItem label="关键字" label-position="top">
                    <Input v-model="searchForm.search" placeholder="请输入关键字"/>
                </FormItem>
            </div>
            <template slot="option" slot-scope="{ row }">
                <div>
                    <Button type="primary" size="small" style="margin-right: 5px;" @click.stop="update(row)" v-if="isButtonExist('pictxt-update')">修改</Button>
                    <Button type="info" size="small" @click.stop="toMaterial(row)" v-if="isButtonExist('material-list')">素材管理</Button>
                    <Button type="error" size="small" @click.stop="remove(row)" v-if="isButtonExist('pictxt-delete')">删除</Button>
                </div>
            </template>
        </base-table>
    </div>
</template>
<script>
    import Vue from 'vue';
    import {
        FormItem,
        Input
    } from 'iview';

    Vue.component('FormItem', FormItem);
    Vue.component('Input', Input);

    import BaseTable from '@/components/baseTable'
    import Add from './Add.vue'
    import Update from './Update.vue'
    import Material from '../material/Index.vue'

    export default {
        components: {
            'base-table': BaseTable,
            'add': Add,
            'update': Update,
            'material': Material
        },
        data: function() {
            return {
                shows: {
                    search: false,
                    add: false,
                    update: false,
                    material: false
                },
                columns: [
                    {
                        title: '图文名称',
                        key: 'name'
                    },
                    {
                        title: '创建时间',
                        key: 'created_at',
                        width: 220
                    },
                    {
                        title: '操作',
                        width: 220,
                        align: 'center',
                        slot: 'option'
                    }
                ],
                buttons: [{
                    name: '添加图文',
                    icon: 'md-add-circle',
                    method: 'add',
                    api_name: 'pictxt-insert'
                }],
                searchForm: {
                    search: ''
                },
                row: {},
                pageUrl: this.$request.picTxt.page
            }
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
            remove: function(row) {
                let vm = this;
                vm.showMessageBox('confirm', '删除', '确定要删除吗？', ()=>{
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.picTxt.delete,
                        params: {
                            id: row.id
                        },
                        success: function () {
                            vm.$refs.tableRef.loadContents();
                        }
                    });
                });
            },
            toMaterial: function(row) {
                this.row = row;
                this.shows.material = true;
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
<style lang="scss" scoped>
    .tips{
        position: absolute;
        left: 220px;
        top: 23px;
        font-size: 13px;
    }
</style>
