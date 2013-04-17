/**
* info
*/
function showmessage(type,info,url){
	url = url == null ? '' : url;
	if (type == 'success' && url){
		window.scrollTo(0,0);
		$('#showmessage').show();
		$('#showmessage').addClass("return_"+type);
		info = info + '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' + url + '" style="font-size:12px;font-weight:normal;color:gray;">3秒后自动跳转, 点击跳转 ...</a>';
		$('input[type="submit"]').attr('disabled','true');
		$('#showmessage').html(info);
		window.setTimeout(function () {
			location.href = url;
		},3000);
	}else{
		alert('Lua: ' + info);
	}
}

/**
* ajax post 
*/
function post(url,formname){
	edit = edit == null ? '' : edit;
	if (edit == 2){
		$('#' + id_name).val(ue.getContent());	
	}
	formname = formname == null ? 'myform' : formname;
	var queryString = $('#' + formname).formSerialize();
	$.post(url, queryString, function(data){
		var obj = eval('(' + data + ')');
		showmessage(obj.type, obj.info, obj.url);
		//document.write(data);
	});
	return false;
}

/**
* upfile
*/
function upfile(id, type, ureturn) {
	var hash = $('#hash').val();
	$(id).wrap("<div class='file_uploadfrom'></div>");
	$(id).parent().wrap("<div class='lua_upfile'></div>");
	$(id).parent().after("<a href='javascript:;' title='浏览上传' class='upbutn round'>浏览上传</a>");
	var img_url = swfurl + 'static/swf/upfile/uploadify.swf';
	var depths = phpurl + 'admin/file.htm';
	$(id).uploadify({
		'uploader': img_url,
		'script': depths,
		'hideButton': true,
		'auto': true,
		'height': '25',
		'scriptData':{action:type,hash:hash},
		'onComplete': function(event, queueId, fileObj, response, data) {
			uponComplete(response, ureturn);
		},
		'onProgress': function(event, ID, fileObj, data) {
			$("input[type='submit']").attr("disabled", true);
			return false;
		}
	});
	$("div.file_uploadfrom").css("opacity", "0");
}

/**
* 上传完成后
*/
function uponComplete(response, ureturn){
	var res = response.split('@');
	var text = res[0] == 1 ? '上传成功' : response;
	alert(text);
	$("input[type='submit']").removeAttr('disabled');
	if (res[0] == 1){
		$("input[name='" + ureturn + "']").val(res[1]);
	}
}

/**
* 查找关联数据
*/
function so_relate(id, model_id, v, mode){
	$.post(phpurl + 'admin/content.htm?action=so_relate',{model_id:model_id, value:v, id:id, mode:mode},function(result){
		var a = $('#div_'+id);
		a.show();
		a.html(result);
	});
}

/**
* 查找结果中选择
*/
function so_select_it(div, id, subject, mode){
	if (mode == 0){
		// 单关联
		$('#div_'+div).hide();
		$("input[name='"+div+"']").val(id);
		$('#so_'+div).val(subject);
	}else if (mode == 1){
		// 多关联
		var oldv = $("input[name='"+div+"']").val();
		var newv = oldv + ',' + id;
		$("input[name='"+div+"']").val(newv);
		var model_id = $('#model_'+div).val();
		so_value(div, model_id, newv, 1);
	}
}

/**
* 搜索点击时清空结果
*/
function so_default(id, mode){
	$('#so_'+id).val('');
	if (mode == 0){
		$("input[name='"+id+"']").val('');
	}
}

/**
* 编辑内容时默认显示标题
*/
function so_value(div, model_id, id, mode){
	$.post(phpurl + 'admin/content.htm?action=so_value',{model_id:model_id, value:id, id:div, mode:mode},function(result){
		if (mode == 0){
			$('#div_'+div).hide();
			$('#so_'+div).val(result);
		}else if (mode == 1){
			$('#value_'+div).html(result);
		}
	});
}

/**
* 多关联模式下移除某值
*/
function so_delete(values, id, div, model_id){
	$.get(phpurl + 'admin/content.htm?action=so_delete',{v1:values, v2:id},function(result){
		so_value(div, model_id, result, 1);
		$("input[name='"+div+"']").val(result);
	});
}

/**
* 颜色选择
*/
function ColorSel(c){
	$('#subject').css('color',c);
	$('#color_table').hide();
	$('#color').val(c);
	return true;
}

/**
* alert
*/
function echo(str) {
	LAYER.open('<div style="margin:20px;">'+str+'</div>', "友情提示");
}

jQuery.fn.drag = function(id){
	return this.each(function(){
		var draging = false;
		var startLeft,startTop;
		var startX,startY;

		$(this).css('cursor','move');
		$(this).mousedown(function(event){
			var o = id ? document.getElementById(id) : this;
			if(!event) event = window.event;
	        if (o.setCapture) {
				o.setCapture();
			} else if (window.captureEvents) {
				window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);
			}
			
			var offset = $(o).offset();
			startLeft = offset.left;
			var posval = $(o).css('position');
			startTop = offset.top - (($(o).css('position') == 'fixed') ? $(window).scrollTop() : 0);
			startX = event.clientX;
			startY = event.clientY;
		
			$(document).bind('mousemove',function(event){
				var deltaX = event.clientX - startX;
				var deltaY = event.clientY - startY;
				var left = startLeft + deltaX;
				var top = startTop + deltaY;
				$(o).css({left:left+'px',top:top+'px'});
			});
			$(document).bind('mouseup',function(event){
				if(o.releaseCapture) {
					o.releaseCapture();
				} else if(window.captureEvents) {
					window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP);
				}
				$(document).unbind('mouseup');
				$(document).unbind('mousemove');
			});
		});
	});
};

var LAYER = {
	width:null,
	open:function() {
		if(!$("#layer").length) {
			$(document.body).append('<div id="layerMarsk"></div><div id="layer"><h4><span></span><a href="javascript:void(0)" class="btn_close"></a></h4><div id="layerHtml"></div></div>');
		}
		var html = arguments[0] ? arguments[0] : '';
		var title = arguments[1] ? arguments[1] : '';
		this.width = arguments[2] ? arguments[2] : 600;
		if(html) {
			$("#layerHtml").html(html);
			$("#layer h4 span").html(title);
			$("#layer h4").drag('layer');
		}
		$("#layer h4 a").click(this.close);
		this.show();
	},
	close:function() {
		$("#layerHtml").html('');
		$("#layer").fadeOut("fast");
		$("#layerMarsk").fadeOut("fast");		
	},
	show:function() {
		var owidth = $(window).width();
		var oheight = $(document).height();
		var mtop = ($(window).height() - $("#layer").height()) / 2;
		$("#layer").css({
			left  : ((owidth - this.width) / 2) + "px",
			top   : (mtop > 0 ? mtop : 0) + "px",
			width : this.width + "px"
		});
		$("#layer").css("width", this.width+"px");
		$("#layerMarsk").css({width:owidth+"px", height:oheight+"px",opacity:'0.6',zIndex:99});
		$("#layerMarsk").fadeIn("fast");
		$("#layer").fadeIn("fast");
	}
};