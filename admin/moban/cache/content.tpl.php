<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <? if($this->lua == 'piece') { ?><a href="./piece.htm">碎片管理</a><? } else { ?><a href="./category.htm">栏目管理</a><? } ?> > <a href="./content.htm?catid=<?php echo isset($catid) ? $catid : "";?><? echo $this->lua_url;; ?>"><? echo $this->cate_db['name'];; ?></a> <? if($db) { ?>> <a href="./content.htm?catid=<?=$catid?>&amp;tableid=<?=$tableid?>&amp;tid=<?php echo isset($tid) ? $tid : "";?><? echo $this->lua_url;; ?>"><? echo $this->mode_db['modelname'];; ?></a> > <font color="blue"><u><?=$db['subject']?></u></font><? } ?></div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<div class="stat_list" style="padding-left:24px;margin:0px;">
	<ul>
		<? if($father) { ?>
		<li><a href="./content.htm?catid=<?php echo isset($father['id']) ? $father['id'] : "";?><? echo $this->lua_url;; ?>"><?=$father['name']?> ↑</a></li>
		<? } if(is_array($Ftree)) { foreach($Ftree as $v) { ?>			<li class="<? if($v['id']==$catid) { ?>now<? } ?>"><a href="./content.htm?catid=<?php echo isset($v['id']) ? $v['id'] : "";?><? echo $this->lua_url;; ?>"><?=$v['name']?></a></li>
		<? } } if(is_array($nav)) { foreach($nav as $v) { ?>			<li class="<? if($v['id']==$tableid) { ?>now<? } ?>"><a href="./content.htm?catid=<?=$catid?>&amp;tableid=<?=$v['id']?>&amp;tid=<?php echo isset($tid) ? $tid : "";?><? echo $this->lua_url;; ?>"><?=$v['modelname']?><? if($v['id'] == $tableid) { ?> √<? } ?></a></li>
		<? } } ?></ul>
</div>
<div style="clear:both;"></div>

