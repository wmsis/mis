<template>
    <div :style="{paddingLeft: '20px', height:'100%', overflowY: 'auto'}">
        <Tree ref="treeRef"
              :data="treeData"
              empty-text="加载中…"
              @on-select-change="selectChange">
        </Tree>
    </div>
</template>

<script>
    import Vue from 'vue'
    import {
        Tree
    } from 'iview';

    Vue.component('Tree', Tree);

    export default {
        data () {
            return {
                treeData: []
            }
        },
        mounted: function() {
            this.setTreeData();
        },
        methods: {
            //异步加载
            setTreeDataAsync() {
                let vm = this;
                vm.ajax({
                    url: vm.$request.permission.list,
                    success: function (data) {
                        let rootNode = [{
                            id: undefined,
                            title: '权限列表',
                            loading: false,
                            expand: true,
                            selected: true,
                            path: undefined,
                            children: []
                        }];
                        for (let permission of data) {
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
                                children: []
                            };
                            rootNode[0].children.push(node);
                        }
                        vm.treeData = rootNode;
                    }
                });
            },
            //一次性加载
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
                                value.checked = false;
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
                                        value.checked = false;
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
                                                value.checked = false;
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
                                                        value.checked = false;
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
                        vm.$nextTick(() => {
                            let selectedNodes = vm.$refs.treeRef.getSelectedNodes();
                            vm.$emit("listenSelectChange", selectedNodes[0]);
                        });
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
                                children: []
                            };
                            nodes.push(node);
                        }
                        callback(nodes);
                    }
                });
            },
            selectChange: function (items) {
                let menu = items[0];
                this.$emit("listenSelectChange", menu);
            }
        }
    }
</script>
