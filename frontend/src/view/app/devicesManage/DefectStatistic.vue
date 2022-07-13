<template>
    <div class="page">
        <div class="inner" v-loading="graghLoading" element-loading-text="拼命加载中">
            <ul class="wel-gragh" v-for="(item, index) in graghList" :key="index">
                <li class="pie">
                    <HChart :id="'ment-pie-' + index" class="chart" :option="item.pie" v-if="hasData"></HChart>
                </li>
                <li class="column">
                    <HChart :id="'ment-column-' + index" class="chart" :option="item.column" v-if="hasData"></HChart>
                    <div class="bts">
                        <RadioGroup v-model="period" type="button" size="small" @on-change="chgPeriod">
                            <Radio label="month">月</Radio>
                            <Radio label="year">年</Radio>
                        </RadioGroup>
                    </div>
                </li>
            </ul>
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
    import HighCharts from 'highcharts';
    import HChart from '@/components/chart/HChart.vue';

    Vue.component('RadioGroup', RadioGroup);
    Vue.component('Radio', Radio);
    Vue.use(Loading);

    let optionPie = {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: '设备缺陷统计'
        },
        tooltip: {
            pointFormat: '次数：{point.y}，占比: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (HighCharts.theme && HighCharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: []
    };
    let optionColumn = {
        chart: {
            type: 'column'
        },
        title: {
            text: '各类缺陷发生频率'
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
                text: '次数'
            }
        },
        tooltip: {
            shared: true,
        },
        plotOptions: {
            column: {
                borderWidth: 0
            }
        },
        series: [{
            name: '一类缺陷',
            data: []
        }, {
            name: '二类缺陷',
            data: []
        }, {
            name: '三类缺陷',
            data: []
        }]
    };

    export default {
        data(){
            return {
                hasData: false,
                graghLoading: false,
                period: 'month',
                graghList: []
            }
        },
        components: {
            HChart
        },
        methods:{
            chgPeriod(){
                this.getGragh();
            },
            getGragh() {
                let vm = this;
                vm.graghLoading = true;
                vm.hasData = false;
                vm.ajax({
                    method: 'get',
                    params: {
                        period: vm.period
                    },
                    url: vm.$request.equipments.equipmentMaintenanceGragh,
                    success: (data) => {
                        vm.graghLoading = false;
                        let graghList = [];
                        for(let key in data){
                            let itemOuter = data[key];
                            let optionPieSeries = JSON.parse(JSON.stringify(optionPie));
                            let optionColumnSeries = JSON.parse(JSON.stringify(optionColumn));
                            optionPieSeries.title.text = key + '缺陷分布统计';
                            optionColumnSeries.title.text = key + '各类缺陷发生频率';
                            let arrPie = [];
                            for(let kind in itemOuter.pie) {
                                let innerItem = itemOuter.pie[kind];
                                if(kind == 'serious') {
                                    arrPie.push({
                                        name: '一类缺陷',
                                        y: Number(innerItem),
                                        sliced: true,
                                        selected: true
                                    });
                                }
                                else if(kind == 'worse') {
                                    arrPie.push({
                                        name: '二类缺陷',
                                        y: Number(innerItem)
                                    });
                                }
                                else if(kind == 'general') {
                                    arrPie.push({
                                        name: '三类缺陷',
                                        y: Number(innerItem)
                                    });
                                }
                            }
                            optionPieSeries.series = [{
                                colorByPoint: true,
                                data: arrPie
                            }];

                            for(let kind in itemOuter.column) {
                                let innerItem = itemOuter.column[kind];
                                if(kind == 'general') {
                                    optionColumnSeries.xAxis.categories = Object.keys(innerItem);
                                    optionColumnSeries.series[0].data = Object.values(innerItem);
                                }
                                else if(kind == 'worse') {
                                    optionColumnSeries.series[1].data = Object.values(innerItem);
                                }
                                else if(kind == 'serious') {
                                    optionColumnSeries.series[2].data = Object.values(innerItem);
                                }
                            }

                            graghList.push({
                                pie: optionPieSeries,
                                column: optionColumnSeries
                            });
                        }
                        vm.graghList = graghList;
                        vm.hasData = true;
                    },
                    fail() {
                        vm.graghLoading = false;
                    }
                });
            },
        },
        mounted(){
            this.getGragh();
        }
    }
</script>

<style scoped  lang="scss">
    .page {
        padding: 0px;

        & .inner {
            box-sizing: border-box;
            padding: 15px;
            overflow: auto;
            height: 100%;
            width: 100%;

            & .wel-gragh{
                list-style: none;
                margin: 0;
                padding:0;
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                position: relative;
                min-height: 400px;
                & .chart{
                    height: 400px;
                }
            }
            & .wel-gragh li{
                box-sizing: border-box;
                height: 410px;
                overflow: auto;
                -webkit-overflow-scrolling : touch;
                margin-bottom: 12px;
                padding-top: 10px;
                background: white;
                position: relative;
                & .bts{
                    position: absolute;
                    left: 0;
                    top: 15px;
                    width: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: flex-end;
                    padding-right: 20px;
                    box-sizing: border-box;
                }
            }
            & .wel-gragh li.half{
                width: calc( 50% - 6px );
            }
            & .wel-gragh li.half:nth-child(odd){
                margin-right: 6px;
            }
            & .wel-gragh li.half:nth-child(even){
                margin-left: 6px;
            }
            & .wel-gragh li.pie{
                width: 40%;
            }
            & .wel-gragh li.column{
                width: 60%;
            }
        }
    }
</style>