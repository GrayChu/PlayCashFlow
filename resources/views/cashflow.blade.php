<!DOCTYPE html>
    <head lang="en">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>

        #chart svg {
            height: 400px;
            width:100%;
        }

    </style>
    </head>
    <body>
      <div id="chart">
          <svg></svg>
      </div>
      <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
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
                                <th>日期</th>
                                <th>單號</th>
                                <th>付款人</th>
                                <th>金額</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                                <td id="td1"></td>
                                <td id="td2"></td>
                                <td id="td3"></td>
                                <td id="td4"></td>
                            </tr>
                          </tbody>
                      </table>
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
      <script>
                d3.json("{{asset('cumulativeLineData.json')}}", function(data) {

                    nv.addGraph(function() {



                        var chart = nv.models.lineChart()
                            .x(function(d) {return Date.parse(d[0])})
                            .y(function(d) { return d[1] })
                            .color(d3.scale.category10().range())
                            //.useInteractiveGuideline(true)
                        ;


                        chart.xAxis
                            .ticks(10)
                            .tickFormat(function(d) {

                                return d3.time.format('%Y-%m-%d')(new Date(d))
                            });




                        d3.select('#chart svg')
                            .datum(data)
                            .transition().duration(500)
                            .call(chart)
                        ;

                        var line = d3.select('#chart svg')
                            .append('line')
                            .attr({
                                x1: 60 + chart.xAxis.scale()(new Date('2018-07-01')),
                                y1: 30 + chart.yAxis.scale()(200000),
                                x2: 60 + chart.xAxis.scale()(new Date('2018-12-01')),
                                y2: 30 + chart.yAxis.scale()(200000)
                            })
                            .style({"stroke" :"#ff0000","stroke-width":"3","stroke-dasharray":"4,4"});

                        chart.lines.dispatch.on('elementClick', function(e) {
                            getInfo(e.point);
                        });

                        nv.utils.windowResize(chart.update);

                        return chart;
                    });

                });
                let getInfo=(e)=>{
                    $('#td1').empty();
                    $('#td2').empty();
                    $('#td3').empty();
                    $('#td4').empty();
                    $('#td1').text(e[0]);
                    $('#td2').text(e[2]);
                    $('#td3').text(e[3]);
                    $('#td4').text(e[4]);
                    $('#exampleModal').modal('show');
                }
        </script>
    </body>
</html>
