<!DOCTYPE html>
    <head lang="en">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>

        #chart svg {
            height: 500px;
            width:100%;
            overflow:visible;

        }
        div.tooltip {
            position: absolute;
            text-align: center;
            padding: 2px;
            font: 12px sans-serif;
            background: cornsilk;
            border: 0px;
            border-radius: 8px;
            pointer-events: none;
        }
        .nv-point {
            stroke-opacity: 1 !important;
            stroke-width: 5px;
            fill-opacity: 1 !important;
        }
    </style>
    </head>
    <body>
      <div id="chart">
          <svg></svg>
      </div>
      <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content" >
                  <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">交易明細</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <table class="table table-bordered">
                          <thead>
                            <tr>
                                <th colspan="3" class="text-center">Last</th>
                                <th id="presum"></th>
                                <th colspan="2"></th>
                            </tr>
                            <tr>
                                <th>種類</th>
                                <th>單號</th>
                                <th>對象</th>
                                <th>金額</th>
                                <th>日期</th>
                                <th>備註</th>
                            </tr>
                          </thead>
                          <tbody id="contents">

                          </tbody>
                      </table>
                  </div>
                  <div class="modal-footer">
                  </div>
              </div>
          </div>
      </div>
      <div class="modal fade" id="safeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content" >
                  <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">資金安全水位</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      計算方式:(每月固定支出)x6
                  </div>
                  <div class="modal-footer">
                  </div>
              </div>
          </div>
      </div>
      <link href="https://cdn.rawgit.com/novus/nvd3/v1.8.1/build/nv.d3.css" rel="stylesheet"></link>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" ></script>
      <script src="https://d3js.org/d3.v3.min.js"></script>
      <script src="https://cdn.rawgit.com/novus/nvd3/v1.8.1/build/nv.d3.js"></script>
      <script src="{{asset('js/common.js')}}"></script>
      <script>
                d3.json("{{asset('cumulativeLineData.json')}}", function(data) {

                    nv.addGraph(function() {

                        var chart = nv.models.lineChart()
                            .x(function(d) {return Date.parse(d[0])})
                            .y(function(d) { return d[1] })
                            .color(d3.scale.category10().range())
                                .yDomain([0,1000000])
                            .margin({left:60,bottom:100});


                        chart.xAxis
                            .ticks(10)
                            .tickFormat(function(d) {

                                return d3.time.format('%Y-%m-%d')(new Date(d))
                            })
                            .rotateLabels(-90)

                        chart.yAxis
                            .tickFormat(function(d){
                                return Common.Money.format(d);
                            })
                            .axisLabel('金額(NTD)');




                        d3.select('#chart svg')
                            .datum(data)
                            .transition().duration(500)
                            .call(chart)
                        ;


                        var line = d3.select('#chart svg')
                            .append('line')
                            .attr({
                                x1: 60 + chart.xAxis.scale()(new Date('2018-12-01')),
                                y1: 30 + chart.yAxis.scale()(600000),
                                x2: 60 + chart.xAxis.scale()(new Date('2019-05-01')),
                                y2: 30 + chart.yAxis.scale()(600000)
                            })
                            .style({"stroke" :"#ff0000","stroke-width":"4","stroke-dasharray":"4,4"})
                            .on("click", function(d) {
                                $('#safeModal').modal('show');
                            });
                        var $svg = $('#chart svg');

                        $svg.parent().prepend('<div class="chart-title" style="text-align:center"><font size="72">Cash Flow Chart</font></div>');


                        chart.lines.dispatch.on('elementClick', function(e) {
                            getInfo(e.point,e.series.key);
                        });

                        nv.utils.windowResize(chart.update);

                        return chart;
                    });

                });
                let getInfo=(e,f)=>{
                    
                    $("#contents").empty();
                    $("#presum").empty();
                    let total=0;
                    for(let i=0;i<e[2].length;i++)
                    {
                        let newtr=$("<tr>");
                        let newtd="";

                        newtd+="<td>"+e[2][i][3]+"</td>";
                        newtd+="<td>"+e[2][i][0]+"</td>";
                        newtd+="<td>"+e[2][i][1]+"</td>";
                        newtd+="<td><font color='red'>"+((f==="Deposit")?"-":"")+Common.Money.format(e[2][i][2])+" NTD</font></td>";

                        newtd+="<td>"+e[0]+"</td>";
                        newtd+="<td></td>";
                        newtr.append(newtd);
                        $("#contents").append(newtr);
                        total+=parseInt(e[2][i][2]*((f==="Deposit")?(-1):(1)));
                    }

                    $("#presum").text(Common.Money.format(e[3])+" NTD");
                    let newtr=$("<tr>");
                    let newtd="";
                    newtd+="<td colspan='3' class='text-center'><b>Now<b></td>";
                    newtd+="<td><font color='green'><b>"+Common.Money.format(parseInt(e[3])+total)+" NTD</b></font></td>";
                    newtd+="<td colspan='2'></td>";
                    newtr.append(newtd);
                    $("#contents").append(newtr);
                    $('#exampleModal').modal('show');
                }
        </script>
    </body>
</html>
