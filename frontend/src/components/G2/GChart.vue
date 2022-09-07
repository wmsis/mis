<template>
    <div class="x-bar">
        <div :id="id" ref="chart"></div>
        <ul class="operate">
            <li v-for="(item, index) in legends" :key="index" @click="updateOptions(index)">{{item.name}}</li>
        </ul>
    </div>
</template>
<script>
    import { Chart } from '@antv/g2';
    export default {
        // 验证类型
        props: {
            id: {
                type: String
            },
            options: {
                type: Object
            }
        },
        data(){
            return {
                chart: null,
                legends: []
            }
        },
        methods: {
            redraw(){
                this.chart.changeData(this.options.data);
            },
            addPoint(newer){

            },
            updateOptions(index){
                let option = {
                    axes: {
                        value1: {
                            position: 'left',
                            grid: null,
                            label: null
                        },
                        value2: {
                            position: 'left',
                            grid: null,
                            label: null
                        },
                        value3: {
                            position: 'left'
                        }
                    }
                };

                this.chart.updateOptions(option);
                this.chart.render();
            },
            in_array(name, array){
                if(!name || !array || array.length ==0){
                    return false;
                }
                for(let item of array){
                    if(item.name == name){
                        return true;
                    }
                }
            },
            computeLegends(){
                let legends = [];
                let lists = this.options.data;
                if(lists.length > 0) {
                    for (let i = 0; i < lists.length; i++) {
                        if (!this.in_array(lists[i].phone, this.legends)) {
                            legends.push({
                                name: lists[i].phone
                            });
                            this.legends = legends;
                        }
                    }
                }
            }
        },
        mounted() {
            let that = this;
            that.computeLegends();
            that.chart = new Chart({
                container: that.id,
                autoFit: true,
                height: 450 // 指定图表高度
            });

            that.chart.data(that.options.data);

            let series = that.options.series;
            if(series.length > 0){
                for(let item of series){
                    that.chart[item.type]()
                        .position(item.position)
                        .color(item.color);
                }
            }

            let scales = that.options.scales;
            if(scales){
                for(let axis in scales){
                    that.chart.scale(axis, scales[axis]);
                }
            }

            let axis = that.options.axis;
            if(axis){
                for(let key in axis){
                    that.chart.axis(key, axis[key]);
                }
            }

            that.chart.tooltip({
                shared: true
            });

            that.chart.render();
        }
    }
</script>
<style scoped lang="scss">
    .x-bar{
        height: 100%;
        width: 100%;
        & .operate{
            list-style: none;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            height: 40px;
            margin-top: 10px;
            & li{
                height: 40px;
                line-height: 40px;
                text-align: center;
                margin: 0 10px;
                max-width: 150px;
                padding-right: 5px;
                padding-left: 5px;
                box-sizing: border-box;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        }
    }
</style>
