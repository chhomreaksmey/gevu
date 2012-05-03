﻿var w = 960,
    h = 600,
    r = Math.min(w, h) / 2,
	z,
	path, titre;
var x = d3.scale.linear().range([0, 2 * Math.PI]),
	y = d3.scale.pow().exponent(1.3).domain([0, 1]).range([0, r]),
	p = 5,
	duration = 1000;
var text;

//merci Ã 
//http://www.colorhexa.com 
//http://vis.stanford.edu/color-names/analyzer/ number-6
var colors = [];
colors['BL'] = ["#ccdef0", "#1e4164"];
colors['CA'] = ["#c9e1c4", "#2a4724"];
colors['CV'] = ["#f5d5ad", "#5e390b"];
colors['MR'] = ["#f1c4c5", "#621819"];
colors['QS'] = ["#dcc1df", "#412343"];

	  
var vis = d3.select("#chart").append("svg")
    .attr("width", w)
    .attr("height", h)
  .append("g")
  	.attr("id","gVis")
    .attr("transform", "translate(" + w / 2 + "," + h / 2 + ")");

var partition = d3.layout.partition() //fct qui permet de calculer les donnees par rapport Ã  un tableau
    .sort(null)
    .size([2 * Math.PI, r * r])
    .value(function(d) { 
    	return d.nb; 
    	});

var arc = d3.svg.arc() //crï¿½er arc qui utilise modï¿½le de d3.
    .startAngle(function(d) { return d.x; }) //d.x---> rï¿½sultat du json par la partition.nodes
    .endAngle(function(d) { return d.x + d.dx; })
    .innerRadius(function(d) { return Math.sqrt(d.y); })
    .outerRadius(function(d) { return Math.sqrt(d.y + d.dy); });

var wL = 300, hL = 300;
var visLeg = d3.select("#legende").append("svg")
	.attr("width", wL)
	.attr("height", hL)
	.attr("viewBox", "0 0 800 800")
	.attr("preserveAspectRatio", "xMidYMid meet")
	.append("g")
		.attr("id","gLeg")
		.attr("transform", "translate(" + wL + "," + hL + ")");
		
		
function getTypeLog(typeLog){
	//merci à http://www.jasondavies.com/coffee-wheel/	
	d3.json("http://www.gevu.org/public/stat/antenne?type=ArbreTypeLog&typeLog="+typeLog, function(json) {
		//suprime l'ancien graphique
		if(path)path.remove();
		if(text)text.remove();
		
		//construction des Ã©chelles de couleurs
		var arrA = json.children;
		z = [];
		for(var i=0; i < arrA.length; i++){
			z[arrA[i].ref]=d3.scale.log().domain([arrA[i].min, arrA[i].nb]).range(colors[arrA[i].ref]);
		}		
		var nodes = partition.nodes(json);
		
		path = vis.selectAll("path") //crï¿½er visualisation, ajoute des donnees, crï¿½er pr chaque ï¿½lement path(ligne)
		      .data(nodes) //ajoute nouvelle data (fonction qui permet de transformer les donnees en un modï¿½le de donnees partition qui n'est pas trier)
		    .enter().append("path") //pr chaque valeur de data, on ajoute un path.
		      .attr("display", function(d) { return d.depth ? null : "blue"; }) // ds () nb attribut, valeur attribut. d=id; d.depth=profndeur du noeud pr l'id. {}=if si d.depth=null sinn bleu
		      .attr("d", arc) //.attr=attribut. Dis ï¿½ d3 comment il doit construire le code du path qu'il doit faire
		      .attr("fill-rule", "evenodd")
		      .style("stroke", "#fff") //sroke=contour de la forme
		      .on("click", click)
		      .attr("id", function(d, i) { 
		    	  return "path-" + i; 
		    	  })
		      .style("fill", function(d) {
		    	  var i = (d.children ? d : d.parent);
		    	  if(i.ref){
		    		  var color = z[i.ref]; 
		    		  return color(i.nb);		    		  
		    	  }else 
		    		  return "white";
		    	  }) //si d.children renvoit trop alors d, sinon d.parent. fill ds spï¿½cification du svg = remplissage.
		      ;
		    	//  .each(stash);
			  

		  titre = path.append("svg:title")
		  .text(function(d) { 
		  	return d.name + " : " + d.value; 
		  });
		  
		  text = vis.selectAll("g").data(nodes);

		var textEnter = text.enter().append("svg:g")
	    .attr("fill", "navy")
	    .append("text")
	    	.attr("class", "txtLeg")
	    	.attr("font-size", "10")
	    	.style("fill", "white")
	    	.append("textPath")
	         	.attr("xlink:href", function(d, i) { 
				  	return "#" + "path-" + i; 
	         	})
			  	.text(function(d) { 
				  	return d.name; 
			  	});	
	
	
			function click(d) {
			    path.transition()
			      .duration(duration)
			      .attrTween("d", arcTween(d));
			
			    /* Somewhat of a hack as we rely on arcTween updating the scales.
			    text.style("visibility", function(e) {
			        return isParentOf(d, e) ? null : d3.select(this).style("visibility");
			      })
			      .transition().duration(duration)
			      .attrTween("text-anchor", function(d) {
			        return function() {
			          return x(d.x + d.dx / 2) > Math.PI ? "end" : "start";
			        };
			      })
			      .attrTween("transform", function(d) {
			        var multiline = (d.name || "").split(" ").length > 1;
			        return function() {
			          var angle = x(d.x + d.dx / 2) * 180 / Math.PI - 90,
			              rotate = angle + (multiline ? -.5 : 0);
			          return "rotate(" + rotate + ")translate(" + (y(d.y) + p) + ")rotate(" + (angle > 90 ? -180 : 0) + ")";
			        };
			      })
			      .style("opacity", function(e) { return isParentOf(d, e) ? 1 : 1e-6; })
			      .each("end", function(e) {
			        d3.select(this).style("visibility", isParentOf(d, e) ? null : "hidden");
			      });
			      */
			      
			  }	  
	});
}

