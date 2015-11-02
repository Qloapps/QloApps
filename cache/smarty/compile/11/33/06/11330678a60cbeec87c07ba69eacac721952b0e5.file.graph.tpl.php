<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 20:02:41
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/helpers/dataviz/graph.tpl" */ ?>
<?php /*%%SmartyHeaderCode:60236786556377409b81862-91010531%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '11330678a60cbeec87c07ba69eacac721952b0e5' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/helpers/dataviz/graph.tpl',
      1 => 1446455061,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '60236786556377409b81862-91010531',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56377409b90c42_42331805',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56377409b90c42_42331805')) {function content_56377409b90c42_42331805($_smarty_tpl) {?>


<div id="box-clients" class="col-lg-3 box-stats color1" >
	<div class="boxchart-overlay">
		<div class="boxchart"></div>
	</div>	
	<span class="title"><?php echo smartyTranslate(array('s'=>'Customers'),$_smarty_tpl);?>
</span>
	<span class="value">4 589</span>
</div>

<div id="box-orders" class="col-lg-3 box-stats color2">
	<div class="boxchart-overlay">
		<div class="boxchart"></div>
	</div>	
	<span class="title"><?php echo smartyTranslate(array('s'=>'Orders'),$_smarty_tpl);?>
</span>
	<span class="value">789</span>
</div>

<div id="box-income" class="col-lg-3 box-stats color3">
	<i class="icon-money"></i>
	<span class="title"><?php echo smartyTranslate(array('s'=>'Income'),$_smarty_tpl);?>
</span>
	<span class="value">$999,99</span>
</div>

<div id="box-messages" class="col-lg-3 box-stats color4">
	<i class="icon-envelope-alt"></i>
	<span class="title"><?php echo smartyTranslate(array('s'=>'Message'),$_smarty_tpl);?>
</span>
	<span class="value">19</span>
</div>

<div class="clearfix"></div>

<div id="box-line" class="col-lg-3 box-stats color1" >
	<div class="boxchart-overlay">
		<div class="boxchart"></div>
	</div>	
	<span class="title"><?php echo smartyTranslate(array('s'=>'Traffic'),$_smarty_tpl);?>
</span>
	<span class="value">4 589</span>
</div>

<div id="box-spline" class="col-lg-3 box-stats color2" >
	<div class="boxchart-overlay">
		<div class="boxchart"></div>
	</div>	
	<span class="title"><?php echo smartyTranslate(array('s'=>'Conversion'),$_smarty_tpl);?>
</span>
	<span class="value">4 589</span>
</div>

<div class="clearfix"></div>

<script>
	var data = [4, 8, 15, 16, 23, 42, 8, 15, 16, 23, 42, 16, 23, 42, 8, 15, 15, 16, 23];
	var chart = d3.select("#box-clients .boxchart").append("svg")
		.attr("class", "data_chart")
		.attr("width", data.length * 6)
		.attr("height", 30);
	var y = d3.scale.linear()
		.domain([0, d3.max(data)])
		.range([0, d3.max(data)]);
	chart.selectAll("rect")
		.data(data)
		.enter().append("rect")
		.attr("y", function(d) { return 30 - d; })
		.attr("x", function(d, i) { return i * 6; })
		.attr("width", 4)
		.attr("height", y);
</script>

<script>
	var data = [4, 8, 15, 16, 23, 42, 8, 15, 16];
	var chart = d3.select("#box-orders .boxchart").append("svg")
		.attr("class", "data_chart")
		.attr("width", data.length * 6)
		.attr("height", 30);
	var y = d3.scale.linear()
		.domain([0, d3.max(data)])
		.range([0, d3.max(data)]);
	chart.selectAll("rect")
		.data(data)
		.enter().append("rect")
		.attr("y", function(d) { return 30 - d; })
		.attr("x", function(d, i) { return i * 6; })
		.attr("width", 4)
		.attr("height", y);
</script>

<script>
	var myColors = ["#1f77b4", "#ff7f0e", "#2ca02c", "#d62728", "#9467bd", "#8c564b", "#e377c2", "#7f7f7f", "#bcbd22", "#17becf"];
	d3.scale.myColors = function() {
		  return d3.scale.ordinal().range(myColors);
	};

	var data = [53245, 28479, 19697, 24037, 30245];
	var width = 140,
		height = 140,
		radius = Math.min(width, height) / 2;
	var color = d3.scale.ordinal().range(myColors);
	var pie = d3.layout.pie()
		.sort(null);
	var arc = d3.svg.arc()
		.innerRadius(radius - 140)
		.outerRadius(radius - 120);
	var svg = d3.select("#box-pie .boxchart").append("svg")
		.attr("class", "data_chart")
		.attr("width", width)
		.attr("height", height)
		.append("g")
		.attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");
	var path = svg.selectAll("path")
		.data(pie(data))
		.enter().append("path")
		.attr("fill", function(d, i) { return color(i); })
		.attr("d", arc);
</script>

<script>
	var data = [3, 6, 2, 7, 5, 12, 1, 3, 8, 9, 2, 5, 7],
		w = 120,
		h = 50,
		margin = 5,
		y = d3.scale.linear().domain([0, d3.max(data)]).range([0 + margin, h - margin]),
		x = d3.scale.linear().domain([0, data.length]).range([0 + margin, w - margin]);
	var vis = d3.select("#box-line .boxchart").append("svg")
		.attr("class", "data_chart")
		.attr("width", w)
		.attr("height", h);
	var g = vis.append("g")
		.attr("transform", "translate(0, 50)");
	var line = d3.svg.line()
		.x(function(d,i) { return x(i); })
		.y(function(d) { return -1 * y(d); });
	g.append("path").attr("d", line(data));

	vis.selectAll("dot")
		.data(data)
		.enter().append("circle")
		.attr("stroke", "#1BA6E5")
		.attr("stroke-width", 1)
		.attr("r", 3)
		.attr("transform", "translate(0, 50)")
		.attr("fill", "white")
		.attr("cx", function(d, i) { return x(i); })
		.attr("cy", function(d, i) { return -1 * y(d); });

	var	area = d3.svg.area()
		.x(function(d, i) { return x(i); })
		.y0(h)
		.y1(function(d, i) { return -1 * y(d); });

	g.append("path")
		.datum(data)
		.attr("class", "area")
		.attr("d", area);
</script>

<script>
	var data = [3, 6, 2, 7, 5, 12, 1, 3, 8, 9, 2, 5, 7],
		w = 120,
		h = 50,
		margin = 5,
		y = d3.scale.linear().domain([0, d3.max(data)]).range([0 + margin, h - margin]),
		x = d3.scale.linear().domain([0, data.length]).range([0 + margin, w - margin]);
	var vis = d3.select("#box-spline .boxchart").append("svg")
		.attr("class", "data_chart")
		.attr("width", w)
		.attr("height", h);
	var g = vis.append("g")
		.attr("transform", "translate(0, 50)");
	var line = d3.svg.line()
		.interpolate("basis")
		.x(function(d,i) { return x(i); })
		.y(function(d) { return -1 * y(d); });
	g.append("path").attr("d", line(data));
</script>
<?php }} ?>
