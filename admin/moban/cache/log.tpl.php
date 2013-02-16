<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<link rel="stylesheet" href="<? echo $this->img; ?>js/cal.css" />
<script src="<? echo $this->img; ?>js/cal.js" type="text/javascript"></script>

<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./log.htm">管理操作日志</a></div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<table cellpadding="2" cellspacing="1" class="table">
	<tr>
		<td class="centle" colspan="9" style="font-weight:normal;">
			<div style="float:left;">&nbsp;&nbsp;
			<form method="get" action="./log.htm">
			时间从
			<input name="startday" type="text" value="<?=$startday?>" size="12" class="text jsdate">
			到
			<input name="endday" type="text" value="<?=$endday?>" size="12" class="text jsdate">
			，关键字：
			<input name="key" type="text" id="key" value="" class="text">
			<select name="show" id="show">
				<option value="0">-- 可选 --</option>
				<option value="1">用户名</option>
				<option value="2">登陆IP</option>
				<option value="3">动作名称</option>
			</select>
			<input type="submit" id="submit12" class="submitmi" style="padding:2px;" value="搜索">
			</form>
			</div>
		</td>
	</tr>
	<tr>
		<td width="60" class="list" style="text-align:center;">ID</td>
		<td class="list" style="padding:0px; text-align:center;">操作者</td>
		<td class="list" style="padding:0px; text-align:center;">IP</td>
		<td class="list" style="padding:0px; text-align:center;">操作时间</td>
		<td class="list" style="padding:0px; text-align:center;">动作</td>
		<td class="list" style="padding:0px; text-align:center;">操作对象</td>
		<td class="list" style="padding:0px; text-align:center;">频道</td>
		<td class="list" style="padding:0px; text-align:center;">删除</td>
	</tr><? if(is_array($list)) { foreach($list as $v) { ?>	<tr class="mouse click">
		<td class="list-text"><?=$v['id']?></td>
		<td class="list-text"><a><?=$v['username']?></a></td>
		<td class="list-text"><?=$v['ip']?></td>
		<td class="list-text"><?=$v['dateline']?></td>
		<td class="list-text"><?=$v['actionname']?></td>
		<td class="list-text" style="text-align:left;padding-left:12px;"><?=$v['content']?></td>
		<td class="list-text"><?=$v['path']?></td>
		<td class="list-text"><input name="checkbox[]" type='checkbox' value="<?=$v['id']?>"/> &nbsp;&nbsp;<a href="./log.htm?action=del&amp;id=<?=$v['id']?>" onclick="return confirm('确认要删除吗?');">删除</a></td>
	</tr>
	<? } } ?><tr>
		<td class="all-submit" colspan="7" style="padding:5px 10px;">
			<input name="submit" type='submit' value='批量删除' class="submit li-submit" onclick="del();"/> 
			<form method="post" action="./log.htm?action=deltime">
			 // 删除从
			<input name="startday" type="text" class="text med jsdate" value="" size="12" style="height:18px;line-height:18px;">
			到
			<input name="endday" type="text" class="text med jsdate" value="" size="12" style="height:18px;line-height:18px;">
			之间的日志
			<input type="submit" name="Submit2" value="提交" class="submitmi" style="padding:2px;">
			</form>
		</td>
		<td class="all"><input name="chkAll" type="checkbox" id="chkAll" value="checkbox" onclick="selectAll($(this));"> &nbsp;&nbsp;选择</td>
	</tr>
	<? if($page) { ?>
	<tr>
		<td class="page_list" colspan="8" style="padding:5px 0px;"><?=$page?></td>
	</tr>
	<? } ?>	
</table>

<script>
function selectAll(e){
	var v = e.attr("checked") ? true : false;
	$("input[name='checkbox[]']").attr("checked",v);
}
function get_value(){
	var v = [];
	$("input[name='checkbox[]']:checked").each(function(){
		v.push($(this).val());
	});
	return v;
}
function del(){
	if (confirm('确认要彻底删除吗?')){
		var val = get_value();
		if (val){
			$.post('./log.htm',{action:'batch_del',values:val},function(data){
				var obj = eval('(' + data + ')');
				showmessage(obj.type, obj.info, obj.url);
			});
		}
	}
}
$(function(){
	$('.jsdate').simpleDatepicker();
});
</script>
<? include Lua::display('_foot',$this->dir); ?>
