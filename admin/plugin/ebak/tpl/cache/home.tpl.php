<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./plugin.htm">插件管理</a> > <a href="./plugin.htm?action=ebak">数据备份</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="doit(0,0,0,0,0);"><font color="red">点击立即备份:)</font></a></div>
</div>
<div class="clear"></div>

<div id="ing" class="return_success" style="display:none;"></div>

<div class="luabox" id="luabox">
	<ul class="columnlist"><? if(is_array($list)) { foreach($list as $v) { ?>		<li class="contlist">
			<div class="box">
				<div class="img" style="text-align:center;"><a href="javascript:;" onclick="if (confirm('确认要还原吗?')){ importit('<?=$v['name']?>', 0, 0); }" title="数据还原至<?=$v['name']?>"><img src="<? echo $this->img; ?>img/icon/zip.png"  width="64" height="64"/></a></div>
				<h2 class="title"><a href="javascript:;" onclick="if (confirm('确认要还原吗?')){ importit('<?=$v['name']?>', 0, 0); }" title="数据还原至<?=$v['name']?>"><?=$v['name']?></a></h2>
				<div class="text">
					<a href="./plugin.htm?action=ebak&amp;c=down&amp;dir=<?=$v['name']?>">下载</a>
					<a href="./plugin.htm?action=ebak&amp;c=del&amp;dir=<?=$v['name']?>" onclick="return confirm('确认要删除吗?');">删除</a>
					<div class="clear" style="height:2px;"></div>
				</div>
			</div>
		</li>
		<? } } ?></ul>
</div>

<script>
function importit(dir, t, p){
	$.post('./plugin.htm?action=ebak&c=import',{dir:dir,t:t,p:p},function(data){
		$('#ing').show();
		$('#luabox').hide();
		if (data == 'success'){
			$('#ing').html('还原成功');
			setTimeout(function(){
				$('#ing').hide();
				$('#luabox').show();
				location.reload();
			},2000);
		}else{
			var obj = eval('(' + data + ')');
			$('#ing').html(obj.info);
			importit(obj.dir, obj.t, obj.p);
		}
	});
}

function doit(s,p,t,alltotal,fnum){
	$.post('./plugin.htm?action=ebak&c=doit',{s:s,p:p,t:t,alltotal:alltotal,fnum:fnum},function(data){
		$('#ing').show();
		$('#luabox').hide();
		if (data == 'success'){
			$('#ing').html('备份成功');
			setTimeout(function(){
				$('#ing').hide();
				$('#luabox').show();
				location.reload();
			},2000);
		}else{
			var obj = eval('(' + data + ')');
			$('#ing').html(obj.info);
			doit(obj.s, obj.p, obj.t, obj.alltotal, obj.fnum);
		}
	});
}
</script>
<? include Lua::display('_foot',$this->dir); ?>
