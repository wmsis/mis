<template>
    <Modal
        v-model="shows.assign"
        title="分配角色"
        :width="width"
        :mask-closable="false"
        :footer-hide="true"
        @on-visible-change="visibleChange">
        <div style="text-align: center">
            <Transfer
                :data="roles"
                :target-keys="userRoles"
                :render-format="renderFormat"
                :titles="titles"
                :list-style="listStyle"
                @on-change="handleChange"></Transfer>
            <Button
                type="primary"
                :style="{marginTop: '15px'}"
                @click="assignRoles">立即修改</Button>
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

    Vue.component('Modal', Modal);
    Vue.component('Transfer', Transfer);
    Vue.component('Button', Button);

    export default {
        props: ['shows','userId'],
        data () {
            return {
                width: 600,
                titles: ['角色列表', '已有角色'],
                roles: [],
                userRoles: [],
                listStyle: {
                    width: '230px',
                    height: '300px',
                    textAlign: 'left'
                }
            }
        },
        mounted: function() {
            this.getRoles();
        },
        methods: {
            visibleChange: function(visible) {
                if (visible) {
                    this.getUserRoles();
                } else {
                    this.shows.assign = false;
                }
            },
            assignRoles: function() {
                let vm = this;
                let url = vm.$request.admins.assignRoles.replace("{id}", this.userId);
                let roleIds = '';
                for (let i = 0; i < vm.userRoles.length; i++) {
                    roleIds += vm.userRoles[i];
                    if (i < vm.userRoles.length - 1) {
                        roleIds += ',';
                    }
                }
                vm.ajax({
                    method: 'POST',
                    url: url,
                    params: {
                        roles: vm.userRoles
                    },
                    success: function () {
                        vm.shows.assign = false;
                        vm.$emit("listenChildClose");
                    }
                });
            },
            getRoles: function() {
                let vm = this;
                vm.ajax({
                    url: vm.$request.roles.list,
                    success: function (data) {
                        let roles = [];
                        for (let role of data) {
                            if(role.id != 1) {
                                roles.push({
                                    key: role.id,
                                    label: role.name
                                });
                            }
                        }
                        vm.roles = roles;
                    }
                });
            },
            getUserRoles: function() {
                let vm = this;
                let url = vm.$request.admins.assignRoles.replace("{id}", this.userId);
                vm.ajax({
                    url: url,
                    success: function (data) {
                        vm.userRoles = [];
                        for (let myRole of data.myRoles) {
                            vm.userRoles.push(myRole.id);
                        }
                    }
                });
            },
            renderFormat (item) {
                return item.label;
            },
            handleChange (newTargetKeys, direction, moveKeys) {
                this.userRoles = newTargetKeys;
            }
        }
    }
</script>
