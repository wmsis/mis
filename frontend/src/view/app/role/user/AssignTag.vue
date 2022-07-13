<template>
    <Modal
            v-model="shows.tag"
            title="分配TAG"
            :fullscreen="true"
            :mask-closable="false"
            :footer-hide="true"
            @on-visible-change="visibleChange">
        <div style="text-align: center" v-loading="rightLoading || leftLoading">
            <Transfer
                    filterable
                    :data="tags"
                    :target-keys="userTags"
                    :render-format="renderFormat"
                    :titles="titles"
                    :list-style="listStyle"
                    @on-change="handleChange"></Transfer>
            <Button
                    type="primary"
                    :style="{marginTop: '15px'}"
                    @click="assignTags" :loading="loading">立即修改</Button>
        </div>
    </Modal>
</template>

<script>
    import Vue from 'vue'
    import {
        Modal,
        Transfer,
        Button
    } from 'iview';
    import {
        Loading
    } from 'element-ui';

    Vue.component('Modal', Modal);
    Vue.component('Transfer', Transfer);
    Vue.component('Button', Button);
    Vue.use(Loading);

    export default {
        props: ['shows','userId'],
        data () {
            return {
                width: 1200,
                loading: false,
                leftLoading: false,
                rightLoading: false,
                titles: ['TAG列表', '已有TAG'],
                tags: [],
                userTags: [],
                listStyle: {
                    width: '528px',
                    height: '500px',
                    textAlign: 'left'
                }
            }
        },
        methods: {
            visibleChange: function(visible) {
                if (visible) {
                    this.getTags();
                    this.getUserTags();
                } else {
                    this.shows.tag = false;
                }
            },
            assignTags: function() {
                let vm = this;
                let url = vm.$request.admins.assignTags.replace("{id}", this.userId);
                let ids = '';
                for (let i = 0; i < vm.userTags.length; i++) {
                    ids += vm.userTags[i];
                    if (i < vm.userTags.length - 1) {
                        ids += ',';
                    }
                }

                if(!vm.loading) {
                    vm.loading = true;
                    vm.ajax({
                        method: 'POST',
                        url: url,
                        params: {
                            tags: vm.userTags
                        },
                        success: function () {
                            vm.shows.tag = false;
                            vm.$emit("listenChildClose");
                            vm.loading = false;
                        },
                        fail(){
                            vm.loading = false;
                        }
                    });
                }
            },
            getTags: function() {
                let vm = this;
                vm.leftLoading = true;
                vm.ajax({
                    url: vm.$request.setting.alltag,
                    params: {

                    },
                    success: function (data) {
                        let tags = [];
                        for (let tag of data) {
                            if(tag.tag_name != 'fake-historian-tag') {
                                tags.push({
                                    key: tag.id,
                                    label: tag.tag_name + ((tag.description || tag.alias) ? ('(' + (tag.alias ? tag.alias : tag.description) + ')') : '')
                                });
                            }
                        }
                        vm.tags = tags;
                        vm.leftLoading = false;
                        vm.notifyParent();
                    },
                    fail(){
                        vm.leftLoading = false;
                    }
                });
            },
            getUserTags: function() {
                let vm = this;
                vm.rightLoading = true;
                let url = vm.$request.admins.tagList.replace("{id}", this.userId);
                vm.ajax({
                    url: url,
                    success: function (data) {
                        vm.rightLoading = false;
                        vm.userTags = [];
                        for (let myTag of data.myTags) {
                            vm.userTags.push(myTag.id);
                        }
                    },
                    fail(){
                        vm.rightLoading = false;
                    }
                });
            },
            renderFormat (item) {
                return item.label;
            },
            notifyParent() {
                this.$emit("listenChildReady", {
                    status: false
                });
            },
            handleChange (newTargetKeys, direction, moveKeys) {
                this.userTags = newTargetKeys;
            }
        }
    }
</script>