<table cellpadding="2" cellspacing="1" class="table">
	<tr>
		<td colspan="20" class="centle" style=" height:20px; line-height:30px; font-weight:normal; padding-left:10px;">
			<div style="float:left;"><? if(is_array($Stree)) { foreach($Stree as $v) { ?>				<a href="./content.htm?catid=<?=$v['id']?>" style="text-decoration:none;"><b>[<?php echo isset($v['name']) ? $v['name'] : "";?>]</b></a>&nbsp;&nbsp;
				<? } } if($this->cate_db['add_perm'] == 1) { ?>
				<a href="./content.htm?action=add&amp;catid=<?=$catid?><?php echo isset($suffix) ? $suffix : "";?><? echo $this->lua_url;; ?>"><font color="red">[+]添加内容</font></a>&nbsp;&nbsp;
				<? } ?>
				<? if($isdel == 1) { ?>
				<a href="./content.htm?catid=<?=$catid?><?php echo isset($suffix) ? $suffix : "";?><? echo $this->lua_url;; ?>"><font color="gray">返回列表</font></a>&nbsp;&nbsp;
				<? } else { ?>
				<a href="./content.htm?action=recycle&amp;catid=<?=$catid?><?php echo isset($suffix) ? $suffix : "";?><? echo $this->lua_url;; ?>"><font color="gray">回收站</font></a>&nbsp;&nbsp;
				<? } ?>
			</div>
			<div class="formright">
				<form method="get" action="./content.htm">
				<input type="hidden" name="catid" value="<?=$catid?>"/>
				<? if($isdel == 1) { ?>
				<input type="hidden" name="action" value="recycle"/>
				<? } ?>
				<? if($tableid) { ?>
				<input type="hidden" name="tableid" value="<?=$tableid?>"/>
				<input type="hidden" name="tid" value="<?=$tid?>"/>
				<? } ?>
				<? if($this->lua == 'piece') { ?>
				<input type="hidden" name="lua" value="piece"/>
				<? } ?>
				&nbsp;推荐
				<select name="commend" id="commend">
					<option value="">---</option><? if(is_array($range)) { foreach($range as $v) { ?>					<option value="<?=$v?>"><?=$v?></option>
					<? } } ?></select>
				&nbsp;置顶
				<select name="topped" id="topped">
					<option value="">---</option><? if(is_array($range)) { foreach($range as $v) { ?>					<option value="<?=$v?>"><?=$v?></option>
					<? } } ?></select>
				<input name="txt" type="text" class="text" id="searchtext" value="请输入信息标题关键字。" onclick="this.value='';"/>
				<input type="submit" name="" value="搜索" class="submitmi" style="padding:2px;"/>
				</form>
			</div>
		</td>
	</tr>
	<tr id="list-top">
		<td width="40" class="list" style="padding:0px; text-align:center;">选择</td>
		<td width="80" class="list" style="padding:0px; text-align:center;">ID</td>
		<td width="60" class="list" style="padding:0px; text-align:center;">排序</td>
		<td class="list">标题</td><? if(is_array($fields)) { foreach($fields as $v) { ?>		<td class="list"><?=$v['name']?></td>
		<? } } ?><td width="50" style="padding:0px; text-align:center;" class="list" >推荐</td>
		<td width="50" style="padding:0px; text-align:center;" class="list" >置顶</td>
		<td width="100" class="list">发布时间</td>
		<td width="60" style="padding:0px; text-align:center;" class="list">用户</td>
		<td width="60" class="list">操作</td>
	</tr><? if(is_array($list)) { foreach($list as $v) { ?>	<tr class="mouse click" id="tr_<?=$v['id']?>">
		<td class="list-text"><input name="checkbox[]" type='checkbox' value="<?=$v['id']?>"/></td>
		<td class="list-text"><?=$v['id']?></td>
		<td class="list-text"><input type="text" value="<?=$v['vieworder']?>" name="order_new[<?php echo isset($v['id']) ? $v['id'] : "";?>]" class="text no_order" /></td>
		<td class="list-text" style="text-align:left;">&nbsp;&nbsp;<? if($this->cate_db['add_perm'] == 0) { ?><a href="./content.htm?catid=<?php echo isset($v['catid']) ? $v['catid'] : "";?><? echo $this->lua_url;; ?>">[<? echo $catidb[$v['catid']]['name'];; ?>]</a> <? } ?><?=$v['subject']?><? if(isset($v['picurl']) && $v['picurl']) { ?>&nbsp;<font color="green">(图)</font><? } ?></td><? if(is_array($fields)) { foreach($fields as $e) { ?>		<td class="list-text"><? echo $v[$e['fieldname']];; ?></td>
		<? } } ?><td class="list-text"><? if($v['commend']) { ?><?=$v['commend']?><? } else { ?>--<? } ?></td>
		<td class="list-text"><? if($v['topped']) { ?><?=$v['topped']?><? } else { ?>--<? } ?></td>
		<td class="list-text"><? echo date('Y-m-d H:i',$v['dateline']);; ?></td>
		<td class="list-text"><?=$v['username']?></td>
		<td class="list-text">
			<div style="position:relative;">
				<a href="./content.htm?action=edit&amp;catid=<?=$v['catid']?>&amp;id=<?=$v['id']?><?php echo isset($suffix) ? $suffix : "";?><? echo $this->lua_url;; ?>" style="float:left; margin-left:10px;">修改</a>
				<div class="columnmore">
					<span class="text">更多&nbsp;<img src="<? echo $this->img;; ?>img/column12.gif" style="position:relative; bottom:2px;" /></span>
					<div class="none columnmorediv">
						<? if($mods) { if(is_array($mods)) { foreach($mods as $m) { ?>							<div><a href="./content.htm?catid=<?=$v['catid']?>&amp;tableid=<?=$m['id']?>&amp;tid=<?php echo isset($v['id']) ? $v['id'] : "";?><? echo $this->lua_url;; ?>"><?=$m['modelname']?></a></div>
							<? } } } else { ?>
						<div><a href="javascript:;">--无--</a></div>
						<? } ?>
					</div>
				</div>
			</div>
		</td>
	</tr>
	<? } } if($this->cate_db['add_perm'] == 1) { ?>
	<tr>
		<td class="all"><input name="chkAll" type="checkbox" id="chkAll" value="checkbox" onclick="selectAll($(this));"></td>
		<td class="all-submit" colspan="19" style="padding:5px 10px;">
			<? if($isdel) { ?>
			<input name="submit" type='submit' value='还原' class="submit li-submit" onclick="undo();"/> 
			<? } else { ?>
			<input name="submit" type='submit' value='移除' class="submit li-submit" onclick="recycle();"/> 
			<? } ?>
			<input name="submit" type='submit' value='排序' class="submit li-submit" onclick="vieworder();"/>
			<input name="submit" type='submit' value='删除' class="submit li-submit" onclick="del();"/> 
			<? if(empty($db)) { ?>
			<div class="li-submit">
				<select name="copy" onchange="option(this.value,'copyit');">
					<option value="">复制至...</option><? if(is_array($same)) { foreach($same as $v) { ?>					<option value="<?=$v['id']?>"><?=$v['name']?></option>
					<? } } ?></select>
			</div>
			<div class="li-submit">
				<select name="move" onchange="option(this.value,'moveit');">
					<option value="">移动至...</option><? if(is_array($same)) { foreach($same as $v) { ?>					<option value="<?=$v['id']?>"><?=$v['name']?></option>
					<? } } ?></select>
			</div>
			<? } ?>
			<div class="li-submit">
				<select name="move" onchange="option(this.value,'commendit');">
					<option value="">推荐至...</option>
					<option value="0">取消</option><? if(is_array($range)) { foreach($range as $v) { ?>					<option value="<?=$v?>"><?=$v?></option>
					<? } } ?></select>
			</div>
			<div class="li-submit">
				<select name="move" onchange="option(this.value,'toppedit');">
					<option value="">置顶至...</option>
					<option value="0">取消</option><? if(is_array($range)) { foreach($range as $v) { ?>					<option value="<?=$v?>"><?=$v?></option>
					<? } } ?></select>
			</div>
		</td>
	</tr>
	<? } ?>
	<? if($page) { ?>
	<tr>
		<td class="page_list" colspan="20" style="padding:5px 0px;"><?=$page?></td>
	</tr>
	<? } ?>
