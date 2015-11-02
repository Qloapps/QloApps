/** 
 * Copyright (c) 2006, David Spurr (http://www.defusion.org.uk/)
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 * 
 *     * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 *     * Neither the name of the David Spurr nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * http://www.opensource.org/licenses/bsd-license.php
 * 
 * See scriptaculous.js for full scriptaculous licence
 */

var CropDraggable=Class.create();
Object.extend(Object.extend(CropDraggable.prototype,Draggable.prototype),{initialize:function(_1){
this.options=Object.extend({drawMethod:function(){
}},arguments[1]||{});
this.element=$(_1);
this.handle=this.element;
this.delta=this.currentDelta();
this.dragging=false;
this.eventMouseDown=this.initDrag.bindAsEventListener(this);
Event.observe(this.handle,"mousedown",this.eventMouseDown);
Draggables.register(this);
},draw:function(_2){
var _3=Position.cumulativeOffset(this.element);
var d=this.currentDelta();
_3[0]-=d[0];
_3[1]-=d[1];
var p=[0,1].map(function(i){
return (_2[i]-_3[i]-this.offset[i]);
}.bind(this));
this.options.drawMethod(p);
}});
var Cropper={};
Cropper.Img=Class.create();
Cropper.Img.prototype={initialize:function(_7,_8){
this.options=Object.extend({ratioDim:{x:0,y:0},minWidth:0,minHeight:0,displayOnInit:false,onEndCrop:Prototype.emptyFunction,captureKeys:true,onloadCoords:null,maxWidth:0,maxHeight:0},_8||{});
this.img=$(_7);
this.clickCoords={x:0,y:0};
this.dragging=false;
this.resizing=false;
this.isWebKit=/Konqueror|Safari|KHTML/.test(navigator.userAgent);
this.isIE=/MSIE/.test(navigator.userAgent);
this.isOpera8=/Opera\s[1-8]/.test(navigator.userAgent);
this.ratioX=0;
this.ratioY=0;
this.attached=false;
this.fixedWidth=(this.options.maxWidth>0&&(this.options.minWidth>=this.options.maxWidth));
this.fixedHeight=(this.options.maxHeight>0&&(this.options.minHeight>=this.options.maxHeight));
if(typeof this.img=="undefined"){
return;
}
$A(document.getElementsByTagName("script")).each(function(s){
if(s.src.match(/cropper\.js/)){
var _a=s.src.replace(/cropper\.js(.*)?/,"");
var _b=document.createElement("link");
_b.rel="stylesheet";
_b.type="text/css";
_b.href=_a+"cropper.css";
_b.media="screen";
document.getElementsByTagName("head")[0].appendChild(_b);
}
});
if(this.options.ratioDim.x>0&&this.options.ratioDim.y>0){
var _c=this.getGCD(this.options.ratioDim.x,this.options.ratioDim.y);
this.ratioX=this.options.ratioDim.x/_c;
this.ratioY=this.options.ratioDim.y/_c;
}
this.subInitialize();
if(this.img.complete||this.isWebKit){
this.onLoad();
}else{
Event.observe(this.img,"load",this.onLoad.bindAsEventListener(this));
}
},getGCD:function(a,b){
if(b==0){
return a;
}
return this.getGCD(b,a%b);
},onLoad:function(){
var _f="imgCrop_";
var _10=this.img.parentNode;
var _11="";
if(this.isOpera8){
_11=" opera8";
}
this.imgWrap=Builder.node("div",{"class":_f+"wrap"+_11});
this.north=Builder.node("div",{"class":_f+"overlay "+_f+"north"},[Builder.node("span")]);
this.east=Builder.node("div",{"class":_f+"overlay "+_f+"east"},[Builder.node("span")]);
this.south=Builder.node("div",{"class":_f+"overlay "+_f+"south"},[Builder.node("span")]);
this.west=Builder.node("div",{"class":_f+"overlay "+_f+"west"},[Builder.node("span")]);
var _12=[this.north,this.east,this.south,this.west];
this.dragArea=Builder.node("div",{"class":_f+"dragArea"},_12);
this.handleN=Builder.node("div",{"class":_f+"handle "+_f+"handleN"});
this.handleNE=Builder.node("div",{"class":_f+"handle "+_f+"handleNE"});
this.handleE=Builder.node("div",{"class":_f+"handle "+_f+"handleE"});
this.handleSE=Builder.node("div",{"class":_f+"handle "+_f+"handleSE"});
this.handleS=Builder.node("div",{"class":_f+"handle "+_f+"handleS"});
this.handleSW=Builder.node("div",{"class":_f+"handle "+_f+"handleSW"});
this.handleW=Builder.node("div",{"class":_f+"handle "+_f+"handleW"});
this.handleNW=Builder.node("div",{"class":_f+"handle "+_f+"handleNW"});
this.selArea=Builder.node("div",{"class":_f+"selArea"},[Builder.node("div",{"class":_f+"marqueeHoriz "+_f+"marqueeNorth"},[Builder.node("span")]),Builder.node("div",{"class":_f+"marqueeVert "+_f+"marqueeEast"},[Builder.node("span")]),Builder.node("div",{"class":_f+"marqueeHoriz "+_f+"marqueeSouth"},[Builder.node("span")]),Builder.node("div",{"class":_f+"marqueeVert "+_f+"marqueeWest"},[Builder.node("span")]),this.handleN,this.handleNE,this.handleE,this.handleSE,this.handleS,this.handleSW,this.handleW,this.handleNW,Builder.node("div",{"class":_f+"clickArea"})]);
this.imgWrap.appendChild(this.img);
this.imgWrap.appendChild(this.dragArea);
this.dragArea.appendChild(this.selArea);
this.dragArea.appendChild(Builder.node("div",{"class":_f+"clickArea"}));
_10.appendChild(this.imgWrap);
this.startDragBind=this.startDrag.bindAsEventListener(this);
Event.observe(this.dragArea,"mousedown",this.startDragBind);
this.onDragBind=this.onDrag.bindAsEventListener(this);
Event.observe(document,"mousemove",this.onDragBind);
this.endCropBind=this.endCrop.bindAsEventListener(this);
Event.observe(document,"mouseup",this.endCropBind);
this.resizeBind=this.startResize.bindAsEventListener(this);
this.handles=[this.handleN,this.handleNE,this.handleE,this.handleSE,this.handleS,this.handleSW,this.handleW,this.handleNW];
this.registerHandles(true);
if(this.options.captureKeys){
this.keysBind=this.handleKeys.bindAsEventListener(this);
Event.observe(document,"keypress",this.keysBind);
}
new CropDraggable(this.selArea,{drawMethod:this.moveArea.bindAsEventListener(this)});
this.setParams();
},registerHandles:function(_13){
for(var i=0;i<this.handles.length;i++){
var _15=$(this.handles[i]);
if(_13){
var _16=false;
if(this.fixedWidth&&this.fixedHeight){
_16=true;
}else{
if(this.fixedWidth||this.fixedHeight){
var _17=_15.className.match(/([S|N][E|W])$/);
var _18=_15.className.match(/(E|W)$/);
var _19=_15.className.match(/(N|S)$/);
if(_17){
_16=true;
}else{
if(this.fixedWidth&&_18){
_16=true;
}else{
if(this.fixedHeight&&_19){
_16=true;
}
}
}
}
}
if(_16){
_15.hide();
}else{
Event.observe(_15,"mousedown",this.resizeBind);
}
}else{
_15.show();
Event.stopObserving(_15,"mousedown",this.resizeBind);
}
}
},setParams:function(){
this.imgW=this.img.width;
this.imgH=this.img.height;
$(this.north).setStyle({height:0});
$(this.east).setStyle({width:0,height:0});
$(this.south).setStyle({height:0});
$(this.west).setStyle({width:0,height:0});
$(this.imgWrap).setStyle({"width":this.imgW+"px","height":this.imgH+"px"});
$(this.selArea).hide();
var _1a={x1:0,y1:0,x2:0,y2:0};
var _1b=false;
if(this.options.onloadCoords!=null){
_1a=this.cloneCoords(this.options.onloadCoords);
_1b=true;
}else{
if(this.options.ratioDim.x>0&&this.options.ratioDim.y>0){
_1a.x1=Math.ceil((this.imgW-this.options.ratioDim.x)/2);
_1a.y1=Math.ceil((this.imgH-this.options.ratioDim.y)/2);
_1a.x2=_1a.x1+this.options.ratioDim.x;
_1a.y2=_1a.y1+this.options.ratioDim.y;
_1b=true;
}
}
//this.setAreaCoords(_1a,false,false,1);
if(this.options.displayOnInit&&_1b){
this.selArea.show();
this.drawArea();
this.endCrop();
}
this.attached=true;
},remove:function(){
if(this.attached){
this.attached=false;
this.imgWrap.parentNode.insertBefore(this.img,this.imgWrap);
this.imgWrap.parentNode.removeChild(this.imgWrap);
Event.stopObserving(this.dragArea,"mousedown",this.startDragBind);
Event.stopObserving(document,"mousemove",this.onDragBind);
Event.stopObserving(document,"mouseup",this.endCropBind);
this.registerHandles(false);
if(this.options.captureKeys){
Event.stopObserving(document,"keypress",this.keysBind);
}
}
},
reset:function(minWidth, minHeight, maxWidth, maxHeight)
{
	if(!this.attached){
		this.onLoad();
	}else{
		this.setParams();
	}
	this.endCrop();
	this.options.minWidth = minWidth;
	this.options.minHeight = minHeight;
	this.options.maxWidth = maxWidth;
	this.options.maxHeight = maxHeight;
},handleKeys:function(e){
var dir={x:0,y:0};
if(!this.dragging){
switch(e.keyCode){
case (37):
dir.x=-1;
break;
case (38):
dir.y=-1;
break;
case (39):
dir.x=1;
break;
case (40):
dir.y=1;
break;
}
if(dir.x!=0||dir.y!=0){
if(e.shiftKey){
dir.x*=10;
dir.y*=10;
}
this.moveArea([this.areaCoords.x1+dir.x,this.areaCoords.y1+dir.y]);
Event.stop(e);
}
}
},calcW:function(){
return (this.areaCoords.x2-this.areaCoords.x1);
},calcH:function(){
return (this.areaCoords.y2-this.areaCoords.y1);
},moveArea:function(_1e){
this.setAreaCoords({x1:_1e[0],y1:_1e[1],x2:_1e[0]+this.calcW(),y2:_1e[1]+this.calcH()},true,false);
this.drawArea();
},cloneCoords:function(_1f){
return {x1:_1f.x1,y1:_1f.y1,x2:_1f.x2,y2:_1f.y2};
},setAreaCoords:function(_20,_21,_22,_23,_24){
if(_21){
var _25=_20.x2-_20.x1;
var _26=_20.y2-_20.y1;
if(_20.x1<0){
_20.x1=0;
_20.x2=_25;
}
if(_20.y1<0){
_20.y1=0;
_20.y2=_26;
}
if(_20.x2>this.imgW){
_20.x2=this.imgW;
_20.x1=this.imgW-_25;
}
if(_20.y2>this.imgH){
_20.y2=this.imgH;
_20.y1=this.imgH-_26;
}
}else{
if(_20.x1<0){
_20.x1=0;
}
if(_20.y1<0){
_20.y1=0;
}
if(_20.x2>this.imgW){
_20.x2=this.imgW;
}
if(_20.y2>this.imgH){
_20.y2=this.imgH;
}
if(_23!=null){
if(this.ratioX>0){
this.applyRatio(_20,{x:this.ratioX,y:this.ratioY},_23,_24);
}else{
if(_22){
this.applyRatio(_20,{x:1,y:1},_23,_24);
}
}
var _27=[this.options.minWidth,this.options.minHeight];
var _28=[this.options.maxWidth,this.options.maxHeight];
if(_27[0]>0||_27[1]>0||_28[0]>0||_28[1]>0){
var _29={a1:_20.x1,a2:_20.x2};
var _2a={a1:_20.y1,a2:_20.y2};
var _2b={min:0,max:this.imgW};
var _2c={min:0,max:this.imgH};
if((_27[0]!=0||_27[1]!=0)&&_22){
if(_27[0]>0){
_27[1]=_27[0];
}else{
if(_27[1]>0){
_27[0]=_27[1];
}
}
}
if((_28[0]!=0||_28[0]!=0)&&_22){
if(_28[0]>0&&_28[0]<=_28[1]){
_28[1]=_28[0];
}else{
if(_28[1]>0&&_28[1]<=_28[0]){
_28[0]=_28[1];
}
}
}
if(_27[0]>0){
this.applyDimRestriction(_29,_27[0],_23.x,_2b,"min");
}
if(_27[1]>1){
this.applyDimRestriction(_2a,_27[1],_23.y,_2c,"min");
}
if(_28[0]>0){
this.applyDimRestriction(_29,_28[0],_23.x,_2b,"max");
}
if(_28[1]>1){
this.applyDimRestriction(_2a,_28[1],_23.y,_2c,"max");
}
_20={x1:_29.a1,y1:_2a.a1,x2:_29.a2,y2:_2a.a2};
}
}
}
this.areaCoords=_20;
},applyDimRestriction:function(_2d,val,_2f,_30,_31){
var _32;
if(_31=="min"){
_32=((_2d.a2-_2d.a1)<val);
}else{
_32=((_2d.a2-_2d.a1)>val);
}
if(_32){
if(_2f==1){
_2d.a2=_2d.a1+val;
}else{
_2d.a1=_2d.a2-val;
}
if(_2d.a1<_30.min){
_2d.a1=_30.min;
_2d.a2=val;
}else{
if(_2d.a2>_30.max){
_2d.a1=_30.max-val;
_2d.a2=_30.max;
}
}
}
},applyRatio:function(_33,_34,_35,_36){
var _37;
if(_36=="N"||_36=="S"){
_37=this.applyRatioToAxis({a1:_33.y1,b1:_33.x1,a2:_33.y2,b2:_33.x2},{a:_34.y,b:_34.x},{a:_35.y,b:_35.x},{min:0,max:this.imgW});
_33.x1=_37.b1;
_33.y1=_37.a1;
_33.x2=_37.b2;
_33.y2=_37.a2;
}else{
_37=this.applyRatioToAxis({a1:_33.x1,b1:_33.y1,a2:_33.x2,b2:_33.y2},{a:_34.x,b:_34.y},{a:_35.x,b:_35.y},{min:0,max:this.imgH});
_33.x1=_37.a1;
_33.y1=_37.b1;
_33.x2=_37.a2;
_33.y2=_37.b2;
}
},applyRatioToAxis:function(_38,_39,_3a,_3b){
var _3c=Object.extend(_38,{});
var _3d=_3c.a2-_3c.a1;
var _3e=Math.floor(_3d*_39.b/_39.a);
var _3f;
var _40;
var _41=null;
if(_3a.b==1){
_3f=_3c.b1+_3e;
if(_3f>_3b.max){
_3f=_3b.max;
_41=_3f-_3c.b1;
}
_3c.b2=_3f;
}else{
_3f=_3c.b2-_3e;
if(_3f<_3b.min){
_3f=_3b.min;
_41=_3f+_3c.b2;
}
_3c.b1=_3f;
}
if(_41!=null){
_40=Math.floor(_41*_39.a/_39.b);
if(_3a.a==1){
_3c.a2=_3c.a1+_40;
}else{
_3c.a1=_3c.a1=_3c.a2-_40;
}
}
return _3c;
},drawArea:function(){
var _42=this.calcW();
var _43=this.calcH();
var px="px";
var _45=[this.areaCoords.x1+px,this.areaCoords.y1+px,_42+px,_43+px,this.areaCoords.x2+px,this.areaCoords.y2+px,(this.img.width-this.areaCoords.x2)+px,(this.img.height-this.areaCoords.y2)+px];
var _46=this.selArea.style;
_46.left=_45[0];
_46.top=_45[1];
_46.width=_45[2];
_46.height=_45[3];
var _47=Math.ceil((_42-6)/2)+px;
var _48=Math.ceil((_43-6)/2)+px;
this.handleN.style.left=_47;
this.handleE.style.top=_48;
this.handleS.style.left=_47;
this.handleW.style.top=_48;
this.north.style.height=_45[1];
var _49=this.east.style;
_49.top=_45[1];
_49.height=_45[3];
_49.left=_45[4];
_49.width=_45[6];
var _4a=this.south.style;
_4a.top=_45[5];
_4a.height=_45[7];
var _4b=this.west.style;
_4b.top=_45[1];
_4b.height=_45[3];
_4b.width=_45[0];
this.subDrawArea();
this.forceReRender();
},forceReRender:function(){
if(this.isIE||this.isWebKit){
var n=document.createTextNode(" ");
var d,el,fixEL,i;
if(this.isIE){
fixEl=this.selArea;
}else{
if(this.isWebKit){
fixEl=document.getElementsByClassName("imgCrop_marqueeSouth",this.imgWrap)[0];
d=Builder.node("div","");
d.style.visibility="hidden";
var _4e=["SE","S","SW"];
for(i=0;i<_4e.length;i++){
el=document.getElementsByClassName("imgCrop_handle"+_4e[i],this.selArea)[0];
if(el.childNodes.length){
el.removeChild(el.childNodes[0]);
}
el.appendChild(d);
}
}
}
fixEl.appendChild(n);
fixEl.removeChild(n);
}
},startResize:function(e){
this.startCoords=this.cloneCoords(this.areaCoords);
this.resizing=true;
this.resizeHandle=Event.element(e).classNames().toString().replace(/([^N|NE|E|SE|S|SW|W|NW])+/,"");
Event.stop(e);
},startDrag:function(e){
this.selArea.show();
this.clickCoords=this.getCurPos(e);
this.setAreaCoords({x1:this.clickCoords.x,y1:this.clickCoords.y,x2:this.clickCoords.x,y2:this.clickCoords.y},false,false,null);
this.dragging=true;
this.onDrag(e);
Event.stop(e);
},getCurPos:function(e){
var el=this.imgWrap,wrapOffsets=Position.cumulativeOffset(el);
while(el.nodeName!="BODY"){
wrapOffsets[1]-=el.scrollTop||0;
wrapOffsets[0]-=el.scrollLeft||0;
el=el.parentNode;
}
return curPos={x:Event.pointerX(e)-wrapOffsets[0],y:Event.pointerY(e)-wrapOffsets[1]};
},onDrag:function(e){
if(this.dragging||this.resizing){
var _54=null;
var _55=this.getCurPos(e);
var _56=this.cloneCoords(this.areaCoords);
var _57={x:1,y:1};
if(this.dragging){
if(_55.x<this.clickCoords.x){
_57.x=-1;
}
if(_55.y<this.clickCoords.y){
_57.y=-1;
}
this.transformCoords(_55.x,this.clickCoords.x,_56,"x");
this.transformCoords(_55.y,this.clickCoords.y,_56,"y");
}else{
if(this.resizing){
_54=this.resizeHandle;
if(_54.match(/E/)){
this.transformCoords(_55.x,this.startCoords.x1,_56,"x");
if(_55.x<this.startCoords.x1){
_57.x=-1;
}
}else{
if(_54.match(/W/)){
this.transformCoords(_55.x,this.startCoords.x2,_56,"x");
if(_55.x<this.startCoords.x2){
_57.x=-1;
}
}
}
if(_54.match(/N/)){
this.transformCoords(_55.y,this.startCoords.y2,_56,"y");
if(_55.y<this.startCoords.y2){
_57.y=-1;
}
}else{
if(_54.match(/S/)){
this.transformCoords(_55.y,this.startCoords.y1,_56,"y");
if(_55.y<this.startCoords.y1){
_57.y=-1;
}
}
}
}
}
this.setAreaCoords(_56,false,e.shiftKey,_57,_54);
this.drawArea();
Event.stop(e);
}
},transformCoords:function(_58,_59,_5a,_5b){
var _5c=[_58,_59];
if(_58>_59){
_5c.reverse();
}
_5a[_5b+"1"]=_5c[0];
_5a[_5b+"2"]=_5c[1];
},endCrop:function(){
this.dragging=false;
this.resizing=false;
this.options.onEndCrop(this.areaCoords,{width:this.calcW(),height:this.calcH()});
},subInitialize:function(){
},subDrawArea:function(){
}};
Cropper.ImgWithPreview=Class.create();
Object.extend(Object.extend(Cropper.ImgWithPreview.prototype,Cropper.Img.prototype),{subInitialize:function(){
this.hasPreviewImg=false;
if(typeof (this.options.previewWrap)!="undefined"&&this.options.minWidth>0&&this.options.minHeight>0){
this.previewWrap=$(this.options.previewWrap);
this.previewImg=this.img.cloneNode(false);
this.previewImg.id="imgCrop_"+this.previewImg.id;
this.options.displayOnInit=true;
this.hasPreviewImg=true;
this.previewWrap.addClassName("imgCrop_previewWrap");
this.previewWrap.setStyle({width:this.options.minWidth+"px",height:this.options.minHeight+"px"});
this.previewWrap.appendChild(this.previewImg);
}
},subDrawArea:function(){
if(this.hasPreviewImg){
var _5d=this.calcW();
var _5e=this.calcH();
var _5f={x:this.imgW/_5d,y:this.imgH/_5e};
var _60={x:_5d/this.options.minWidth,y:_5e/this.options.minHeight};
var _61={w:Math.ceil(this.options.minWidth*_5f.x)+"px",h:Math.ceil(this.options.minHeight*_5f.y)+"px",x:"-"+Math.ceil(this.areaCoords.x1/_60.x)+"px",y:"-"+Math.ceil(this.areaCoords.y1/_60.y)+"px"};
var _62=this.previewImg.style;
_62.width=_61.w;
_62.height=_61.h;
_62.left=_61.x;
_62.top=_61.y;
}
}});

