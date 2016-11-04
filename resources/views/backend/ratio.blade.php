<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <script src="{{asset('backend/js/jquery-2.2.3.min.js')}}"></script>
    <script src="{{asset('backend/js/echarts.common.min.js')}}"></script>
    <script src="{{asset('backend/js/roma.js')}}"></script>
</head>
<body>
<!-- 为ECharts准备一个具备大小（宽高）的Dom -->
<div id="main" style="width: 600px;height:400px;"></div>
<script type="text/javascript">

    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('main'));
    $.get('show', function (data) {
        myChart.showLoading();
        $.get('show', function (data) {
            myChart.hideLoading();

            var base = -data.reduce(function (min, val) {
                return Math.floor(Math.min(min, val.l));
            }, Infinity);
            myChart.setOption(option = {
                title: {
                    text: 'Confidence Band',
                    subtext: 'Example in MetricsGraphics.js',
                    left: 'center'
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        animation: false
                    },
                    formatter: function (params) {
                        return params[2].name + '<br />' + params[2].value;
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: data.map(function (item) {
                        return item.date;
                    }),
                    axisLabel: {
                        formatter: function (value, idx) {
                            var date = new Date(value);
                            return idx === 0 ? value : [date.getMonth() + 1, date.getDate()].join('-');
                        }
                    },
                    splitLine: {
                        show: false
                    },
                    boundaryGap: false
                },
                yAxis: {
                    axisLabel: {
                        formatter: function (val) {
                            return (val - base) * 100 + '%';
                        }
                    },
                    splitNumber: 3,
                    splitLine: {
                        show: false
                    }
                },
                series: [{
                    name: 'L',
                    type: 'line',
                    data: data.map(function (item) {
                        return item.l + base;
                    }),
                    lineStyle: {
                        normal: {
                            opacity: 0
                        }
                    },
                    stack: 'confidence-band',
                    symbol: 'none'
                }, {
                    name: 'U',
                    type: 'line',
                    data: data.map(function (item) {
                        return item.u - item.l;
                    }),
                    lineStyle: {
                        normal: {
                            opacity: 0
                        }
                    },
                    areaStyle: {
                        normal: {
                            color: '#ccc'
                        }
                    },
                    stack: 'confidence-band',
                    symbol: 'none'
                }, {
                    type: 'line',
                    data: data.map(function (item) {
                        return item.value + base;
                    }),
                    hoverAnimation: false,
                    symbolSize: 6,
                    itemStyle: {
                        normal: {
                            color: '#c23531'
                        }
                    },
                    showSymbol: false
                }]
            });
        });
    });

</script>
</body>
</html>