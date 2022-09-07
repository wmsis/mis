<template>
    <div class="container-x">
        <!--修改当前节点-->
        <Update
            v-bind:shows="shows"
            v-bind:currentNode="currentNode"
            v-on:listenChildClose="childClose">
        </Update>

        <!--新增子节点-->
        <Add
            v-bind:shows="shows"
            v-bind:currentNode="currentNode"
            v-on:listenChildClose="childClose">
        </Add>

        <div class="left">
            <Tree
                ref="treeRef"
                @listenSelectChange="selectChange">
            </Tree>
        </div>
        <div class="center">
            <!--工具栏-->
            <div class="button-bar">
                <ButtonGroup :style="{'margin': '0px 0px 10px 0px'}">
                    <Button
                            v-if="showAddSubButton && isButtonExist('orgnization-insert')"
                            type="primary"
                            icon="md-add-circle"
                            @click="shows.add = true">{{currentNode ? '新增子组织' : '新增一级组织'}}</Button>
                    <Button
                        v-if="showModifyButton && isButtonExist('orgnization-update')"
                        type="primary"
                        icon="ios-create"
                        @click="shows.update = true">修改</Button>
                    <Button
                        v-if="showDeleteButton && isButtonExist('orgnization-delete')"
                        type="primary"
                        icon="md-close-circle"
                        @click="del">删除</Button>
                </ButtonGroup>
            </div>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue'
    import {
        ButtonGroup,
        Button
    } from 'iview';

    Vue.component('ButtonGroup', ButtonGroup);
    Vue.component('Button', Button);

    import Tree from './Tree.vue'
    import Add from './Add.vue'
    import Update from './Update.vue'

    export default {
        components: {
            Tree,
            Add,
            Update
        },
        data () {
            return {
                shows: {
                    add: false,
                    update: false
                },
                showModifyButton: false,
                showAddSubButton: true,
                showDeleteButton: false,
                currentNode: undefined
            }
        },
        methods: {
            selectChange: function (node) {
                this.currentNode = node;
                if(node) {
                    if (!node.id) {
                        this.showModifyButton = false;
                        if (node.level < 10) {
                            this.showAddSubButton = true;
                        }
                        else {
                            this.showAddSubButton = false;
                        }
                        this.showDeleteButton = false;
                        return;
                    }

                    this.showModifyButton = true;
                    if (node.level < 10) {
                        this.showAddSubButton = true;
                    }
                    else {
                        this.showAddSubButton = false;
                    }
                    this.showDeleteButton = true;
                }
                else{
                    //没有选中任何节点
                    this.showModifyButton = false;
                    this.showAddSubButton = true;
                    this.showDeleteButton = false;
                }
            },
            del: function () {
                let vm = this;
                vm.showMessageBox('confirm', '删除', '确定要删除吗？', ()=>{
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.orgnization.delete,
                        data: {
                            id: vm.currentNode.id
                        },
                        success: function () {
                            vm.$refs.treeRef.setTreeData();
                            vm.showModifyButton = false;
                            vm.showAddSubButton = true;
                            vm.showDeleteButton = false;
                        }
                    });
                });
            },
            childClose: function () {
                let vm = this;
                this.$refs.treeRef.setTreeData(); //重新加载树形
                vm.showModifyButton = false;
                vm.showAddSubButton = false;
                vm.showDeleteButton = false;
            }
        }
    }
</script>
<style lang="scss">
    .container-x {
        display: flex;
        flex-direction: row;
        .left {
            width: 400px;
            height: calc(100vh - 110px);
            border-right: 1px solid #eee;
        }
        .center {
            flex: 1;
            padding: 7px 10px;
            .button-bar {
                height: 40px;
                text-align: right;
            }
        }
    }
</style>