function getLegende(){
			
	var dataCircle = {"id":"0","name":"Alc\u00e9ane","children":[{"id":"1","name":"Antenne","nb":1,"children":[{"id":"2","name":"Groupe","nb":1,"children":[{"id":"3","name":"Batiment","nb":1,"children":[{"id":"4","name":"Type logement","nb":"1"}]}]}],"max":"1","min":"1"}],"nb":1};
	var dataTexte = [{"id":"0","name":"Alc\u00e9ane"},{"id":"1","name":"Antennes"},{"id":"2","name":"Groupes"},{"id":"3","name":"Bâtiments"},{"id":"4","name":"Type logement"}];
	var dataAntenne = [{"id":"1","ref":"BL","name":"Antenne - BL"},{"id":"2","ref":"CA","name":"Antenne - CA"},{"id":"3","ref":"CV","name":"Antenne - CV"},{"id":"4","ref":"MR","name":"Antenne - MR"},{"id":"5","ref":"QS","name":"Antenne - QS"}];
			
	var antenneLeg = visLeg.selectAll("text").data(dataAntenne).enter()
	.append("text")
	   .attr("transform", "translate(0,-300)")
       .attr("class", "txtLeg")
       .attr("font-size", "30")
       .attr("font-weight", "bold")
       .attr("fill", function(d) { 
    	   return colors[d.ref][1]; 
    	   })
       .attr("x", "320")
       .attr("y", function(d) { 
    	   return 100*d.id; 
	   })
       .text(function(d) { 
    	   return d.name; 
	   });
	
	var pathLeg = visLeg.data([dataCircle]).selectAll("path") 
	      .data(partition.nodes) 
	    .enter().append("path") 
	      .attr("display", function(d) { return d.depth ? null : "blue"; }) 
	      .attr("d", arc)
	      .attr("id", function(d) { return d.id; })
	      .attr("fill-rule", "evenodd")
	      .style("stroke", "#fff") 
	      .style("fill", "#CCCCCC")
	      .each(stash)
	      ;

	var textLeg = visLeg.selectAll("g").data(dataTexte).enter().append("svg:g")
	    .attr("fill", "navy")
	    .attr("transform", "rotate(164)")
	    .append("text")
	    	.attr("transform", "translate(0,-30)")
	    	.attr("class", "txtLeg")
	    	.attr("font-size", "30")
	    	.append("textPath")
	         	.attr("xlink:href", function(d) { 
				  	return "#" + d.id; 
	         	})
			  	.text(function(d) { 
				  	return d.name; 
			  	});	
}
	
	

// Stash the old values for transition.
function stash(d) {
  d.x0 = d.x;
  d.dx0 = d.dx;
}


function isParentOf(p, c) {
  if (p === c) return true;
  if (p.children) {
    return p.children.some(function(d) {
      return isParentOf(d, c);
    });
  }
  return false;
}

//Interpolate the scales!
function arcTween(d) {
  var my = maxY(d),
      xd = d3.interpolate(x.domain(), [d.x, d.x + d.dx]),
      yd = d3.interpolate(y.domain(), [d.y, my]),
      yr = d3.interpolate(y.range(), [d.y ? 20 : 0, r]);
  return function(d) {
    return function(t) { x.domain(xd(t)); y.domain(yd(t)).range(yr(t)); return arc(d); };
  };
}

function maxY(d) {
  return d.children ? Math.max.apply(Math, d.children.map(maxY)) : d.y + d.dy;
}