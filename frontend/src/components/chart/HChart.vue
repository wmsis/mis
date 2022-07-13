<template>
    <div class="x-bar">
        <div :id="id" :option="option"></div>
    </div>
</template>
<script>
    import HighCharts from 'highcharts';
    export default {
        // 验证类型
        props: {
            id: {
                type: String
            },
            option: {
                type: Object
            }
        },
        data(){
            return {
                chart: null
            }
        },
        methods: {
            redraw(){
                let series = this.option.series;
                for(let i=0; i<series.length; i++){
                    this.chart.series[i].setData(series[i].data);
                }
            },
            addPoint(newer){
                let series = this.option.series;
                for(let i=0; i<series.length; i++) {
                    if(newer[i]) {
                        this.chart.series[i].addPoint(newer[i], true, true);
                    }
                }

                this.chart.redraw();
            },
            switchAxis(index){
                let yAxis = this.option.yAxis;
                for(let i=0; i<yAxis.length; i++) {
                    if(index == i) {
                        this.chart.series[i].update({
                            lineWidth: 2,
                            className: 'full'
                        });
                        this.chart.yAxis[i].update({
                            visible: true,
                            gridLineWidth: 1
                        });
                    }
                    else{
                        this.chart.series[i].update({
                            lineWidth: 2,
                            className: 'opacity'
                        });
                        this.chart.yAxis[i].update({
                            visible: false,
                            gridLineWidth: 0
                        });
                    }
                }
            }
        },
        mounted() {
            HighCharts.setOptions({
                lang:{
                    contextButtonTitle:"图表导出菜单",
                    decimalPoint:".",
                    downloadJPEG:"下载JPEG图片",
                    downloadPDF:"下载PDF文件",
                    downloadPNG:"下载PNG文件",
                    downloadSVG:"下载SVG文件",
                    drillUpText:"返回 {series.name}",
                    loading:"加载中",
                    months:["一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月"],
                    noData:"没有数据",
                    numericSymbols: [ "千" , "兆" , "G" , "T" , "P" , "E"],
                    printChart:"打印图表",
                    resetZoom:"恢复缩放",
                    resetZoomTitle:"恢复图表",
                    shortMonths: [ "Jan" , "Feb" , "Mar" , "Apr" , "May" , "Jun" , "Jul" , "Aug" , "Sep" , "Oct" , "Nov" , "Dec"],
                    thousandsSep:",",
                    weekdays: ["星期一", "星期二", "星期三", "星期四", "星期五", "星期六","星期天"]
                },
                global: {
                    useUTC: false //关闭UTC
                }
            });
            this.chart = HighCharts.chart(this.id,this.option)
        }
    }
</script>
<style>
    .x-bar{
        height: 100%;
        width: 100%;
    }
    .x-bar > div{
        height: 100%;
        width: 100%;
    }
    .highcharts-title{
        visibility: visible;
    }
    .highcharts-credits{
        visibility: hidden;
    }
    .full{
        opacity: 1;
    }
    .opacity{
        opacity: 0.4;
    }
</style>
