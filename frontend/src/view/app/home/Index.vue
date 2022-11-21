<template>
    <div>
        <div class="pageWrapper">
            <div style="display:none;">数据中台</div>
            <HChart :id="idFirst" :option="optionRubbish" v-if="hasData"></HChart>
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

    let optionRubbish = {
        chart: {
            type: 'pie'
        },
        title: {
            text: '炉温分布图'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: '℃'
            }
        },
        tooltip: {
            // head + 每个 point + footer 拼接成完整的 table
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                borderWidth: 0
            }
        },
        series: [{
				name: '小时数',
				colorByPoint: true,
				data: []
		}]
    };

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
                idFirst: 'first',
                optionRubbish: optionRubbish,
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
            getGragh() {
                let vm = this;
                vm.graghLoading = true;
                vm.hasData = false;
                vm.ajax({
                    method: 'get',
                    params: {
                        factory_id: 9
                    },
                    url: '/screen/boiler-temperature',
                    success: (data) => {
                        vm.graghData = data;
                        vm.graghLoading = false;
                        // vm.optionRubbish.xAxis.categories = Object.keys(data.enter_factory_rubbish);
                        // vm.optionRubbish.series[0].data = Object.values(data.enter_factory_rubbish);
                        // vm.optionRubbish.series[1].data = Object.values(data.total_incineration_rubbish);
                        //
                        // vm.optionEnergy.xAxis.categories = Object.keys(data.total_electric_energy);
                        // vm.optionEnergy.series[0].data = Object.values(data.total_electric_energy);
                        // vm.optionEnergy.series[1].data = Object.values(data.total_online_electric_energy);
                        //
                        // vm.optionLeachate.xAxis.categories = Object.keys(data.produce_leachate);
                        // vm.optionLeachate.series[0].data = Object.values(data.produce_leachate);
                        // vm.optionLeachate.series[1].data = Object.values(data.handle_leachate);
                        vm.hasData = true;
                        let list = [];
                        let key = 0;
                        let index = 0;
                        let max = 0;
                        for(let item of data){
                            let hour = Number((item.value * 5/60).toFixed(2));
                            list.push({
                                name: item.name,
						        y: hour
                            });

                            if(item.value > 0 && item.value > max){
                                key = index;
                                max = item.value;
                            }
                            index++;
                        }
                        list[key].sliced = true;
                        list[key].selected = true;
                        vm.optionRubbish.series[0].data = list;
                    },
                    fail() {
                        vm.graghLoading = false;
                    }
                });
            },
        },
        components: {
            HChart
        },
        mounted() {
            let that = this;
            checkToken(()=>{
                that.getGragh();//取数据
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
