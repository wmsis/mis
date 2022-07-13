<template>
    <div>
        <div class="pageWrapper">
            <div class="">数据中台</div>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import {
        RadioGroup,
        Radio
    } from 'iview';

    import {
        Loading
    } from 'element-ui';
    import HChart from '@/components/chart/HChart.vue';
    import {checkToken} from '@/utils/checkToken';
    import {unixtimefromat} from '@/utils/utils';
    import { mapState } from 'vuex'
    Vue.component('RadioGroup', RadioGroup);
    Vue.component('Radio', Radio);
    Vue.use(Loading);

    export default {
        computed:{
            ...mapState([
                'sitetitle'
            ]),
            loading(){
                if(this.kpiLoading || this.graghLoading){
                    return true;
                }
                else{
                    return false;
                }
            }
        },
        data() {
            return {
                hasData: false,
                kpiLoading: true,
                graghLoading: true,
            }
        },
        methods:{
            random(total, num){
                let data = [];
                for(let i=0; i< total; i++) {
                    data.push(Number((num * Math.random()).toFixed(2)));
                }

                return data;
            },
        },
        components: {
            HChart
        },
        mounted() {
            let that = this;
            checkToken(()=>{
                //取数据
            });
        }
    }
</script>
<style scoped  lang="scss">
    @import '../../../assets/scss/base/reset';
    @import '../../../assets/scss/base/mixins';
    @import '../../../assets/scss/base/placeholder';

    .pageWrapper{
        box-sizing: border-box;
        position: relative;
        overflow: auto;
        width: 100%;
        height: 100%;
    }
    .loading{
        width: 100%;
        height: 100%;
        position: absolute;;
        left: 0;
        top: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: hsla(0,0%,100%,.9);
    }
</style>
