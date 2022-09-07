<template>
    <Modal
            v-model="shows.permission"
            title="菜单权限"
            :width="width"
            :mask-closable="true"
            :footer-hide="true"
            @on-visible-change="visibleChange">
        <Tree ref="treeRef"
              v-if="!loading"
              show-checkbox
              :data="treeData"
              @on-select-change="selectChange"
              @on-check-change="checkChange"
              :style="{height: '400px', overflowY: 'auto'}">
        </Tree>
        <div :style="{textAlign: 'right', marginTop: '20px'}">
            <Button type="primary" @click="handleSelect">确定选择</Button>
        </div>
        <Spin v-if="loading" size="large" class="spin"></Spin>
    </Modal>
</template>

<script>
    import Vue from 'vue'
    import {
        Tree,
        Modal,
        Button,
        Spin
    } from 'iview';

    Vue.component('Tree', Tree);
    Vue.component('Modal', Modal);
    Vue.component('Button', Button);
    Vue.component('Spin', Spin);

    export default {
        props: ['shows', 'role'],
        data () {
            return {
                model: true,
                loading: true,
                width: '880px',
                treeData: [],
                rolePermissions: []
            }
        },
        methods: {
            visibleChange: function(visible) {
                if (visible) {
                    this.getRolePermission();
                } else {
                    this.shows.permission = false;
                }
            },
            getRolePermission() {
                let vm = this;
                vm.loading = true;
                let url = vm.$request.roles.permissionList.replace("{id}", this.role.id);
                vm.ajax({
                    url: url,
                    success: function (data) {
                        vm.rolePermissions = data.myPermissions;
                        vm.setTreeData()
                    }
                });
            },
            setTreeData() {
                let vm = this;
                vm.ajax({
                    url: vm.$request.permission.tree,
                    success: function (data) {
                        for(let item of data){
                            item.expand = true;
                            item.title = item.name;
                        }
                        vm.selectIds(data);
                        vm.treeData = data;
                        vm.loading = false;
                    }
                });
            },

            //选中我的接口菜单
            selectIds(lists){
                for(let i=0; i<lists.length; i++){
                    lists[i].title = lists[i].name;
                    for(let item of this.rolePermissions){
                        if(item.id == lists[i].id){
                            lists[i].checked = true;
                            break;
                        }
                    }
                    if(lists[i].children && lists[i].children.length > 0){
                        this.selectIds(lists[i].children);
                    }
                }
            },

            selectChange: function(items) {

            },
            checkChange(items){

            },
            //递归获取选中的id
            checkIds(lists){
                let ids = [];
                for(let i=0; i<lists.length; i++){
                    if(lists[i].checked){
                        ids.push(lists[i].id);
                    }
                    let child_ids = [];
                    if(lists[i].children && lists[i].children.length > 0){
                        child_ids = this.checkIds(lists[i].children);
                    }

                    ids = ids.concat(child_ids);
                }

                return ids;
            },
            handleSelect: function() {
                let vm = this;
                let lists = vm.treeData;
                let ids = this.checkIds(lists);

                let url = vm.$request.roles.savePermission.replace("{id}", this.role.id);
                vm.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        permissions: ids
                    },
                    success: function (data) {
                        vm.$emit("listenChildClose");
                        vm.shows.permission = false;
                    }
                });
            }
        }
    }
</script>

<style>

</style>