</table>
<? if($this->cate_db['add_perm'] == 1) { ?>
<script>
function selectAll(e){
	var v = e.attr("checked") ? true : false;
	$("input[name='checkbox[]']").attr("checked",v);
}
function option(v, action){
	var val = get_value();
	if (val){
		if (v){
			$.post('./content.htm?love=1<?php echo isset($suffix) ? $suffix : "";?><? echo $this->lua_url;; ?>',{action:action,catid:v,values:val,trueid:'<?=$catid?>'},function(data){
				var obj = eval('(' + data + ')');
				showmessage(obj.type, obj.info, obj.url);
			});
		}
	}
}

function del(){
	if (confirm('确认要彻底删除吗?')){
		var val = get_value();
		if (val){
			$.post('./content.htm?love=1<?php echo isset($suffix) ? $suffix : "";?><? echo $this->lua_url;; ?>',{action:'del',values:val,catid:'<?=$catid?>',del:<?=$isdel?>},function(data){
				var obj = eval('(' + data + ')');
				showmessage(obj.type, obj.info, obj.url);
			});
		}
	}
}

function vieworder(){
	var a = $('.no_order').fieldSerialize();
	if (a){
		$.post('./content.htm?love=1<?php echo isset($suffix) ? $suffix : "";?><? echo $this->lua_url;; ?>',{action:'orderit',values:a,catid:'<?=$catid?>'},function(data){
			var obj = eval('(' + data + ')');
			showmessage(obj.type, obj.info, obj.url);
		});
	}
}

function recycle(){
	if (confirm('确认要移除至回收站吗?')){
		option(<?=$catid?>,'recycleit');
	}
}

function undo(){
	option(<?=$catid?>,'undoit');
}

function get_value(){
	var v = [];
	$("input[name='checkbox[]']:checked").each(function(){
		v.push($(this).val());
	});
	return v;
}
</script>
<? } ?>
<script>
$(function(){
	$('.columnmore').hover(
		function () {
			$(this).find('span.text').addClass("columnmore_hover");
			$(this).find('div.columnmorediv').show();
		},
		function () {
			$(this).find('span.text').removeClass("columnmore_hover");
			$(this).find('div.columnmorediv').hide();
		}
	);
});
</script>
<? include Lua::display('_foot',$this->dir); ?>
