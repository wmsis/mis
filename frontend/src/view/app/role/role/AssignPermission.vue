<template>
    <Modal
            v-model="shows.permission"
            title="分配权限"
            :width="width"
            :mask-closable="false"
            :footer-hide="true"
            @on-visible-change="visibleChange">
        <Tree ref="treeRef"
              v-if="loading"
              show-checkbox
              :data="treeData"
              @on-select-change="selectChange"
              @on-check-change="checkChange"
              :style="{height: '400px', overflowY: 'auto'}">
        </Tree>
        <div :style="{textAlign: 'right', marginTop: '20px'}">
            <Button type="primary" @click="handleSelect">确定选择</Button>
        </div>
    </Modal>
</template>

<script>
    import Vue from 'vue'
    import {
        Tree,
        Modal,
        Button
    } from 'iview';

    Vue.component('Tree', Tree);
    Vue.component('Modal', Modal);
    Vue.component('Button', Button);

    export default {
        props: ['shows', 'role'],
        data () {
            return {
                model: true,
                loading: false,
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
                    url: vm.$request.permission.all,
                    success: function (data) {
                        let rootArray = [];
                        let originalData = data;

                        //depth = 1
                        originalData.map((value, key) => {
                            value.children = [];
                            if(value.is_root == 1){
                                let flag = false;
                                for(let mm=0; mm<vm.rolePermissions.length; mm++){
                                    if(vm.rolePermissions[mm].id == value.id){
                                        flag = true;
                                        break;
                                    }
                                }
                                value.checked = flag;
                                value.title = value.name;
                                value.expand = true;
                                rootArray.push(value);
                            }
                        });

                        //depth = 2
                        for(var i=0; i<rootArray.length; i++){
                            originalData.map((value, key) => {
                                if(value.is_root != 1){
                                    var tempPath = value.path.split('/');
                                    if(tempPath.length == 2 && tempPath[0] == rootArray[i].path){
                                        let flag = false;
                                        for(let mm=0; mm<vm.rolePermissions.length; mm++){
                                            if(vm.rolePermissions[mm].id == value.id){
                                                flag = true;
                                                break;
                                            }
                                        }
                                        value.checked = flag;
                                        value.title = value.name;
                                        rootArray[i].children.push(value);
                                    }
                                }
                            });
                        }

                        //depth = 3
                        for(var i=0; i<rootArray.length; i++){
                            originalData.map((value, key) => {
                                var tempPath = value.path.split('/');
                                if(value.is_root != 1 && tempPath.length == 3){
                                    if(rootArray[i].children.length > 0){
                                        for(var j=0; j<rootArray[i].children.length; j++){
                                            var tempParentPath = rootArray[i].children[j].path.split('/');
                                            var pathString = tempPath[0] + tempPath[1];
                                            var parentPathString = tempParentPath[0] + tempParentPath[1];
                                            if(pathString == parentPathString){
                                                let flag = false;
                                                for(let mm=0; mm<vm.rolePermissions.length; mm++){
                                                    if(vm.rolePermissions[mm].id == value.id){
                                                        flag = true;
                                                        break;
                                                    }
                                                }
                                                value.checked = flag;
                                                value.title = value.name;
                                                rootArray[i].children[j].children.push(value);
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        //depth = 4
                        for(var i=0; i<rootArray.length; i++){
                            originalData.map((value, key) => {
                                var tempPath = value.path.split('/');
                                if(value.is_root != 1 && tempPath.length == 4){
                                    if(rootArray[i].children.length > 0){
                                        for(var j=0; j<rootArray[i].children.length; j++){
                                            if(rootArray[i].children[j].children.length > 0){
                                                for(var k=0; k<rootArray[i].children[j].children.length; k++){
                                                    var tempParentPath = rootArray[i].children[j].children[k].path.split('/');
                                                    var pathString = tempPath[0] + tempPath[1] + tempPath[2];
                                                    var parentPathString = tempParentPath[0] + tempParentPath[1] + tempParentPath[2];
                                                    if(pathString == parentPathString){
                                                        let flag = false;
                                                        for(let mm=0; mm<vm.rolePermissions.length; mm++){
                                                            if(vm.rolePermissions[mm].id == value.id){
                                                                flag = true;
                                                                break;
                                                            }
                                                        }
                                                        value.checked = flag;
                                                        value.title = value.name;
                                                        rootArray[i].children[j].children[k].children.push(value);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        vm.treeData = rootArray;
                        vm.loading = true;
                    }
                });
            },
            loadData (item, callback) {
                let vm = this;
                vm.ajax({
                    url: vm.$request.permission.children,
                    params: {
                        path: item.path
                    },
                    success: function (data) {
                        if (data.length == 0) {
                            item.loading = false;
                            delete item.loading;
                            delete item.children;
                            return;
                        }

                        let nodes = [];
                        for (let permission of data) {
                            let flag = false;
                            for(let i=0; i<vm.rolePermissions.length; i++){
                                if(vm.rolePermissions[i].id == permission.id){
                                    flag = true;
                                    break;
                                }
                            }

                            let node = {
                                id: permission.id,
                                type: permission.type,
                                title: permission.name,
                                loading: false,
                                path: permission.path,
                                page_url: permission.page_url,
                                api_name: permission.api_name,
                                api_url: permission.api_url,
                                sort: permission.sort,
                                checked: flag,
                                children: []
                            };
                            nodes.push(node);
                        }
                        callback(nodes);
                    }
                });
            },
            selectChange: function(items) {

            },
            checkChange(items){

            },
            handleSelect: function() {
                let vm = this;
                let ids = [];
                let lists = vm.treeData;
                for(let i=0; i<lists.length; i++){
                    if(lists[i].checked){
                        ids.push(lists[i].id);
                    }

                    if(lists[i].children && lists[i].children.length > 0){
                        let items = lists[i].children;
                        for(let j=0; j<items.length; j++) {
                            if (items[j].checked) {
                                ids.push(items[j].id);
                            }

                            if(items[j].children && items[j].children.length > 0){
                                let children = items[j].children;
                                for(let k=0; k<children.length; k++) {
                                    if (children[k].checked) {
                                        ids.push(children[k].id);
                                    }
                                }
                            }
                        }
                    }
                }

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
