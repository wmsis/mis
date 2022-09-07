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
            //一次性加载
            setTreeData() {
                let vm = this;
                vm.ajax({
                    url: vm.$request.api.tree,
                    success: function (data) {
                        for(let item of data){
                            item.expand = true;
                        }
                        vm.loopIds(data);
                        vm.treeData = data;
                        vm.$nextTick(() => {
                            let selectedNodes = vm.$refs.treeRef.getSelectedNodes();
                            vm.$emit("listenSelectChange", selectedNodes[0]);
                        });
                    }
                });
            },

            //选中我的接口
            loopIds(lists){
                for(let i=0; i<lists.length; i++){
                    lists[i].title = lists[i].name;
                    if(lists[i].children && lists[i].children.length > 0){
                        this.loopIds(lists[i].children);
                    }
                }
            },

            selectChange: function (items) {
                let menu = items[0];
                this.$emit("listenSelectChange", menu);
            }
        }
    }
</script>
